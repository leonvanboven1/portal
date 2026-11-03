<?php

namespace App\Http\Controllers;

use App\Models\SiteChatConversation;
use App\Models\SiteChatMessage;
use App\Models\SiteChatStaffPresence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SiteChatController extends Controller
{
    private const SESSION_KEY = 'site_chat_conversation_id';

    public function status(Request $request)
    {
        $conversation = $this->visitorConversation($request);

        return response()->json([
            'available' => $this->staffAvailable(),
            'conversation' => $conversation ? $this->presentConversation($conversation) : null,
            'messages' => $conversation ? $this->messages($conversation) : [],
        ])->header('Cache-Control', 'private, no-store');
    }

    public function start(Request $request)
    {
        $validated = $request->validate([
            'visitor_name' => ['required', 'string', 'max:80'],
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $existing = $this->visitorConversation($request);
        if ($existing) {
            return response()->json([
                'conversation' => $this->presentConversation($existing),
                'messages' => $this->messages($existing),
            ], 200);
        }

        $conversation = DB::transaction(function () use ($request, $validated) {
            $conversation = SiteChatConversation::create([
                'id' => (string) Str::uuid(),
                'visitor_name' => trim($validated['visitor_name']),
                'visitor_user_id' => $request->user()?->id,
                'status' => 'open',
                'last_message_at' => now(),
            ]);

            $conversation->messages()->create([
                'sender_type' => 'visitor',
                'sender_user_id' => $request->user()?->id,
                'sender_name' => $conversation->visitor_name,
                'body' => trim($validated['body']),
            ]);

            return $conversation;
        });

        $request->session()->put(self::SESSION_KEY, $conversation->id);

        return response()->json([
            'conversation' => $this->presentConversation($conversation->load('assignedStaff')),
            'messages' => $this->messages($conversation),
        ], 201);
    }

    public function send(Request $request)
    {
        $validated = $request->validate(['body' => ['required', 'string', 'max:2000']]);
        $conversation = $this->visitorConversation($request);

        abort_unless($conversation && $conversation->status === 'open', 404);

        $conversation->messages()->create([
            'sender_type' => 'visitor',
            'sender_user_id' => $request->user()?->id,
            'sender_name' => $conversation->visitor_name,
            'body' => trim($validated['body']),
        ]);
        $conversation->update(['last_message_at' => now()]);

        return response()->json(['ok' => true]);
    }

    private function visitorConversation(Request $request): ?SiteChatConversation
    {
        $id = $request->session()->get(self::SESSION_KEY);
        if (! is_string($id)) {
            return null;
        }

        return SiteChatConversation::with('assignedStaff')
            ->whereKey($id)
            ->where('status', 'open')
            ->first();
    }

    private function staffAvailable(): bool
    {
        return SiteChatStaffPresence::with('user.roles.permissions')
            ->where('last_seen_at', '>=', now()->subSeconds(75))
            ->get()
            ->contains(fn (SiteChatStaffPresence $presence) => $presence->user?->can('manage-live-chat'));
    }

    private function presentConversation(SiteChatConversation $conversation): array
    {
        return [
            'id' => $conversation->id,
            'visitor_name' => $conversation->visitor_name,
            'staff_name' => $conversation->assignedStaff?->minecraft_username ?: $conversation->assignedStaff?->name,
            'status' => $conversation->status,
        ];
    }

    private function messages(SiteChatConversation $conversation): array
    {
        return $conversation->messages()
            ->orderByDesc('id')
            ->limit(100)
            ->get()
            ->reverse()
            ->values()
            ->map(fn (SiteChatMessage $message) => [
                'id' => $message->id,
                'sender_type' => $message->sender_type,
                'sender_name' => $message->sender_name,
                'body' => $message->body,
                'created_at' => $message->created_at->format('H:i'),
            ])
            ->all();
    }
}
