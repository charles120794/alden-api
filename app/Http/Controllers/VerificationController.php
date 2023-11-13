<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function verify($user_id, Request $request){
        if(!$request->hasValidSignature()){
            return response()->json([
                'response' => 'Invalid/expired link.'
            ], 401);
        }

        $user = User::findOrFail($user_id);

        if(!$user->hasVerifiedEmail()){

            $user->markEmailAsVerified();

        }else{
            return response()->json([
                'status' => 400,
                'response' => 'Email already verified'
            ], 400);
        }

        return response()->json([
            'status' => 200,
            'response' => 'Email successfully verified'
        ], 200);
    }
}
