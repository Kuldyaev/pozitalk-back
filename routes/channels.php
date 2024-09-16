<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('private-notification.{userId}', function (User $user, $userId) {
    Log::info('Channel private-notification.' . $userId);
    return true;
    // return (int) $user->id === (int) $userId;
});

