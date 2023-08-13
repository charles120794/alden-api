<?php

namespace App\Http\Controllers;

use Storage;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
	public function updateOwner(Request $request)
	{

        try {

            $path = "";

            if ($request->hasFile('file')) {

                $file = $request->file('file');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public', $filename); // You can choose a storage disk here
                // You can also save the filename to a database if needed

                $path = Storage::disk('public')->url($filename);
            } else {
                throw new Exception("Image is required", 1);
            }

            Auth()->User()->update([
                'valid_doc' => $path,
                'type' => 1
            ]);
         
            return response()->json([
                'authenticated' => true,
                'response' => 'Successfully updated',
                'data' => [
                	'valid_doc' => $path,
                	'type' => 1
                ]
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'authenticated' => true,
                'response' => $e->getMessage(),
                'token' => ''
            ]);
        }
	}
}