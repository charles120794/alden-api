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

class BuildingPolicyController extends Controller
{
	public function index(Request $request)
	{
		return BuildingUnitPolicy::where('created_by', Auth()->User()->id)get();
	}

	public function show($id)
	{
		try {
			return BuildingUnitPolicy::where('id', $id)->firstOrFail();
		} catch (\Exception $e) {
			return "Building Policy not found!";
		}
	}

	public function store(Request $request)
	{
		$building = new BuildingUnitPolicy;

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

			BuildingUnitPolicy::where('id', $request->id)->firstOrFail();

			BuildingUnitPolicy::where('id', $request->id)->update([
				'unit_id' => $request->unit_id,
				'building_id' => $request->building_id,
				'name' => $request->name,
				'description' => $request->description,
			]);

			return "Successfully Updated";
			
		} catch (\Exception $e) {
			return "Building Policy not found!";
		}
	}

	public function destroy(Request $request)
	{
		try {

			$building = BuildingUnitPolicy::where('id', $request->id)->firstOrFail();

			$building->delete();

			return "Successfully Deleted";

		} catch (\Exception $e) {
			return "Building Policy not found!";
		}
	}
}