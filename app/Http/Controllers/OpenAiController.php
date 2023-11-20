<?php

namespace App\Http\Controllers;

use App\Events\SendChunkOfGptResponse;
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

        $temp = '';

        foreach ($stream as $response) {
            $temp .= $response->choices[0]->delta->content;

            if (strlen($temp) >= 20) {
                event(new SendChunkOfGptResponse($response->choices[0]->finishReason, $temp));
                $temp = '';
            }
        }
        if (!empty($temp)) {
            event(new SendChunkOfGptResponse('Stop', $temp));
        }
    }
}
