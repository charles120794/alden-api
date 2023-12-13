<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RateImages extends Model
{
    use HasFactory;

    protected $table = "rate_images";

    protected $primaryKey = "id";

    public $timestamps = false;

    public function createdUser()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function rateInfo()
    {
        return $this->hasOne(ResortRatings::class, 'id', 'resort_rate_id');
    }
}