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

class BuildingController extends Controller
{
	public function index(Request $request)
	{
		return Building::with('buildingUnit.userCreated')->with('buildingAmenity')->where('created_by', Auth()->User()->id)->get();
	}

	public function show($id)
	{
		try {
			return Building::where('building_id', $id)->firstOrFail();
		} catch (\Exception $e) {
			return "Building not found!";
		}
	}

	public function store(Request $request)
	{
		$building = new Building;

		$building->insert([
			'building_name' => $request->building_name,
			'building_address' => $request->building_address,
			'created_at' => now(),
			'created_by' => Auth()->User()->id
		]);

		return "Successfully Created";
	}

	public function update(Request $request)
	{
		try {

			Building::where('building_id', $request->building_id)->firstOrFail();

			Building::where('building_id', $request->building_id)->update([
				'building_name' => $request->building_name,
				'building_address' => $request->building_address
			]);

			return "Successfully Updated";

		} catch (\Exception $e) {
			return "Building not found!";
		}
	}

	public function destroy(Request $request)
	{
		try {

			$building = Building::where('building_id', $request->building_id)->firstOrFail();

			$building->delete();

			return "Successfully Deleted";

		} catch (\Exception $e) {
			return "Building not found!";
		}
	}
}