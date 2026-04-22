<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\FinancialYear;
use App\Models\Client;
use App\Models\Vendor;

class PrefixConfiguration extends Model
{
    protected $fillable = [
        'document_type',
        'prefix_base',
        'prefix_format',
        'sequence_format',
        'sequence_length',
        'current_sequence',
        'reset_yearly',
        'reset_monthly',
        'is_active',
        'description'
    ];

    protected $casts = [
        'reset_yearly' => 'boolean',
        'reset_monthly' => 'boolean',
        'is_active' => 'boolean'
    ];

    /**
     * Get configuration for specific document type and base
     */
    public static function getConfig($documentType, $prefixBase = 'FY')
    {
        return self::where('document_type', $documentType)
                   ->where('prefix_base', $prefixBase)
                   ->where('is_active', true)
                   ->first();
    }

    /**
     * Generate next sequence number for this configuration
     */
    public function getNextSequence()
    {
        $this->increment('current_sequence');
        return str_pad($this->current_sequence, $this->sequence_length, '0', STR_PAD_LEFT);
    }

    /**
     * Generate prefix based on configuration
     */
    public function generatePrefix($clientId = null, $vendorId = null)
    {
        $format = $this->prefix_format;
        
        // Replace placeholders based on prefix base
        switch ($this->prefix_base) {
            case 'FY': // Financial Year Base
                $fy = FinancialYear::getActiveFY();
                $fyYear = $fy ? $fy->getFormattedYear() : date('y');
                $format = str_replace('{FY}', $fyYear, $format);
                break;
                
            case 'VB': // Vendor Base
                if ($vendorId) {
                    $vendor = Vendor::find($vendorId);
                    $vendorCode = $vendor ? ($vendor->code ?? $vendor->vendor_code ?? 'VND') : 'VND';
                    $format = str_replace('{VENDOR_CODE}', $vendorCode, $format);
                }
                break;
                
            case 'CB': // Client Base
                if ($clientId) {
                    $client = Client::find($clientId);
                    $clientCode = $client ? ($client->code ?? $client->client_code ?? 'CLI') : 'CLI';
                    $format = str_replace('{CLIENT_CODE}', $clientCode, $format);
                }
                break;
        }

        // Replace common placeholders that might appear in any format
        $fy = FinancialYear::getActiveFY();
        $fyYear = $fy ? $fy->getFormattedYear() : date('y');
        $format = str_replace('{FY}', $fyYear, $format);
        
        // Replace sequence placeholder
        $sequence = $this->getNextSequence();
        $format = str_replace('{SEQUENCE}', $sequence, $format);
        
        // Replace month/year placeholders
        $format = str_replace('{YYYY}', date('Y'), $format);
        $format = str_replace('{YY}', date('y'), $format);
        $format = str_replace('{MM}', date('m'), $format);
        
        return $format;
    }

    /**
     * Reset sequence based on configuration
     */
    public function resetSequenceIfNeeded()
    {
        $shouldReset = false;
        
        if ($this->reset_yearly) {
            $fy = FinancialYear::getActiveFY();
            if ($fy && !$fy->isDateInRange($this->updated_at)) {
                $shouldReset = true;
            }
        }
        
        if ($this->reset_monthly && $this->updated_at->month !== now()->month) {
            $shouldReset = true;
        }
        
        if ($shouldReset) {
            $this->update(['current_sequence' => 0]);
        }
    }
}
