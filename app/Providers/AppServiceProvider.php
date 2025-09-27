<?php

namespace App\Providers;

use App\Models\CompanyProfile;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

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
        // Listen for login events
        Event::listen(Login::class, function ($event) {
            // Get the user's default company profile
            $companyProfile = CompanyProfile::where('user_id', $event->user->id)
                ->where('is_default', 1)
                ->first();

            // Store the company profile in the session
            if ($companyProfile) {
                session(['company_profile' => $companyProfile]);
            }
        });
    }
}
