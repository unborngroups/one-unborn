<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PurchaseInvoice;
use App\Models\Vendor;
use App\Models\EmailLog;
use Carbon\Carbon;

class PurchaseInvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample email logs first
        $emailLog1 = EmailLog::create([
            'sender' => 'vendor1@example.com',
            'subject' => 'Invoice #INV-2024-001 from Tech Solutions Pvt Ltd',
            'body' => 'Please find attached invoice for your reference.',
            'attachment_path' => 'images/poinvoice_files/sample_invoice_1.pdf',
            'status' => 'processed',
            'file_hash' => hash('sha256', 'sample1'),
        ]);

        $emailLog2 = EmailLog::create([
            'sender' => 'vendor2@example.com',
            'subject' => 'Invoice #INV-2024-002 from Digital Services Ltd',
            'body' => 'Monthly invoice for services rendered.',
            'attachment_path' => 'images/poinvoice_files/sample_invoice_2.pdf',
            'status' => 'processed',
            'file_hash' => hash('sha256', 'sample2'),
        ]);

        $emailLog3 = EmailLog::create([
            'sender' => 'vendor3@example.com',
            'subject' => 'Invoice #INV-2024-003 from Hardware Suppliers',
            'body' => 'Purchase invoice for equipment.',
            'attachment_path' => 'images/poinvoice_files/sample_invoice_3.pdf',
            'status' => 'processed',
            'file_hash' => hash('sha256', 'sample3'),
        ]);

        // Create sample purchase invoices with different statuses
        PurchaseInvoice::create([
            'type' => 'purchase',
            'vendor_name' => 'Tech Solutions Pvt Ltd',
            'vendor_name_raw' => 'Tech Solutions Pvt Ltd',
            'invoice_no' => 'INV-2024-001',
            'invoice_number' => 'INV-2024-001',
            'invoice_date' => Carbon::now()->subDays(15)->toDateString(),
            'due_date' => Carbon::now()->subDays(10)->toDateString(),
            'total_amount' => 15000.00,
            'tax_amount' => 2700.00,
            'grand_total' => 17700.00,
            'net_amount' => 17700.00,
            'status' => 'needs_review',
            'gstin' => '33AAHCI0166K1ZM',
            'gst_number' => '33AAHCI0166K1ZM',
            'vendor_gstin' => '33AAHCI0166K1ZM',
            'confidence_score' => 85.50,
            'email_log_id' => $emailLog1->id,
            'raw_json' => [
                'vendor_name' => 'Tech Solutions Pvt Ltd',
                'invoice_number' => 'INV-2024-001',
                'total' => 17700.00,
                'gst' => '33AAHCI0166K1ZM',
                'matching' => [
                    'combined_confidence' => 85.50
                ]
            ],
            'created_at' => Carbon::now()->subMinutes(30),
            'updated_at' => Carbon::now()->subMinutes(30),
        ]);

        PurchaseInvoice::create([
            'type' => 'purchase',
            'vendor_name' => 'Digital Services Ltd',
            'vendor_name_raw' => 'Digital Services Ltd',
            'invoice_no' => 'INV-2024-002',
            'invoice_number' => 'INV-2024-002',
            'invoice_date' => Carbon::now()->subDays(10)->toDateString(),
            'due_date' => Carbon::now()->subDays(5)->toDateString(),
            'total_amount' => 8500.00,
            'tax_amount' => 1530.00,
            'grand_total' => 10030.00,
            'net_amount' => 10030.00,
            'status' => 'verified',
            'gstin' => '27AAAPL7188G1ZV',
            'gst_number' => '27AAAPL7188G1ZV',
            'vendor_gstin' => '27AAAPL7188G1ZV',
            'confidence_score' => 92.75,
            'email_log_id' => $emailLog2->id,
            'raw_json' => [
                'vendor_name' => 'Digital Services Ltd',
                'invoice_number' => 'INV-2024-002',
                'total' => 10030.00,
                'gst' => '27AAAPL7188G1ZV',
                'matching' => [
                    'combined_confidence' => 92.75
                ]
            ],
            'created_at' => Carbon::now()->subMinutes(20),
            'updated_at' => Carbon::now()->subMinutes(20),
        ]);

        PurchaseInvoice::create([
            'type' => 'purchase',
            'vendor_name' => 'Hardware Suppliers',
            'vendor_name_raw' => 'Hardware Suppliers',
            'invoice_no' => 'GMAIL-d967ded74d',
            'invoice_number' => null,
            'invoice_date' => Carbon::now()->subDays(5)->toDateString(),
            'due_date' => Carbon::now()->addDays(5)->toDateString(),
            'total_amount' => 25000.00,
            'tax_amount' => 4500.00,
            'grand_total' => 29500.00,
            'net_amount' => 29500.00,
            'status' => 'draft',
            'gstin' => '19AAACI1680G1C0',
            'gst_number' => '19AAACI1680G1C0',
            'vendor_gstin' => '19AAACI1680G1C0',
            'confidence_score' => 78.25,
            'email_log_id' => $emailLog3->id,
            'raw_json' => [
                'vendor_name' => 'Hardware Suppliers',
                'invoice_number' => null,
                'total' => 29500.00,
                'gst' => '19AAACI1680G1C0',
                'matching' => [
                    'combined_confidence' => 78.25
                ]
            ],
            'created_at' => Carbon::now()->subMinutes(10),
            'updated_at' => Carbon::now()->subMinutes(10),
        ]);

        PurchaseInvoice::create([
            'type' => 'purchase',
            'vendor_name' => 'Office Equipment Mart',
            'vendor_name_raw' => 'Office Equipment Mart',
            'invoice_no' => 'INV-2024-003',
            'invoice_number' => 'INV-2024-003',
            'invoice_date' => Carbon::now()->subDays(2)->toDateString(),
            'due_date' => Carbon::now()->addDays(8)->toDateString(),
            'total_amount' => 12000.00,
            'tax_amount' => 2160.00,
            'grand_total' => 14160.00,
            'net_amount' => 14160.00,
            'status' => 'approved',
            'gstin' => '29AABCM7710C1ZY',
            'gst_number' => '29AABCM7710C1ZY',
            'vendor_gstin' => '29AABCM7710C1ZY',
            'confidence_score' => 95.00,
            'email_log_id' => $emailLog1->id,
            'raw_json' => [
                'vendor_name' => 'Office Equipment Mart',
                'invoice_number' => 'INV-2024-003',
                'total' => 14160.00,
                'gst' => '29AABCM7710C1ZY',
                'matching' => [
                    'combined_confidence' => 95.00
                ]
            ],
            'created_at' => Carbon::now()->subMinutes(5),
            'updated_at' => Carbon::now()->subMinutes(5),
        ]);

        $this->command->info('Sample purchase invoices created successfully!');
    }
}
