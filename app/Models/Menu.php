<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
     protected $fillable = [
        'module_name', 'name', 'sub_section', 'route', 'icon', 'user_type',
        'can_add', 'can_edit', 'can_delete', 'can_view', 'can_menu',
    ];
    public function privileges()
{
    return $this->hasMany(UserMenuPrivilege::class);
}

}
