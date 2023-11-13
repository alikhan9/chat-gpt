<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatSettingsController extends Controller
{
    public function update(Request $request)
    {
        $result = $request->validate([
         'model' => 'required|string',
          'temperature' => 'required|numeric',
        ]);

        auth()->user()->chatSettings->update($result);

        return back();
    }

}
