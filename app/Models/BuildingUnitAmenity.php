<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildingUnitAmenity extends Model
{
    use HasFactory;

    protected $table = "building_unit_amenity";

    protected $primaryKey = "id";

    public $timestamps = false;
}