<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResortRatings extends Model
{
    use HasFactory;

    protected $table = "resort_rate";

    protected $primaryKey = "id";

    public $timestamps = false;

    public function createdUser()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function resortInfo()
    {
        return $this->hasOne(Resorts::class, 'id', 'resort_id');
    }

    public function rateImages()
    {
        return $this->hasMany(RateImages::class, 'resort_rate_id', 'id');
    }
}