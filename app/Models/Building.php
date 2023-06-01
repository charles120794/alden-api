<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    use HasFactory;

    protected $table = "building";

    protected $primaryKey = "building_id";

    public $timestamps = false;

    public function buildingAmenity()
    {
        return $this->hasMany(BuildingUnitAmenity::class, 'building_id', 'building_id');
    }

    public function buildingUnit()
    {
        return $this->hasMany(BuildingUnit::class, 'building_id', 'building_id')->with('unitAmenity', 'unitPolicy' ,'building');
    }
}
