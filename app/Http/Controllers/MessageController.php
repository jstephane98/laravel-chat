<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Http\Enums\MessageType;
use App\Models\Message;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Return all messages sent by the authenticated user to the given receiver ID.
     *
     * @param int $receive_id The ID of the user that received the messages.
     *
     * @return JsonResponse
     */
    public function index(int $receive_id, int $send_id)
    {
        $messages = Message::where(function (Builder $query) use ($receive_id, $send_id) {
            $query->where('send_id', $send_id)
                ->where('received_id', $receive_id);
        })
        ->orWhere(function (Builder $query) use ($send_id, $receive_id) {
            $query->where('send_id', $receive_id)
                ->where('received_id', $send_id);
        })
        ->get();

        return response()->json([
            'messages' => $messages
        ]);
    }

    /**
     * Store a newly created message in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'value' => 'required|string|max:255',
            'receive_id' => 'required|exists:users,id',
            'type' => 'required|string',
        ]);

        $message = Message::create([
            'send_id' => auth()->user()->id,
            'received_id' => $request->receive_id,
            'type' => $request->type,
            'value' => $request->value,
            'group_id' => $request->group_id,
        ]);

        broadcast(new MessageSent($message));

        return redirect()->back();
    }
}
