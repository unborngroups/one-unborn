<?php

namespace App\Services;

use App\Models\Vendor;
use App\Models\VendorLearningLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class VendorResolverService
{
    private ?bool $learningTableAvailable = null;

    public function resolve($invoiceData)
    {
        return $this->resolveMatch($invoiceData)['vendor'];
    }

    public function resolveMatch($invoiceData): array
    {
        if (!is_array($invoiceData)) {
            return $this->emptyMatchResult();
        }

        $gstin = $this->normalizeGstin($invoiceData['gstin'] ?? $invoiceData['gst'] ?? null);
        $vendorName = $this->cleanName($invoiceData['vendor_name'] ?? null);
        $result = $this->emptyMatchResult($gstin, $vendorName);

        // Priority 1: GSTIN learning (strong)
        if ($gstin) {
            $vendor = $this->resolveViaLearnedGSTIN($gstin);
            if ($vendor) {
                Log::info('Vendor resolved via learned GSTIN: ' . $gstin);
                return $this->buildMatchResult($vendor, $gstin, $vendorName, 'learned_gstin', 96);
            }
        }

        // Priority 2: Learned vendor_name mapping
        if ($vendorName) {
            $vendor = $this->resolveViaLearnedName($vendorName);
            if ($vendor) {
                Log::info('Vendor resolved via learned name: ' . $vendorName);
                return $this->buildMatchResult($vendor, $gstin, $vendorName, 'learned_name', $gstin ? 90 : 88);
            }
        }

        // Priority 3: Default resolver (GSTIN > normalized name)
        return $this->resolveDefault($gstin, $vendorName, $result);
    }

    private function resolveViaLearnedGSTIN($gstin)
    {
        if (!$this->isLearningTableAvailable()) {
            return null;
        }

        $learning = VendorLearningLog::where('gstin', $gstin)
            ->where('is_verified', true)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($learning) {
            return Vendor::find($learning->matched_vendor_id);
        }

        return null;
    }

    private function resolveViaLearnedName($vendorName)
    {
        if (!$this->isLearningTableAvailable()) {
            return null;
        }

        $normalizedName = $this->normalize($vendorName);

        $learning = VendorLearningLog::whereRaw(
            "LOWER(REPLACE(REPLACE(REPLACE(vendor_name_raw, ' ', ''), '-', ''), '.', '')) = ?",
            [$normalizedName]
        )
            ->where('is_verified', true)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($learning) {
            return Vendor::find($learning->matched_vendor_id);
        }

        return null;
    }

    private function resolveDefault($gstin, $vendorName, array $fallbackResult)
    {
        // Rule 1: GSTIN match (PRIMARY)
        if ($gstin) {
            $vendor = Vendor::where('gstin', $gstin)->first();
            if ($vendor) {
                Log::info('Vendor resolved via GSTIN: ' . $gstin);
                return $this->buildMatchResult($vendor, $gstin, $vendorName, 'gstin', 94);
            }
        }

        // Rule 2: Normalized vendor name match (SECONDARY)
        if ($vendorName) {
            $normalizedName = $this->normalize($vendorName);
            $vendor = Vendor::whereRaw(
                "LOWER(REPLACE(REPLACE(REPLACE(vendor_name, ' ', ''), '-', ''), '.', '')) = ?",
                [$normalizedName]
            )->first();

            if ($vendor) {
                Log::info('Vendor resolved via normalized name: ' . $vendorName);
                return $this->buildMatchResult($vendor, $gstin, $vendorName, 'normalized_name', $gstin ? 84 : 82);
            }

            $bestMatch = $this->findBestApproximateNameMatch($vendorName);
            if ($bestMatch['vendor']) {
                Log::info('Vendor resolved via approximate name: ' . $vendorName, [
                    'vendor_id' => $bestMatch['vendor']->id,
                    'similarity' => $bestMatch['similarity'],
                ]);

                return $this->buildMatchResult(
                    $bestMatch['vendor'],
                    $gstin,
                    $vendorName,
                    'approximate_name',
                    $gstin ? 78 : max(70, (int) round($bestMatch['similarity'])),
                    $bestMatch['similarity']
                );
            }
        }

        // Not found
        Log::warning('Vendor not resolved for GSTIN: ' . $gstin . ', Name: ' . $vendorName);
        return $fallbackResult;
    }

    public function recordLearning($vendorNameRaw, $gstin, $vendorId, $confidence = 1.0, $isVerified = false)
    {
        if (!$this->isLearningTableAvailable()) {
            return false;
        }

        try {
            VendorLearningLog::create([
                'vendor_name_raw' => $vendorNameRaw,
                'gstin' => $gstin,
                'matched_vendor_id' => $vendorId,
                'confidence' => $confidence,
                'is_verified' => $isVerified
            ]);

            Log::info('Vendor learning recorded: ' . $vendorNameRaw . ' -> ' . $vendorId);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to record vendor learning: ' . $e->getMessage());
            return false;
        }
    }

    public function verifyLearning($vendorLearningId)
    {
        if (!$this->isLearningTableAvailable()) {
            return false;
        }

        try {
            $learning = VendorLearningLog::find($vendorLearningId);
            if ($learning) {
                $learning->update(['is_verified' => true]);
                Log::info('Vendor learning verified: ' . $learning->id);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            Log::error('Failed to verify vendor learning: ' . $e->getMessage());
            return false;
        }
    }

    private function normalize($text)
    {
        if (!$text) {
            return '';
        }

        // Lowercase
        $normalized = strtolower($text);
        
        // Remove spaces
        $normalized = str_replace(' ', '', $normalized);
        
        // Remove special characters (keep only alphanumeric)
        $normalized = preg_replace('/[^a-z0-9]/', '', $normalized);
        
        return $normalized;
    }

    private function isLearningTableAvailable(): bool
    {
        if ($this->learningTableAvailable !== null) {
            return $this->learningTableAvailable;
        }

        try {
            $this->learningTableAvailable = Schema::hasTable('vendor_learning_logs');
        } catch (\Throwable $e) {
            $this->learningTableAvailable = false;
            Log::warning('Vendor learning table availability check failed', ['error' => $e->getMessage()]);
        }

        return $this->learningTableAvailable;
    }

    private function buildMatchResult($vendor, ?string $gstin, ?string $vendorName, string $matchedBy, int $baseScore, ?float $presetSimilarity = null): array
    {
        $nameComparison = $this->compareNames($vendorName, $vendor);
        $gstMatch = $gstin && $vendor && $this->normalizeGstin($vendor->gstin) === $gstin;
        $score = $baseScore;

        if ($vendorName) {
            if ($nameComparison['exact']) {
                $score = min(100, $score + 4);
            } elseif ($nameComparison['similarity'] >= 85) {
                $score = min(100, $score + 2);
            } elseif ($gstMatch) {
                $score = max(75, $score - 8);
            } else {
                $score = max(70, $score - 5);
            }
        }

        return [
            'vendor' => $vendor,
            'score' => max(0, min(100, $score)),
            'matched_by' => $matchedBy,
            'gst_match' => (bool) $gstMatch,
            'name_match' => $nameComparison['exact'],
            'name_similarity' => round($presetSimilarity ?? $nameComparison['similarity'], 2),
            'extracted_vendor_name' => $vendorName,
            'vendor_master_name' => $vendor?->vendor_name,
            'vendor_master_display_name' => $vendor?->business_display_name,
            'gstin' => $gstin,
        ];
    }

    private function compareNames(?string $vendorName, ?Vendor $vendor): array
    {
        if (!$vendorName || !$vendor) {
            return [
                'exact' => false,
                'similarity' => 0,
            ];
        }

        $normalizedIncoming = $this->normalize($vendorName);
        $candidates = array_filter([
            $vendor->vendor_name,
            $vendor->business_display_name,
        ]);

        $bestSimilarity = 0;
        $exact = false;

        foreach ($candidates as $candidate) {
            $normalizedCandidate = $this->normalize($candidate);
            if ($normalizedCandidate === '') {
                continue;
            }

            if ($normalizedCandidate === $normalizedIncoming) {
                $exact = true;
                $bestSimilarity = 100;
                break;
            }

            similar_text($normalizedIncoming, $normalizedCandidate, $similarity);
            $bestSimilarity = max($bestSimilarity, $similarity);
        }

        return [
            'exact' => $exact,
            'similarity' => round($bestSimilarity, 2),
        ];
    }

    private function findBestApproximateNameMatch(string $vendorName): array
    {
        $normalizedName = $this->normalize($vendorName);
        $bestVendor = null;
        $bestSimilarity = 0;

        $vendors = Vendor::query()
            ->select(['id', 'vendor_name', 'business_display_name', 'gstin'])
            ->get();

        foreach ($vendors as $vendor) {
            $comparison = $this->compareNames($vendorName, $vendor);
            if ($comparison['similarity'] > $bestSimilarity) {
                $bestSimilarity = $comparison['similarity'];
                $bestVendor = $vendor;
            }

            if ($comparison['exact']) {
                break;
            }
        }

        if (!$bestVendor || $bestSimilarity < 70 || $normalizedName === '') {
            return ['vendor' => null, 'similarity' => 0];
        }

        return [
            'vendor' => $bestVendor,
            'similarity' => round($bestSimilarity, 2),
        ];
    }

    private function normalizeGstin(?string $gstin): ?string
    {
        $normalized = strtoupper(trim((string) $gstin));
        return $normalized !== '' ? $normalized : null;
    }

    private function cleanName(?string $name): ?string
    {
        $cleaned = trim((string) $name);
        return $cleaned !== '' ? $cleaned : null;
    }

    private function emptyMatchResult(?string $gstin = null, ?string $vendorName = null): array
    {
        return [
            'vendor' => null,
            'score' => 0,
            'matched_by' => null,
            'gst_match' => false,
            'name_match' => false,
            'name_similarity' => 0,
            'extracted_vendor_name' => $vendorName,
            'vendor_master_name' => null,
            'vendor_master_display_name' => null,
            'gstin' => $gstin,
        ];
    }
}

