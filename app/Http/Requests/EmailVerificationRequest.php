<?php

namespace App\Http\Requests;

use Illuminate\Auth\Events\EmailVerified;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Access\AuthorizationException;

class EmailVerificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function fulfill()
    {
        $this->fulfillAndRedirect(
            config('verification.mail')
        );
    }

    /**
     * Fulfill the email verification request.
     *
     * @param  array  $options
     * @return void
     */
    public function fulfillAndRedirect(array $options)
    {
        $user = $this->user();

        if (! hash_equals((string) $this->route('hash'), sha1($user->getEmailForVerification()))) {
            throw new AuthorizationException;
        }

        if ($user->hasVerifiedEmail()) {
            return $this->redirectPath();
        }

        if ($user->markEmailAsVerified() === false) {
            throw new AuthorizationException;
        }

        event(new EmailVerified($user));

        $this->redirectPath();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
