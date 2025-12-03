<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class ExtendRememberMeOnLogin
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        // This listener extends the remember me cookie expiration to 1 year
        // when a user logs in with "remember me" checked
        // The cookie is set by Laravel during Auth::attempt(), but we can't modify it here
        // The ExtendRememberMeCookie middleware will handle refreshing it on subsequent requests
    }
}

