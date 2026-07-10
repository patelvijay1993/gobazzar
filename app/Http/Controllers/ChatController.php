<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Business;
use App\Models\BusinessPost;
use App\Models\Conversation;
use App\Models\Event;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function inbox()
    {
        $userId = Auth::id();

        $conversations = Conversation::with(['conversable', 'buyer', 'seller', 'latestMessage'])
            ->where('buyer_id', $userId)
            ->orWhere('seller_id', $userId)
            ->orderByDesc(function ($query) {
                $query->select('created_at')
                    ->from('chat_messages')
                    ->whereColumn('conversation_id', 'conversations.id')
                    ->orderByDesc('created_at')
                    ->limit(1);
            })
            ->get();

        return view('chat.inbox', compact('conversations'));
    }

    // Generic: open/create conversation for any model (Listing, Event, Business)
    private function openChat(object $model, int $ownerId): \Illuminate\Http\Response|\Illuminate\View\View
    {
        $userId = Auth::id();
        abort_if($ownerId === $userId, 403, 'You cannot chat with yourself.');

        $modelClass = get_class($model);

        $conversation = Conversation::firstOrCreate(
            ['conversable_type' => $modelClass, 'conversable_id' => $model->id, 'buyer_id' => $userId],
            ['seller_id' => $ownerId]
        );

        $conversation->markReadFor($userId);
        $conversation->load(['messages.sender', 'conversable', 'buyer', 'seller']);

        return view('chat.show', compact('conversation'));
    }

    public function showListing(Listing $listing)
    {
        return $this->openChat($listing, $listing->user_id);
    }

    public function showEvent(Event $event)
    {
        return $this->openChat($event, $event->user_id);
    }

    public function showBusiness(Business $business)
    {
        return $this->openChat($business, $business->user_id);
    }

    public function showBusinessPost(Business $business, BusinessPost $post)
    {
        return $this->openChat($post, $post->user_id);
    }

    public function showConversation(Conversation $conversation)
    {
        $userId = Auth::id();
        abort_unless(
            (int)$conversation->buyer_id === (int)$userId || (int)$conversation->seller_id === (int)$userId,
            403
        );

        $conversation->markReadFor($userId);
        $conversation->load(['messages.sender', 'conversable', 'buyer', 'seller']);

        return view('chat.show', compact('conversation'));
    }

    public function send(Request $request, Conversation $conversation)
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['error' => 'unauthenticated'], 401);
        }
        abort_unless(
            (int)$conversation->buyer_id === (int)$userId || (int)$conversation->seller_id === (int)$userId,
            403
        );

        $request->validate(['body' => 'required|string|max:2000']);

        $message = $conversation->messages()->create([
            'sender_id' => $userId,
            'body'      => $request->body,
        ]);

        $message->load('sender');
        $conversation->markReadFor($userId);

        try { broadcast(new MessageSent($message))->toOthers(); } catch (\Throwable $e) { /* Reverb not running — polling fallback handles delivery */ }

        // Push notification to the other person
        $recipientId = (int)$conversation->buyer_id === (int)$userId
            ? (int)$conversation->seller_id
            : (int)$conversation->buyer_id;
        \App\Http\Controllers\PushController::sendToUser(
            $recipientId,
            'New message from ' . $message->sender->name,
            \Str::limit($message->body, 80),
            route('chat.conversation', $conversation->id)
        );

        return response()->json([
            'id'          => $message->id,
            'sender_id'   => $message->sender_id,
            'sender_name' => $message->sender->name,
            'body'        => $message->body,
            'created_at'  => $message->created_at->toISOString(),
        ]);
    }

    public function poll(Request $request, Conversation $conversation)
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['error' => 'unauthenticated'], 401);
        }
        abort_unless(
            (int)$conversation->buyer_id === (int)$userId || (int)$conversation->seller_id === (int)$userId,
            403
        );

        $after    = $request->input('after', 0);
        $messages = $conversation->messages()
            ->with('sender')
            ->where('id', '>', $after)
            ->get()
            ->map(fn($m) => [
                'id'          => $m->id,
                'sender_id'   => $m->sender_id,
                'sender_name' => $m->sender->name,
                'body'        => $m->body,
                'created_at'  => $m->created_at->toISOString(),
            ]);

        if ($messages->isNotEmpty()) {
            $conversation->markReadFor($userId);
        }

        return response()->json($messages);
    }

    public function markRead(Conversation $conversation)
    {
        $userId = Auth::id();
        abort_unless(
            (int)$conversation->buyer_id === (int)$userId || (int)$conversation->seller_id === (int)$userId,
            403
        );
        $conversation->markReadFor($userId);
        return response()->json(['ok' => true]);
    }

    public function unreadCount()
    {
        $userId = Auth::id();
        $conversations = Conversation::with('latestMessage')
            ->where('buyer_id', $userId)
            ->orWhere('seller_id', $userId)
            ->get();

        $total = $conversations->sum(fn($c) => $c->unreadCountFor($userId));

        $latestUnread = $conversations
            ->filter(fn($c) => $c->unreadCountFor($userId) > 0)
            ->sortByDesc(fn($c) => optional($c->latestMessage)->created_at)
            ->first();

        return response()->json([
            'count'    => $total,
            'conv_url' => $latestUnread
                ? route('chat.conversation', $latestUnread->id)
                : null,
        ]);
    }
}
