<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class MessageController extends Controller
{
    public function getAll(Request $request)
    {
        $whereToStart = $request->whereToStart;
        $messages = Cache::rememberForever('messages', function () use($whereToStart) {
            return Message::includes()->orderBy('created_at','desc')->skip($whereToStart)->take(15)->get();
        });
        return json_encode($messages->reverse()->flatten());
    }
    public function getAllWithKey(request $request)
    {
        $key = $request->key;
        if (isset($request->whereToStart)) {
            $whereToStart = $request->whereToStart;
        }else{
            $whereToStart = 0;
        }
        if (!(Cache::has($key))) {
            $arrayForNewValues = Cache::rememberForever($key, function () use ($whereToStart, $key) {
                $arrForNewValues = array();
                $messages = DB::Table($key)->orderBy('created_at', 'desc')->skip($whereToStart)->take(15)->get();
                for ($x = 0; $x < count($messages); $x++) { # TREBA REFACTORAT UZASNO INEFFICIENT
                    $currentMessage = $messages[$x];
                    $values = DB::Table('users')->where('id', $currentMessage->user_id)->select('name', 'image')->first();
                    $name = $values->name;
                    $image = $values->image;
                    $currentMessage = (array) $currentMessage;
                    $currentMessage['username'] = $name;
                    $currentMessage['image'] = $image;
                    array_push($arrForNewValues, $currentMessage);
                }
                return $arrForNewValues;
            });
        }else{
            $arrayForNewValues = Cache::get($key);
        }
        return json_encode($arrayForNewValues);
    }
}
