<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
	public function updateOwner(Request $request)
	{
		$path = storage_path('app/public');

		if ($request->hasFile('file')) {

      $file = $request->file('file');
      $filename = time() . '_' . $file->getClientOriginalName();
      $file->storeAs('public', $filename); // You can choose a storage disk here
      // You can also save the filename to a database if needed

      $path = $path . '/' . $filename;
    }

    Auth()->User()->update([
    	'valid_doc' => $path,
    	'type' => 1
    ]);

    return 'Successfully updated';
	}
}