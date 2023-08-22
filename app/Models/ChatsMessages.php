<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatsMessages extends Model
{
    use HasFactory;

    protected $table = "chats_message";

    protected $primaryKey = "id";

    public $timestamps = false;

    public function userInfo() 
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}