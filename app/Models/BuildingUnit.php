<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildingUnit extends Model
{
    use HasFactory;

    protected $table = "building_unit";

    protected $primaryKey = "unit_id";

    public $timestamps = false;

    public function building()
    {
        return $this->hasOne(Building::class, 'building_id', 'building_id')->with('buildingAmenity');
    }

    public function unitAmenity()
    {
        return $this->hasMany(BuildingUnitAmenity::class, 'unit_id', 'unit_id');
    }

    public function unitPolicy()
    {
        return $this->hasMany(BuildingUnitPolicy::class, 'unit_id', 'unit_id');
    }

    public function userCreated()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
