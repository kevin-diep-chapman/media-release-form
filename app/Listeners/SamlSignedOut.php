<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Slides\Saml2\Events\SignedOut;

class SamlSignedOut
{
    public function handle(SignedOut $event): void
    {
        Auth::logout();
        Session::save();
    }
}
