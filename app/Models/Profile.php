<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    
      use HasFactory;

      protected $table = 'profiles'; 

    protected $fillable = [
        'user_id',
        'profile_photo',
        'fname', 
        'lname', 
        'designation', 
        'Date_of_Birth',
        'official_email',
        'personal_email',
        'phone1', 
        'phone2', 
        'aadhaar_number', 
        'aadhaar_upload',
        'pan', 
        'pan_upload', 
        'bank_name', 
        'branch',
        'bank_account_no', 
        'ifsc_code',
        // 'profile_created'
    ];

      public function user()
    {
        return $this->belongsTo(User::class);
    }
 

    
}
