<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\CompanySetting;
use App\Models\Company;

class RazorpayService
{
    private string $keyId;
    private string $keySecret;
    private string $baseUrl;
    private int $companyId;

    public function __construct(int $companyId = null)
    {
        $this->companyId = $companyId ?? $this->getCurrentCompanyId();
        $multiTenantService = new MultiTenantRazorpayService();
        $credentials = $multiTenantService->getCredentialsForCompany($this->companyId);
        
        $this->keyId = $credentials['key_id'];
        $this->keySecret = $credentials['key_secret'];
        $this->baseUrl = 'https://api.razorpay.com/v1';
    }

    /**
     * Create a single payout to vendor
     */
    public function createPayout(array $data): array
    {
        $endpoint = $this->baseUrl . '/payouts';
        
        $payload = [
            'account_number' => $data['account_number'],
            'fund_account' => [
                'account_number' => $data['account_number'],
                'name' => $data['beneficiary_name'],
                'ifsc' => $data['ifsc_code'],
                'contact' => $data['contact_id'] ?? null,
            ],
            'amount' => $data['amount'] * 100, // Convert to paise
            'currency' => 'INR',
            'mode' => 'IMPS',
            'purpose' => 'payout',
            'queue_if_low_balance' => true,
            'reference_id' => $data['reference_id'] ?? null,
            'narration' => $data['narration'] ?? 'Vendor Payment',
            'notes' => $data['notes'] ?? [],
        ];

        return $this->makeRequest('POST', $endpoint, $payload);
    }

    /**
     * Create multiple payouts in batch
     */
    public function createBatchPayouts(array $payouts): array
    {
        $results = [];
        $batchId = 'batch_' . time();

        foreach ($payouts as $index => $payout) {
            $payout['reference_id'] = ($payout['reference_id'] ?? $batchId) . '_' . $index;
            
            try {
                $result = $this->createPayout($payout);
                $results[] = [
                    'success' => true,
                    'data' => $result,
                    'payout' => $payout,
                ];
            } catch (\Exception $e) {
                Log::error('Payout creation failed', [
                    'error' => $e->getMessage(),
                    'payout' => $payout,
                ]);
                
                $results[] = [
                    'success' => false,
                    'error' => $e->getMessage(),
                    'payout' => $payout,
                ];
            }

            // Rate limiting: 1 request per second
            if ($index < count($payouts) - 1) {
                usleep(1000000); // 1 second delay
            }
        }

        return $results;
    }

    /**
     * Get payout status
     */
    public function getPayoutStatus(string $payoutId): array
    {
        $endpoint = $this->baseUrl . '/payouts/' . $payoutId;
        return $this->makeRequest('GET', $endpoint);
    }

    /**
     * Get account balance
     */
    public function getBalance(): array
    {
        $endpoint = $this->baseUrl . '/balance';
        return $this->makeRequest('GET', $endpoint);
    }

    /**
     * Validate webhook signature
     */
    public function validateWebhookSignature(array $payload, string $signature): bool
    {
        $webhookSecret = $this->getWebhookSecret();
        
        if (!$webhookSecret) {
            Log::error('Webhook secret not configured for company', ['company_id' => $this->companyId]);
            return false;
        }

        $expectedSignature = hash_hmac('sha256', json_encode($payload), $webhookSecret);
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Make HTTP request to Razorpay API
     */
    private function makeRequest(string $method, string $endpoint, array $data = []): array
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode($this->keyId . ':' . $this->keySecret),
        ];

        $response = Http::withHeaders($headers)->$method($endpoint, $data);

        if (!$response->successful()) {
            Log::error('Razorpay API request failed', [
                'endpoint' => $endpoint,
                'method' => $method,
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            throw new \Exception('Razorpay API Error: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Get Razorpay credentials for company
     */
    private function getCredentials(int $companyId): array
    {
        $cacheKey = "razorpay_credentials_{$companyId}";
        
        return Cache::remember($cacheKey, 3600, function () use ($companyId) {
            $setting = CompanySetting::where('company_id', $companyId)->first();
            
            if (!$setting || !$setting->razorpay_key_id || !$setting->razorpay_key_secret) {
                throw new \Exception("Razorpay credentials not configured for company {$companyId}");
            }

            return [
                'key_id' => $setting->razorpay_key_id,
                'key_secret' => $setting->razorpay_key_secret,
            ];
        });
    }

    /**
     * Get webhook secret for company
     */
    private function getWebhookSecret(): ?string
    {
        $setting = CompanySetting::where('company_id', $this->companyId)->first();
        return $setting?->razorpay_webhook_secret;
    }

    /**
     * Get current company ID from context
     */
    private function getCurrentCompanyId(): int
    {
        // Try to get from current authenticated user
        if (\Illuminate\Support\Facades\Auth::check()) {
            $user = \Illuminate\Support\Facades\Auth::user();
            $companies = $user->companies;
            if ($companies->isNotEmpty()) {
                return $companies->first()->id;
            }
        }

        // Fallback to default company
        return Company::first()?->id ?? 1;
    }

    /**
     * Check if sufficient balance is available
     */
    public function hasSufficientBalance(float $requiredAmount): bool
    {
        try {
            $balance = $this->getBalance();
            $availableBalance = $balance['balance'] ?? 0;
            
            // Convert from paise to rupees
            $availableBalance /= 100;
            
            return $availableBalance >= $requiredAmount;
        } catch (\Exception $e) {
            Log::error('Failed to check balance', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Create contact for vendor
     */
    public function createContact(array $data): array
    {
        $endpoint = $this->baseUrl . '/contacts';
        return $this->makeRequest('POST', $endpoint, $data);
    }

    /**
     * Create fund account for vendor
     */
    public function createFundAccount(array $data): array
    {
        $endpoint = $this->baseUrl . '/fund_accounts';
        return $this->makeRequest('POST', $endpoint, $data);
    }

    /**
     * Get daily payout limits
     */
    public function getDailyLimits(): array
    {
        $endpoint = $this->baseUrl . '/payouts/limits';
        return $this->makeRequest('GET', $endpoint);
    }
}
