<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use App\Http\Requests\EmailVerificationRequest; 

class VerificationController extends Controller
{
    use VerifiesEmails;

    protected $redirectTo = 'https://quickrent.online/signin';

    public function __construct()
    {
        $this->middleware('auth')->only('verify');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify');
    }

    public function verify(Request $request, $id, $hash)
    {
        $user = User::find($id);

        if (!$user || !hash_equals($hash, sha1($user->getEmailForVerification()))) {
            // Invalid verification link
            abort(404);
        }

        if ($user->hasVerifiedEmail()) {
            // User already verified
            return redirect('/'); // You can redirect wherever you want
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        // Redirect the user after manual verification
        return redirect('/')->with('verified', true); 
    }
}
