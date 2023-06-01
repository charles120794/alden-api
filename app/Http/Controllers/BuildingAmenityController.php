<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\Building;
use App\Models\BuildingUnit;
use App\Models\BuildingUnitAmenity;
use App\Models\BuildingUnitPolicy;

class BuildingAmenityController extends Controller
{
	public function index(Request $request)
	{
		return BuildingUnitAmenity::get();
	}

	public function show($id)
	{
		try {
			return BuildingUnitAmenity::where('id', $id)->firstOrFail();
		} catch (\Exception $e) {
			return "Building Amenity not found!";
		}
	}

	public function store(Request $request)
	{
		$building = new BuildingUnitAmenity;

		$building->insert([
			'unit_id' => $request->unit_id,
			'building_id' => $request->building_id,
			'name' => $request->name,
			'description' => $request->description,
			'created_at' => now(),
			'created_by' => Auth()->User()->id
		]);

		return "Successfully Created";
	}

	public function update(Request $request)
	{
			
		try {

			BuildingUnitAmenity::where('id', $request->id)->firstOrFail();

			BuildingUnitAmenity::where('id', $request->id)->update([
				'unit_id' => $request->unit_id,
				'building_id' => $request->building_id,
				'name' => $request->name,
				'description' => $request->description,
			]);

			return "Successfully Updated";
			
		} catch (\Exception $e) {
			return "Building Amenity not found!";
		}
	}

	public function destroy(Request $request)
	{
		try {

			$building = BuildingUnitAmenity::where('id', $request->id)->firstOrFail();

			$building->delete();

			return "Successfully Deleted";

		} catch (\Exception $e) {
			return "Building Amenity not found!";
		}
	}
}