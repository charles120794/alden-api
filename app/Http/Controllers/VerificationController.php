<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use App\Http\Requests\EmailVerificationRequest; 

class VerificationController extends Controller
{
    use VerifiesEmails;

    protected $redirectTo = 'https://quickrent.online/signin';

    public function fulfill(EmailVerificationRequest $request)
    {
        parent::fulfill();

        // Your custom logic goes here
        // For example, you can log a message or perform additional actions

        return redirect()->route('login')->with('verified', true);
    }
}
