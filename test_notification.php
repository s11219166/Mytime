<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = App\Models\User::first();
$project = App\Models\Project::first();

if($user && $project) {
    $service = app(App\Services\NotificationService::class);
    $service->sendProjectDueReminder($project, $user, 3);
    echo "Notification created\n";
} else {
    echo "No user or project found\n";
}
