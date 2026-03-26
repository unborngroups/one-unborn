<?php

namespace App\Services;

use App\Models\PrefixConfiguration;
use App\Models\FinancialYear;

class PrefixGenerator
{
    /**
     * Generate prefix for different document types
     */
    public static function generate($documentType, $prefixBase = 'FY', $options = [])
    {
        $config = PrefixConfiguration::getConfig($documentType, $prefixBase);
        
        if (!$config) {
            // Fallback to default configuration
            return self::getFallbackPrefix($documentType);
        }
        
        // Reset sequence if needed
        $config->resetSequenceIfNeeded();
        
        // Generate prefix with options
        return $config->generatePrefix(
            $options['client_id'] ?? null,
            $options['vendor_id'] ?? null
        );
    }

    /**
     * Generate Purchase Order number
     */
    public static function generatePONumber($vendorId = null)
    {
        // Try vendor-based first, then financial year-based
        if ($vendorId) {
            $prefix = self::generate('PO', 'VB', ['vendor_id' => $vendorId]);
            if ($prefix) return $prefix;
        }
        
        return self::generate('PO', 'FY');
    }

    /**
     * Generate Feasibility Request ID
     */
    public static function generateFeasibilityId($clientId = null)
{
    if ($clientId) {

        // Sync sequence with last record
        $last = \App\Models\Feasibility::orderBy('id','desc')->first();

        if ($last) {

            $parts = explode('/', $last->feasibility_request_id);
            $lastSeq = intval(end($parts));

            $config = PrefixConfiguration::getConfig('Feasibility','CB');

            if ($config && $config->current_sequence < $lastSeq) {
                $config->current_sequence = $lastSeq;
                $config->save();
            }
        }

        $prefix = self::generate('Feasibility','CB',['client_id'=>$clientId]);

        if ($prefix) return $prefix;
    }

    return self::generate('Feasibility','FY');
}

    /**
     * Generate Invoice number
     */
    public static function generateInvoiceNumber($clientId = null)
    {
        if ($clientId) {
            $prefix = self::generate('Invoice', 'CB', ['client_id' => $clientId]);
            if ($prefix) return $prefix;
        }
        
        return self::generate('Invoice', 'FY');
    }

    /**
     * Generate Vendor Code
     */
    public static function generateVendorCode()
    {
        return self::generate('Vendor', 'GN');
    }

    /**
     * Generate Client Code
     */
    public static function generateClientCode()
    {
        return self::generate('Client', 'GN');
    }

    /**
     * Fallback prefix generation for backward compatibility
     */
    private static function getFallbackPrefix($documentType)
    {
        $fy = FinancialYear::getActiveFY();
        $year = $fy ? $fy->getFormattedYear() : date('y');
        
        switch ($documentType) {
            case 'PO':
                return 'PO' . date('Ym') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            case 'Feasibility':
                return 'FR-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            case 'Invoice':
                return 'INV/' . $year . '/' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            case 'Vendor':
                return 'VND' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
            case 'Client':
                return 'CLI' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
            default:
                return strtoupper(substr($documentType, 0, 3)) . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        }
    }

    /**
     * Initialize default configurations
     */
    public static function initializeDefaultConfigs()
    {
        $defaultConfigs = [
            // Purchase Order - Financial Year Based
            [
                'document_type' => 'PO',
                'prefix_base' => 'FY',
                'prefix_format' => 'PO/{FY}/{SEQUENCE}',
                'sequence_format' => '####',
                'sequence_length' => 4,
                'reset_yearly' => true,
                'description' => 'Purchase Order with Financial Year'
            ],
            // Purchase Order - Vendor Based
            [
                'document_type' => 'PO',
                'prefix_base' => 'VB',
                'prefix_format' => 'PO/{VENDOR_CODE}/{FY}/{SEQUENCE}',
                'sequence_format' => '####',
                'sequence_length' => 4,
                'reset_yearly' => true,
                'description' => 'Purchase Order with Vendor Code'
            ],
            // Feasibility - Financial Year Based
            [
                'document_type' => 'Feasibility',
                'prefix_base' => 'FY',
                'prefix_format' => 'FR/{FY}/{SEQUENCE}',
                'sequence_format' => '####',
                'sequence_length' => 4,
                'reset_yearly' => true,
                'description' => 'Feasibility Request with Financial Year'
            ],
            // Feasibility - Client Based
            [
                'document_type' => 'Feasibility',
                'prefix_base' => 'CB',
                'prefix_format' => 'FR/{CLIENT_CODE}/{FY}/{SEQUENCE}',
                'sequence_format' => '####',
                'sequence_length' => 4,
                'reset_yearly' => true,
                'description' => 'Feasibility Request with Client Code'
            ],
            // Invoice - Client Based
            [
                'document_type' => 'Invoice',
                'prefix_base' => 'CB',
                'prefix_format' => 'INV/{CLIENT_CODE}/{FY}/{SEQUENCE}',
                'sequence_format' => '#####',
                'sequence_length' => 5,
                'reset_yearly' => true,
                'description' => 'Invoice with Client Code'
            ],
            // Vendor Code
            [
                'document_type' => 'Vendor',
                'prefix_base' => 'GN',
                'prefix_format' => 'VND{SEQUENCE}',
                'sequence_format' => '###',
                'sequence_length' => 3,
                'reset_yearly' => false,
                'description' => 'Vendor Code Generation'
            ],
            // Client Code
            [
                'document_type' => 'Client',
                'prefix_base' => 'GN',
                'prefix_format' => 'CLI{SEQUENCE}',
                'sequence_format' => '###',
                'sequence_length' => 3,
                'reset_yearly' => false,
                'description' => 'Client Code Generation'
            ]
        ];

        foreach ($defaultConfigs as $config) {
            PrefixConfiguration::updateOrCreate(
                ['document_type' => $config['document_type'], 'prefix_base' => $config['prefix_base']],
                $config
            );
        }
    }
}