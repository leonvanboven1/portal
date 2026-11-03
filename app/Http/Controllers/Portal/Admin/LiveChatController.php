<?php

namespace App\Http\Controllers\Portal\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteChatConversation;
use App\Models\SiteChatMessage;
use App\Models\SiteChatStaffPresence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LiveChatController extends Controller
{
    public function index()
    {
        return view('portal.admin.live-chat');
    }

    public function heartbeat(Request $request)
    {
        SiteChatStaffPresence::updateOrCreate(
            ['user_id' => $request->user()->id],
            ['last_seen_at' => now()],
        );

        return response()->noContent();
    }

    public function conversations()
    {
        return response()->json([
            'conversations' => SiteChatConversation::with(['assignedStaff', 'messages' => fn ($query) => $query->latest('id')->limit(1)])
                ->where('status', 'open')
                ->orderByDesc('last_message_at')
                ->limit(100)
                ->get()
                ->map(fn (SiteChatConversation $conversation) => [
                    'id' => $conversation->id,
                    'visitor_name' => $conversation->visitor_name,
                    'staff_name' => $conversation->assignedStaff?->minecraft_username ?: $conversation->assignedStaff?->name,
                    'last_message' => $conversation->messages->first()?->body,
                    'last_message_at' => $conversation->last_message_at?->diffForHumans(),
                ]),
        ]);
    }

    public function messages(string $conversationId)
    {
        $conversation = SiteChatConversation::findOrFail($conversationId);

        return response()->json([
            'conversation' => [
                'id' => $conversation->id,
                'visitor_name' => $conversation->visitor_name,
                'staff_name' => $conversation->assignedStaff?->minecraft_username ?: $conversation->assignedStaff?->name,
                'status' => $conversation->status,
            ],
            'messages' => $conversation->messages()
                ->orderByDesc('id')
                ->limit(200)
                ->get()
                ->reverse()
                ->values()
                ->map(fn (SiteChatMessage $message) => [
                    'id' => $message->id,
                    'sender_type' => $message->sender_type,
                    'sender_name' => $message->sender_name,
                    'body' => $message->body,
                    'created_at' => $message->created_at->format('H:i'),
                ]),
        ]);
    }

    public function reply(Request $request, string $conversationId)
    {
        $validated = $request->validate(['body' => ['required', 'string', 'max:2000']]);
        $user = $request->user();

        DB::transaction(function () use ($conversationId, $validated, $user) {
            $conversation = SiteChatConversation::whereKey($conversationId)->lockForUpdate()->firstOrFail();
            abort_unless($conversation->status === 'open', 409);

            $conversation->messages()->create([
                'sender_type' => 'staff',
                'sender_user_id' => $user->id,
                'sender_name' => $user->minecraft_username ?: $user->name,
                'body' => trim($validated['body']),
            ]);
            $conversation->update([
                'assigned_staff_id' => $user->id,
                'last_message_at' => now(),
            ]);
        });

        return response()->json(['ok' => true], 201);
    }

    public function close(string $conversationId)
    {
        $conversation = SiteChatConversation::whereKey($conversationId)->firstOrFail();
        $conversation->update(['status' => 'closed', 'assigned_staff_id' => auth()->id()]);

        return response()->json(['ok' => true]);
    }
}
