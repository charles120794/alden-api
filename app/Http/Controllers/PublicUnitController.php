<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use App\Models\BuildingUnit;

class PublicUnitController extends Controller
{
	public function index(Request $request)
	{
		return BuildingUnit::with('building','unitAmenity','unitPolicy','userCreated')
			->when(!empty($request->search), function($query) {
				return $query->where('unit_name', 'like', '%' . request()->search . '%')
				->orWhere('unit_name', 'like', '%' . request()->search . '%');
			})
			->get();
	}
}