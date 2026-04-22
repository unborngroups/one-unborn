<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTypeMenuPrivilege extends Model
{
    protected $fillable = [
        'user_type_id', 
        'menu_id', 
        'can_menu', 
        'can_add', 
        'can_edit', 
        'can_delete', 
        'can_view'
    ];

    protected $casts = [
        'can_menu' => 'boolean',
        'can_add' => 'boolean',
        'can_edit' => 'boolean',
        'can_delete' => 'boolean',
        'can_view' => 'boolean',
    ];

    public function userType()
    {
        return $this->belongsTo(UserType::class);
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
