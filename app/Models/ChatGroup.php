<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatGroup extends Model
{
    protected $fillable = ['name', 'created_by', 'company_id'];

    public function users() {
        return $this->belongsToMany(User::class,'chat_group_users');
    }

    public function messages() {
        return $this->hasMany(ChatMessage::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
