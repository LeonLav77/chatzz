<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Events\SendMessage;
use Illuminate\Http\Request;
use App\Events\SendPrivateMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class BroadcastController extends Controller
{

    public function message(request $request)
    {
        if (Cache::has('messages')) {
            Cache::forget('messages');
        }
        Cache::forget('messages');
        $message = $request->message;
        $user = Auth::user();
        $id = $user->id;
        $username = $user->name;
        $picture = $user->image;
        broadcast(new SendMessage($message, $id, $username, $picture));
    }
    public function PrivateMessage(request $request)
    {
        $message = $request->message;
        $key = $request->key;
        if (Cache::has($key)) {
            Cache::forget($key);
        }
        $user = Auth::user();
        $id = $user->id;
        $username = $user->name;
        $picture = $user->image;
        broadcast(new SendPrivateMessage($message, $id, $username, $picture, $key));
    }
}
