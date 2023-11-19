<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;

class OpenAiController extends Controller
{
    public function streamOpenAI(Request $request)
    {
        $messages = json_decode($request->input('messages'), true);
        $stream = OpenAI::chat()->createStreamed([
            'model' => $request->input('model'),
            'temperature' => floatval($request->input('temperature')),
            'messages' => $messages,
        ]);

        return response()->stream(function () use ($stream) {
            foreach ($stream as $response) {
                echo "\n" .json_encode($response->choices[0]->toArray()) . "\n";
                ob_flush();
                flush();
            }
        }, 200, ['Content-Type' => 'text/event-stream']);
    }
}
