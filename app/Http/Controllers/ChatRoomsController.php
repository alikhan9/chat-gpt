<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChatRoomsController extends Controller
{
    public function store(Request $request)
    {
        $result = $request->validate([
            'name' => 'required|string',
            'user_id' => 'required|integer',
        ]);

        Chatroom::create($result);

        return Inertia::location('/');
    }

    public function destroy(ChatRoom $chatRoom)
    {
        $chatRoom->delete();
        return Inertia::location('/');
    }

    public function update(ChatRoom $chatRoom, Request $request)
    {
        $chatRoom->update(['name' => $request->name]);
        return back();
    }
}
