<?php

namespace App\Http\Middleware;

use App\Models\ChatRoom;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tightenco\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {

        $user = auth()->user();

        if ($user !== null) {
            $data = [
                'activeChatRoom' => $request->has('chatRoom') ? ChatRoom::with('messages')->where('id', $request->chatRoom)->first() : ChatRoom::with('messages')->where('user_id', $user->id)->latest()->first(),
                'chatRooms' => $user->chatRooms()->latest()->get(),
                'chatSettings' => $user->chatSettings()->select('model', 'temperature')->first(),
            ];
        } else {
            $data = null;
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'chat' => $data,
            'ziggy' => fn () => [
                ...(new Ziggy())->toArray(),
                'location' => $request->url(),
            ],
        ];
    }
}
