<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $table = "resort_reservation";

    protected $primaryKey = "id";

    public $timestamps = false;

    public function userCreated()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function reservationPriceDesc()
    {
        return $this->hasOne(ResortPricings::class, 'id', 'pricing_id');
    }
}
