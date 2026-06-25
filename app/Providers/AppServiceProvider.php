<?php

namespace App\Providers;

use App\Listeners\SamlSignedIn;
use App\Listeners\SamlSignedOut;
use App\Models\Event;
use App\Models\MediaRelease;
use App\Models\User;
use App\Observers\EventObserver;
use App\Observers\MediaReleaseObserver;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\Event as EventFacade;
use Illuminate\Support\ServiceProvider;
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
        EventFacade::listen(SignedIn::class, SamlSignedIn::class);
        EventFacade::listen(SignedOut::class, SamlSignedOut::class);

        Event::observe(EventObserver::class);
        MediaRelease::observe(MediaReleaseObserver::class);
        User::observe(UserObserver::class);
    }
}
