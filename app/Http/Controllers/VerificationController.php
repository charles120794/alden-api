<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use App\Http\Requests\EmailVerificationRequest;
use App\Models\User;


class VerificationController extends Controller
{
    use VerifiesEmails;

    protected $redirectToHome = 'https://quickrent.online/';
    protected $redirectToSignIn = 'https://quickrent.online/signin';

    // public function __construct()
    // {
    //     $this->middleware('auth');
    //     $this->middleware('signed')->only('verify');
    //     $this->middleware('throttle:6,1')->only('verify', 'resend');
    // }

    public function verify(Request $request)
    {
        try{
            $user = User::findOrFail($request->route('id')); 

        // Check if the user is already verified to avoid unnecessary updates
            if (!$user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
            }

            return redirect('https://quickrent.online/signin');
        
        }catch (\Exception $e) {

            return response()->json(['response' => $e->getMessage()]);

        }
    }
}
