<?php

use Inertia\Inertia;
use App\Events\Hello;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\CacheController;
use App\Http\Controllers\indexController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\newChatController;
use App\Http\Controllers\BroadcastController;
use App\Http\Controllers\ReactAuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::post('/message', [BroadcastController::class, 'message']);

Route::get('/checkIfLoggedIn', [IndexController::class, 'checkIfLoggedIn']);

Route::get('/getMyChats', [IndexController::class, 'getMyChats']);

Route::view('/APITester', 'APITester');

Route::get('/new', [indexController::class, 'index']);


Route::post('/privateMessage', [BroadcastController::class, 'privateMessage']);

Route::post('/checkIfChatExists', [newChatController::class, 'checkIfChatExists']);

Route::get('/getMessage', [MessageController::class, 'getAll']);


Route::get('/setCache', [CacheController::class, 'setCache']);

Route::get('/readCache', [CacheController::class, 'readCache']);



Route::view('/noAccess', 'noAccess');

Route::get('/getMessageWithKey', [MessageController::class, 'getAllWithKey']);

Route::get('/lastMessage', [IndexController::class, 'lastMessage']);

Route::group(['middleware' => ['protectedPage']], function () {
    Route::get('/', [indexController::class, 'new']);
});

require __DIR__ . '/auth.php';