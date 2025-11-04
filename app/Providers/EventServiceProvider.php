<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\ProjectCreated;
use App\Events\ProjectAssigned;
use App\Events\ProjectCompleted;
use App\Listeners\SendProjectCreatedNotification;
use App\Listeners\SendProjectAssignedNotification;
use App\Listeners\SendProjectCompletedNotification;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        ProjectCreated::class => [
            SendProjectCreatedNotification::class,
        ],
        ProjectAssigned::class => [
            SendProjectAssignedNotification::class,
        ],
        ProjectCompleted::class => [
            SendProjectCompletedNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
