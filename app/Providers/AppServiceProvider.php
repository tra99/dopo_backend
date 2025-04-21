<?php

namespace App\Providers;

use App\Models\Answer;
use App\Models\Evaluation;
use App\Observers\AchievementDetailObserver;
use App\Observers\MissionEvaluateOberser;
use App\Observers\SchoolCategoryObserver;
use App\Observers\SchoolSurveyObserver;
use App\Services\FileService;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind the service into the container
        $this->app->singleton(FileService::class, function ($app) {
            return new FileService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url') . "/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });
        Answer::observe(SchoolSurveyObserver::class);
        Answer::observe(SchoolCategoryObserver::class);
        Evaluation::observe(MissionEvaluateOberser::class);
        Evaluation::observe(AchievementDetailObserver::class);
    }
}
