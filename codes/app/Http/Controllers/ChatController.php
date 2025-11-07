<?php

namespace App\Http\Controllers;
use App\Models\Chat;
use App\Models\ChatUser;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function chats()
{
    $chats = Chat::whereHas('chatUser', function($query) {
        $query->where('user_id', auth()->id());
    })->get();

    return response()->json(['chats' => $chats]);
}


    public function users(int $chat_id)
    {
        $users = ChatUser::where('chat_id', $chat_id)->user()->get();
        return response()->json(['users' => $users]);
    }

    public function chat()
    {

    }
}
