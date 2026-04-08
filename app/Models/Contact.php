<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'contact_type',
        'name',
        'area',
        'state',
        'contact1',
        'contact2',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
