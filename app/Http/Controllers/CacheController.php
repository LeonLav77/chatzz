<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class CacheController extends Controller
{
        public function setCache(){
        $value = Cache::remember('users', 5, function () {
            return DB::table('users')->get();
        });
    }
    public function readCache(){
        $value = Cache::get('users');
        return json_encode($value);
    }
}
