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
        ]);

        $result['user_id'] = auth()->user()->id;

        Chatroom::create($result);
        return redirect()->route('home');
    }

    public function destroy(ChatRoom $chatRoom)
    {
        if($chatRoom->userId != auth()->id()) {
            return;
        }
        $chatRoom->delete();
        return redirect()->route('home');
    }

    public function update(ChatRoom $chatRoom, Request $request)
    {
        if($chatRoom->userId != auth()->id()) {
            return;
        }
        $chatRoom->update(['name' => $request->name]);
        return back();
    }
}
