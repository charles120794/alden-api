<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resorts extends Model
{
    use HasFactory;

    protected $table = "resort";

    protected $primaryKey = "id";

    public $timestamps = false;

    public function createdUser()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function images()
    {
        return $this->hasMany(Images::class, 'resort_id', 'id')->where('archive', 0);
    }
}