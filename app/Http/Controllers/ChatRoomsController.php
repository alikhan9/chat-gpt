<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use Illuminate\Http\Request;

class ChatRoomsController extends Controller
{
    public function store(Request $request)
    {
        $result = $request->validate([
            'name' => 'required|string',
            'user_id' => 'required|integer',
        ]);

        $chatRoom = Chatroom::create($result);
        return redirect()->route('home');
    }

    public function destroy(ChatRoom $chatRoom)
    {
        $chatRoom->delete();
        return redirect()->route('home');
    }

    public function update(ChatRoom $chatRoom, Request $request)
    {
        $chatRoom->update(['name' => $request->name]);
        return back();
    }
}
