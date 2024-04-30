<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReelsResource;
use App\Http\Resources\StatisticsResource;
use App\Models\CampainHeart;
use App\Models\CampainLike;
use App\Models\CampainViews;
use App\Models\Reel;
use App\Models\ReelComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatisticsController extends Controller
{
    public function info(){
        $totalReels = Reel::where('user_id', Auth::id())->pluck('id');
        if(!$totalReels->exists()){
            return $this->failure('not found any reel for this user');
        }
        $statistics = [
            'totalLikes' => CampainLike::where('reel_id', $totalReels)->count(),
            'totalViews' => CampainViews::where('reel_id', $totalReels)->count(),
            'totalComments' => ReelComment::where('reel_id', $totalReels)->count(),
            'totalHearts' => CampainHeart::where('reel_id', $totalReels)->count(),
        ];
        //return $this->success('', ['statistics'=>$statistics]);
        return new StatisticsResource($statistics);
    }

    public function reel(){
        $reels = Reel::where('user_id', Auth::id())->paginate(10);
        return ReelsResource::collection($reels);
    }
}
