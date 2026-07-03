<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\MessageRecipient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    // ── BOÎTE DE RÉCEPTION ────────────────────────────────────────────────
    public function index(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $folder = $request->input('folder', 'inbox');

        if ($folder === 'sent') {
            $threads = Message::where('sender_id', $user->id)
                ->with(['recipients.recipient'])
                ->orderByDesc('created_at')->paginate(20);
        } else {
            $threads = MessageRecipient::where('recipient_id', $user->id)
                ->when($folder === 'archived', fn($q) => $q->where('is_archived', true))
                ->when($folder === 'inbox', fn($q) => $q->where('is_archived', false))
                ->with(['message.sender'])
                ->orderByDesc('created_at')->paginate(20);
        }

        $unreadCount = MessageRecipient::where('recipient_id', $user->id)
            ->where('is_read', false)->count();

        return view('communication.messages.index', compact(
            'threads', 'folder', 'unreadCount'
        ));
    }

    public function create()
    {
        $staffUsers = User::where('id', '!=', Auth::id())
            ->orderBy('name')->get();

        return view('communication.messages.create', compact('staffUsers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'recipient_ids'   => ['required', 'array', 'min:1'],
            'recipient_ids.*' => ['exists:users,id'],
            'subject'         => ['required', 'string', 'max:191'],
            'body'            => ['required', 'string', 'max:5000'],
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'subject'   => $request->subject,
            'body'      => $request->body,
        ]);

        foreach ($request->recipient_ids as $recipientId) {
            MessageRecipient::create([
                'message_id'   => $message->id,
                'recipient_id' => $recipientId,
            ]);
        }

        return redirect()->route('communication.messages.index')
            ->with('success', 'Message envoyé avec succès.');
    }

    public function show(Message $message)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        $recipient = MessageRecipient::where([
            'message_id'   => $message->id,
            'recipient_id' => $user->id,
        ])->first();

        if ($recipient && !$recipient->is_read) {
            $recipient->update(['is_read' => true, 'read_at' => now()]);
        }

        if ($message->sender_id !== $user->id && !$recipient) {
            abort(403);
        }

        $message->load('sender', 'recipients.recipient');

        return view('communication.messages.show', compact('message'));
    }

    public function archive(MessageRecipient $messageRecipient)
    {
        $messageRecipient->update(['is_archived' => true]);
        return back()->with('success', 'Message archivé.');
    }
}