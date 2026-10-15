<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as ViewInstance;

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
        // The sidebar shows the signed-in user's streak on every page
        View::composer('partials.sidebar', function (ViewInstance $view) {
            $streak = auth()->user()->currentStreak();

            $view->with([
                'sidebarStreak' => $streak,
                'sidebarGoal' => auth()->user()->nextStreakMilestone($streak),
            ]);
        });
    }
}
