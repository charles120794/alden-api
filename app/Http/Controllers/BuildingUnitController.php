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

class BuildingUnitController extends Controller
{
	public function index(Request $request)
	{
		return BuildingUnit::with('building','unitAmenity','unitPolicy','userCreated')->get();
	}

	public function show($id)
	{
		try {
			return BuildingUnit::with('building','unitAmenity','unitPolicy','userCreated')->where('unit_id', $id)->firstOrFail();
		} catch (\Exception $e) {
			return "Building Unit not found!";
		}
	}

	public function store(Request $request)
	{
		$building = new BuildingUnit;

		$building->insert([
			'building_id' => $request->building_id,
			'unit_name' => $request->unit_name,
			'unit_description' => $request->unit_description,
			'unit_floor' => $request->unit_floor,
			'unit_room_no' => $request->unit_room_no,
			'unit_rate' => $request->unit_rate,
			'unit_rooms' => $request->unit_rooms,
			'unit_status' => $request->unit_status,
			'created_at' => now(),
			'created_by' => Auth()->User()->id
		]);

		return "Successfully Created";
	}

	public function update(Request $request)
	{
			
		try {

			BuildingUnit::where('unit_id', $request->unit_id)->firstOrFail();

			BuildingUnit::where('unit_id', $request->unit_id)->update([
				'building_id' => $request->building_id,
				'unit_name' => $request->unit_name,
				'unit_description' => $request->unit_description,
				'unit_floor' => $request->unit_floor,
				'unit_room_no' => $request->unit_room_no,
				'unit_rate' => $request->unit_rate,
				'unit_rooms' => $request->unit_rooms,
				'unit_status' => $request->unit_status
			]);

			return "Successfully Updated";
			
		} catch (\Exception $e) {
			return "Building Unit not found!";
		}
	}

	public function destroy(Request $request)
	{
		try {

			$building = BuildingUnit::where('unit_id', $request->unit_id)->firstOrFail();

			$building->delete();

			return "Successfully Deleted";

		} catch (\Exception $e) {
			return "Building Unit not found!";
		}
	}
}