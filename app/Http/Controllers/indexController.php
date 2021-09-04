<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class indexController extends Controller
{
    public function new()
        {
            if (Auth::check()) {
                $user = Auth::user();
                $id = $user->id;
                $myChats = $this->findChats($user);
            }else{
                $myChats = [];
                $id = 0;
            }
            $people = User::whereNotIn('id', [$id])->get();
            return view('newChatTemplate', ['isAuth' => $user, 'people' => $people,'myChats' => $myChats,'id' => $id]);
        }

    public function findChats($user){
        $myChats = $user->chats1;
        $myChatsWhereHeInitiated = $user->chats2;
        foreach ($myChatsWhereHeInitiated as $key => $value) {
            $myChats[$key] = $value;
        }
        return $myChats;
    }


    public function checkIfLoggedIn(){
        if (Auth::check()) {
            return json_encode(Auth::User()->id);
        }else{
            return json_encode('NOT LOGGED IN');
        }
    }

    public function getMyChats(request $request){
        $id = $request->id;
        return json_encode($this->getMyChatsByID($id));
    }

    public function getMyChatsByID($id){
        $user = User::find($id);
        $myChats = $this->findChats($user);
        return json_encode($myChats);
    }

    public function lastMessage(request $request)  {
        $key = $request->key;
        $lastMessage = DB::table($key)->orderBy('created_at', 'desc')->first();
        if (!($lastMessage)){
            return json_encode('No Messages Yet');
        }
        $username = User::find($lastMessage->user_id)->name;
        $lastMessage->username = $username;
        return json_encode($lastMessage);
    }
}
