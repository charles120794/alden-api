<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use App\Http\Requests\EmailVerificationRequest; 

class VerificationController extends Controller
{
    use VerifiesEmails;

    protected $redirectToHome = 'https://quickrent.online/signin';
    protected $redirectToSignIn = 'https://quickrent.online/signin';

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    public function verify(Request $request)
    {
        $user = $request->user();
        
        if ($user->hasVerifiedEmail()) {
            return redirect('/home'); // or any other route
        }

        $user->markEmailAsVerified();
        event(new \Illuminate\Auth\Events\Verified($user));

        return redirect('/signin')->with('verified', true);
    }
}
