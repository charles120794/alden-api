<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Foundation\Auth\EmailVerificationRequest as BaseEmailVerificationRequest;

class VerificationController extends BaseEmailVerificationRequest
{
    use VerifiesEmails, RedirectsUsers;

    protected $redirectTo = 'https://quickrent.online/signin';

    public function fulfill()
    {
        // Add your custom logic here
        parent::fulfill(); // Make sure to call the parent fulfill method to complete the verification

        return redirect()->route($redirectTo)->with('verified', true);
        // Your custom logic goes here
        // For example, you can log a message or perform additional actions
    }
}
