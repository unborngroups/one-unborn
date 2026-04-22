<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserType extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'email', 'Description', 'status'];
    public function users()
    {
        return $this->hasMany(User::class, 'user_type_id');
    }

}
