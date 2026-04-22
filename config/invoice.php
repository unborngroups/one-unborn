<?php

return [
    /*
    |--------------------------------------------------------------------------
    | OCR Provider Settings
    |--------------------------------------------------------------------------
    |
    | Configure the primary and fallback OCR providers for invoice extraction.
    | Priority order: primary -> secondary -> fallback (regex)
    |
    */

    'ocr' => [
        'primary' => env('OCR_PRIMARY_PROVIDER', 'textract'),
        'secondary' => env('OCR_SECONDARY_PROVIDER', 'google'),
        'fallback' => 'regex',

        'textract' => [
            'enabled' => env('OCR_TEXTRACT_ENABLED', true),
            'confidence' => 0.85,
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
        ],

        'google' => [
            'enabled' => env('OCR_GOOGLE_ENABLED', true),
            'confidence' => 0.80,
            'project_id' => env('GOOGLE_PROJECT_ID'),
            'api_key' => env('GOOGLE_API_KEY'),
            'location' => env('GOOGLE_LOCATION', 'us'),
        ],

        'regex' => [
            'enabled' => true,
            'confidence' => 0.60,
            'timeout' => 5000, // milliseconds
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Confidence Threshold
    |--------------------------------------------------------------------------
    |
    | Invoices with OCR confidence below this threshold require manual review.
    | Range: 0.0 to 1.0
    |
    | Values:
    | - < 0.50: Very poor extraction, always needs review
    | - 0.50-0.70: Moderate coverage, needs review unless vendor verified
    | - 0.70-0.90: Good coverage, can be auto-drafted
    | - > 0.90: Excellent extraction, auto-approved if vendor matched
    |
    */

    'confidence_threshold' => env('INVOICE_CONFIDENCE_THRESHOLD', 0.70),

    'confidence_rules' => [
        'auto_draft' => 0.70,        // Vendor found + confidence >= this = status: draft
        'needs_review' => 0.70,      // Vendor missing OR confidence < this = status: needs_review
        'learning_verified' => 0.95, // User verification creates learning log with this confidence
        'learning_boost' => 0.10,    // Each subsequent verification increases by this amount
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed File Types
    |--------------------------------------------------------------------------
    |
    | MIME types and extensions allowed for invoice upload/processing.
    |
    */

    'allowed_files' => [
        'types' => [
            'application/pdf',
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/tiff',
            'image/webp',
        ],
        'extensions' => ['pdf', 'jpg', 'jpeg', 'png', 'tiff', 'tif', 'webp'],
        'max_size' => 25 * 1024 * 1024, // 25 MB
        'max_pages' => 50,              // For PDF files
    ],

    /*
    |--------------------------------------------------------------------------
    | Regex Fallback Patterns
    |--------------------------------------------------------------------------
    |
    | Regular expressions for extracting invoice fields when OCR fails.
    | Each pattern includes:
    | - pattern: RegEx with named capture group (?P<value>...)
    | - required: Whether field is mandatory
    | - confidence_weight: How much this field contributes to overall confidence
    |
    */

    'regex_patterns' => [
        'vendor_name' => [
            'patterns' => [
                '/(?:from|vendor|bill\s+from|seller)[:\s]+(?P<value>[a-zA-Z0-9\s\&\-\.()]+)/i',
                '/(?P<value>^[A-Z][A-Za-z0-9\s\&\-\.()]{5,50})(?:\s+(?:pvt|ltd|llp|inc|corp))?$/im',
            ],
            'required' => true,
            'confidence_weight' => 0.20,
        ],

        'gstin' => [
            'patterns' => [
                '/(?:gstin|gst|tax\s+id)[:\s]+(?P<value>\d{2}[A-Z]{5}\d{4}[A-Z]{1}[A-Z0-9]{1}[Z]{1}[A-Z0-9]{1})/i',
                '/\b(?P<value>\d{2}[A-Z]{5}\d{4}[A-Z]{1}[A-Z0-9]{1}[Z]{1}[A-Z0-9]{1})\b/',
            ],
            'required' => false,
            'confidence_weight' => 0.25,
            'validator' => 'isValidGSTIN', // Custom validation method
        ],

        'invoice_number' => [
            'patterns' => [
                '/(?:invoice\s+(?:number|no|#)|ref|document)[:\s]+(?P<value>[A-Z0-9\-\/]+)/i',
                '/(?P<value>INV[A-Z0-9\-\/]{5,20})/i',
                '/(?P<value>[A-Z]{2,4}\/[\d]{2,4}\/[\d]{3,6})/i',
            ],
            'required' => true,
            'confidence_weight' => 0.15,
        ],

        'invoice_date' => [
            'patterns' => [
                '/(?:invoice\s+date|date)[:\s]+(?P<value>\d{1,2}[-\/]\d{1,2}[-\/]\d{2,4})/i',
                '/(?:date)[:\s]+(?P<value>\d{4}[-\/]\d{1,2}[-\/]\d{1,2})/i',
                '/(?P<value>\d{1,2}[-\/](?:jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec)[-\/]\d{4})/i',
            ],
            'required' => true,
            'confidence_weight' => 0.15,
            'format' => 'YYYY-MM-DD',
        ],

        'amount' => [
            'patterns' => [
                '/(?:amount|subtotal|quantity|total)[:\s]+(?:rs|₹)?\s*(?P<value>[\d,]+\.?\d*)/i',
                '/(?:sub\s*total|net\s+amount)[:\s]+(?:rs|₹)?\s*(?P<value>[\d,]+\.?\d*)/i',
            ],
            'required' => true,
            'confidence_weight' => 0.15,
            'cleanup' => 'removeCommas', // Remove commas before converting to float
        ],

        'tax' => [
            'patterns' => [
                '/(?:gst|tax|igst|sgst|cgst)[:\s]+(?:rs|₹)?\s*(?P<value>[\d,]+\.?\d*)/i',
                '/(?:tax\s+amount)[:\s]+(?:rs|₹)?\s*(?P<value>[\d,]+\.?\d*)/i',
            ],
            'required' => false,
            'confidence_weight' => 0.10,
            'cleanup' => 'removeCommas',
        ],

        'total' => [
            'patterns' => [
                '/(?:grand\s+total|total\s+amount|net\s+total)[:\s]+(?:rs|₹)?\s*(?P<value>[\d,]+\.?\d*)/i',
                '/(?:total)[:\s]+(?:rs|₹)?\s*(?P<value>[\d,]+\.?\d*)/i',
            ],
            'required' => true,
            'confidence_weight' => 0.10,
            'cleanup' => 'removeCommas',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | GSTIN Validation
    |--------------------------------------------------------------------------
    |
    | Rules for GSTIN validation and verification.
    |
    */

    'gstin_validation' => [
        'enabled' => true,
        'format_check' => true,         // Validate GSTIN format structure
        'surepass_verification' => true, // Verify GSTIN via Surepass API
        'confidence_boost' => 0.15,      // Boost confidence if GSTIN valid
    ],

    /*
    |--------------------------------------------------------------------------
    | Vendor Learning
    |--------------------------------------------------------------------------
    |
    | Configuration for vendor matching learning system.
    |
    */

    'vendor_learning' => [
        'enabled' => true,
        'verify_boost_amount' => 0.10, // Increase confidence on each user verification
        'max_confidence' => 1.0,
        'priority_rules' => [
            'learned_gstin' => 1,   // Priority 1: GSTIN from verified learning log
            'learned_name' => 2,    // Priority 2: Vendor name from verified learning log
            'default_gstin' => 3,   // Priority 3: GSTIN from vendors table
            'default_name' => 4,    // Priority 4: Normalized name from vendors table
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Processing
    |--------------------------------------------------------------------------
    |
    | Email webhook and attachment handling settings.
    |
    */

    'email' => [
        'webhook_secret' => env('EMAIL_WEBHOOK_SECRET'),
        'attachment_path' => 'invoices/',
        'delete_after_days' => 90,       // Delete old files after 90 days
        'mailgun_domain' => env('MAILGUN_DOMAIN'),
        'mailgun_secret' => env('MAILGUN_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Processing
    |--------------------------------------------------------------------------
    |
    | Invoice processing job configuration.
    |
    */

    'queue' => [
        'connection' => env('QUEUE_CONNECTION', 'redis'),
        'queue' => env('QUEUE_NAME', 'default'),
        'max_attempts' => 3,
        'timeout' => 60,              // seconds
        'retry_after' => 60,          // seconds between retries
    ],

    /*
    |--------------------------------------------------------------------------
    | Duplicate Detection
    |--------------------------------------------------------------------------
    |
    | Rules for identifying duplicate invoices.
    |
    */

    'duplicate_detection' => [
        'enabled' => true,
        'rules' => [
            'gstin_invoice_number' => [
                'enabled' => true,
                'description' => 'Same GSTIN + invoice number',
                'weight' => 1.0,
            ],
            'vendor_name_invoice_number' => [
                'enabled' => true,
                'description' => 'Same vendor name + invoice number',
                'weight' => 0.95,
            ],
            'amount_similarity' => [
                'enabled' => true,
                'description' => 'Very similar amount from same vendor',
                'threshold' => 0.98, // 98% match
                'weight' => 0.85,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Debugging
    |--------------------------------------------------------------------------
    |
    | Debug mode for detailed logging and testing.
    |
    */

    'debug' => [
        'enabled' => env('INVOICE_DEBUG', false),
        'log_ocr_responses' => true,
        'log_regex_matches' => true,
        'save_intermediate_files' => false,
        'log_vendor_resolution' => true,
    ],
];
