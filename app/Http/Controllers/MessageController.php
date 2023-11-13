<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function store(Request $request)
    {
        // dd($request);
        $result = $request->validate([
          'chat_room_id' => 'required',
          'content' => 'required',
          'role' => 'required',
        ]);

        
        $result['user_id'] = auth()->id();
        Message::create($result);
        return;
    }
}
