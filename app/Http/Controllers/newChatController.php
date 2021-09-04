<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Events\ReceiveNewChats;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class newChatController extends Controller
{
    public function checkIfChatExists(request $request)
    {
        $myId = $request->myId;
        $hisId = $request->hisId;
        $hisInfo = User::where('id', $hisId)->first();
        $chatRoom = Chat::Where(function($query) use ($myId,$hisId){
            $query->where('user1', '=', $myId)
                    ->where('user2', '=', $hisId);
            })
        ->orWhere(function($query) use ($myId,$hisId) {
            $query->where('user2', '=', $myId)
                    ->where('user1', '=', $hisId);
            })
        ->select('key')
        ->get();
        if (!$chatRoom || $chatRoom->count() == 0) {
            $key = Str::random(32);
            $chat = Chat::create([
                'key' => $key,
                'user1' => $myId,
                'user2' => $hisId,
            ]);
            Schema::create($key, function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->references('id')->on('users')->cascadeOnDelete();
                $table->text('content');
                $table->timestamps();
            });
            $identifier = "newChat".strval($hisId);
            broadcast(new ReceiveNewChats("$identifier",$key,$myId));
            return json_encode(['statusCode'=>'added','key'=>$key,'image'=>$hisInfo->image,'name'=>$hisInfo->name]);
        } else {
            return json_encode(['statusCode'=>'already Exists','key'=>$chatRoom,'image'=>$hisInfo->image,'name'=>$hisInfo->name]);
        }



        //if null create new key and that shit, if anything else go to that chat
        // if ($chatRoom == null) {
        // $key = $this->createNewKey($myId, $hisId);
        // ovo create novi table zove ga po keyu chata trbea nekako
        // napravit eloquent bond sa user_id-om za message sendera


        // Schema::create($key, function (Blueprint $table) {
        //     $table->increments('id');
        //     $table->foreignId('user_id');
        //     $table->text('content');
        //     $table->timestamps();
        // });


    }
}
