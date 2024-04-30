<?php
namespace App\Http\Controllers\Reel\Traits;

use App\Models\Fav;
use App\Models\ReelLike;
use App\Models\ReelLove;
use App\Events\Heart;
use App\Events\Like;
use App\Events\View;
use App\Models\CampainViews;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

trait ReelAccessors{

    public function CampainAddViews(Request $request) {
        $validator = Validator::make($request->all(),  [
            'campain_id' => 'required|exists:campains,id',
        ]);
        if($validator->fails()){
            return $this->failure('Campain ID is missing.');
        }
        $add = CampainViews::where(['campain_id' => $request->campain_id, 'user_id' => Auth::id()])->first();
        if(!empty($add)){
            $add->increment('count');
            $add->update();
        }else{
            $add = new CampainViews;
            $add->campain_id = $request->campain_id;
            $add->user_id = Auth::id();
            $add->count = 1;
            $add->save();
        }
        $campain_views = CampainViews::where('campain_id', $request->campain_id)->sum('count');
        broadcast(new View($campain_views));
        return $this->success('View Added Successfully.', [
            'campain_views' => $campain_views
        ]);
    }

    public function ReeTogglelLove(Request $request) {
        $validator = Validator::make($request->all(),  [
            'reel_id' => 'required|exists:reels,id',
        ]);
        if($validator->fails()){
            return $this->failure('Reel ID is missing.');
        }
        $add = ReelLove::where(['reel_id' => $request->reel_id, 'user_id' => Auth::id()])->first();
        if(!empty($add)){
            $add->delete();
        }else{
            $add = new ReelLove;
            $add->reel_id = $request->reel_id;
            $add->user_id = Auth::id();
            $add->save();
        }
        $hearts_count = ReelLove::where('reel_id', $request->reel_id)->count();
        broadcast(new Heart($hearts_count));
        return $this->success('Love Toggled Successfully.', [
            'love_count' => $hearts_count
        ]);

    }
    
    public function ReelToggleLike(Request $request) {
        $validator = Validator::make($request->all(),  [
            'reel_id' => 'required|exists:reels,id',
        ]);
        if($validator->fails()){
            return $this->failure('Reel ID is missing.');
        }
        $add = ReelLike::where(['reel_id' => $request->reel_id, 'user_id' => Auth::id()])->first();
        if(!empty($add)){
            $add->delete();
        }else{
            $add = new ReelLike;
            $add->reel_id = $request->reel_id;
            $add->user_id = Auth::id();
            $add->save();
        }
        $likes_count = ReelLike::where('reel_id', $request->reel_id)->count();
        broadcast(new Like($likes_count));
        return $this->success('Like Toggled Successfully.', [
            'likes_count' => $likes_count
        ]);

    }
    
    public function ReelToggleFavourite(Request $request) {
        $validator = Validator::make($request->all(),  [
            'reel_id' => 'required|exists:reels,id',
        ]);
        if($validator->fails()){
            return $this->failure('Reel ID is missing.');
        }
        $add = Fav::where(['reel_id' => $request->reel_id, 'user_id' => Auth::id()])->first();
        if(!empty($add)){
            $add->delete();
        }else{
            $add = new Fav;
            $add->reel_id = $request->reel_id;
            $add->user_id = Auth::id();
            $add->save();
        }
        return $this->success('Favourite Toggled Successfully.', );

    }
    
}