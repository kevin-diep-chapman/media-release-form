<?php

namespace App\Providers;

use App\Listeners\SamlSignedIn;
use App\Listeners\SamlSignedOut;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event as EventFacade;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Slides\Saml2\Events\SignedIn;
use Slides\Saml2\Events\SignedOut;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('security.force_https')) {
            URL::forceScheme('https');
        }

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('media-release', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        Password::defaults(function () {
            $rule = Password::min(8)->letters()->numbers();

            if (app()->isProduction()) {
                $rule = $rule->mixedCase()->uncompromised();
            }

            return $rule;
        });

        EventFacade::listen(SignedIn::class, SamlSignedIn::class);
        EventFacade::listen(SignedOut::class, SamlSignedOut::class);
    }
}
