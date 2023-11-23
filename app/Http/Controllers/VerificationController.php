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

    public function verify(Request $request)
    {
        // ... existing code ...

        // Update the user's email_verified_at column
        $user = User::find($request->route('id'));
        $user->markEmailAsVerified();

        // Fire the Verified event
        event(new Verified($request->user()));

        // Redirect the user to the sign-in page
        return redirect($this->redirectPath())->with('verified', true);
    }
}
