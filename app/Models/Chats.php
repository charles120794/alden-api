<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chats extends Model
{
    use HasFactory;

    protected $table = "chats";

    protected $primaryKey = "id";

    protected $fillable = ['user1_id', 'user2_id', 'created_at', 'updated_at'];

    public $timestamps = false;

    public function userInfo1() 
    {
        return $this->hasOne(User::class, 'id', 'user1_id');
    }

    public function userInfo2() 
    {
        return $this->hasOne(User::class, 'id', 'user2_id');
    }

    public function userInfoCreated() 
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function chatsMessages() 
    {
        return $this->hasMany(ChatsMessages::class, 'channel_id', 'id')->orderBy('created_at', 'asc');
    }
}