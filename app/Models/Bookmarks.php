<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bookmarks extends Model
{
    use HasFactory;

    protected $table = "bookmarks";

    protected $primaryKey = "id";

    public $timestamps = false;

    public function resortInfo()
    {
        return $this->hasOne(Resorts::class, 'id', 'resort_id');
    }
}