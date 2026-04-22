<?php

use App\Models\ChatGroup;
use App\Services\LiveChatService;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Storage;

Broadcast::channel('chat-group.{groupId}', function ($user, $groupId) {
    $group = ChatGroup::with('company')->find($groupId);

    if (! $group) {
        return false;
    }

    $service = app(LiveChatService::class);

    if (! $service->userCanAccessGroup($user, $group)) {
        return false;
    }

    $group->users()->syncWithoutDetaching([$user->id]);

    $user->loadMissing(['profile', 'userType']);

    return [
        'id' => $user->id,
        'name' => $user->name,
        'avatar' => $user->profile && $user->profile->profile_photo
            ? asset('storage/' . $user->profile->profile_photo)
            : null,
        'type' => $user->userType->name ?? null,
    ];
});
