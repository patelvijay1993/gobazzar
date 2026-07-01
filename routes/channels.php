<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    $conv = \App\Models\Conversation::find($conversationId);
    return $conv && ($conv->buyer_id === $user->id || $conv->seller_id === $user->id);
});
