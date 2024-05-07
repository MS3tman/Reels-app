<?php

namespace App\Http\Controllers;

use SwooleTW\Http\Websocket\Facades\Websocket;
use Illuminate\Http\Request;
use App\Events\Like;
use SwooleTW\Http\Websocket\Facades\Broadcast;


class TestController extends Controller
{
    public function test($websocket, $data){
	//$like = $request->number;
	Broadcast::to('demo')->emit('like', ['data' => $data]);
    }
    
    public function test1($websocket, $data){
	Broadcast::to('demo')->emit('views', ['data' => $data]);

    }
}
