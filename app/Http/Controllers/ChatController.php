<?php

namespace App\Http\Controllers;

use App\Http\Enums\MessageType;
use App\Models\Message;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class ChatController extends Controller
{
    /**
     * Get the chat page with the list of users and message types.
     *
     * @return Response
     */
    public function chat()
    {
        $users = User::where('id', '!=', auth()->user()->id)->get();

        return Inertia::render('Chat', [
            'message_type' =>  MessageType::toArray(),
            'contacts' => $users,
        ]);
    }
}
