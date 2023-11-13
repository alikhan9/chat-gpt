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
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'chat' =>
                auth()->user() != null ? [
                'activeChatRoom' => $request->has('chatRoom') == true ? ChatRoom::with('messages')->where('id', $request->chatRoom)->first() : ChatRoom::with('messages')->where('user_id', auth()->id())->latest()->first(),
                'chatRooms' => auth()->user()->chatRooms()->latest()->get()]
                : null
            ,
            'ziggy' => fn () => [
                ...(new Ziggy())->toArray(),
                'location' => $request->url(),
            ],
        ];
    }
}
