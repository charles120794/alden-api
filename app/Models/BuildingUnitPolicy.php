<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildingUnitPolicy extends Model
{
    use HasFactory;

    protected $table = "building_unit_policy";

    protected $primaryKey = "id";

    public $timestamps = false;
}