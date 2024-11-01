<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Pusher\Pusher;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'useTLS' => true,
            ]
        );
    }

    public function sendMessage(Request $request)
    {
        // $request->validate([
        //     'message' => 'required|string',
        //     'group_id' => 'nullable|exists:groups,id',
        // ]);

        $chat = Chat::create([
            'user_id' => Auth::id(),
            'group_id' => $request->group_id,
            'message' => $request->message,
        ]);

        $options = [
            'cluster' => env('PUSHER_APP_CLUSTER'),
            'useTLS' => true,
        ];
        $pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            $options
        );

        $pusher->trigger('chat.' . ($request->group_id ?? 'global'), 'message.sent', $chat);

        return response()->json(['status' => 'Message sent!']);
    }

    public function getMessages(Request $request)
    {
        $groupId = $request->group_id;
        $messages = Chat::with('user')->where('group_id', $groupId)->get();
        return response()->json($messages);
    }
}
