<?php
namespace App\Http\Controllers\Reel\Traits;

use App\Events\Heart;
use App\Events\Like;
use App\Events\View;
use App\Models\CampainLike;
use App\Models\CampainHeart;
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

    public function CampainToggleHeart(Request $request) {
        $validator = Validator::make($request->all(),  [
            'campain_id' => 'required|exists:campains,id',
        ]);
        if($validator->fails()){
            return $this->failure('Campain ID is missing.');
        }
        $add = CampainHeart::where(['campain_id' => $request->campain_id, 'user_id' => Auth::id()])->first();
        if(!empty($add)){
            $add->delete();
        }else{
            $add = new CampainHeart;
            $add->campain_id = $request->campain_id;
            $add->user_id = Auth::id();
            $add->save();
        }
        $hearts_count = CampainHeart::where('campain_id', $request->campain_id)->count();
        broadcast(new Heart($hearts_count));
        return $this->success('Heart Toggled Successfully.', [
            'love_count' => $hearts_count
        ]);

    }
    
    public function CampainToggleLike(Request $request) {
        $validator = Validator::make($request->all(),  [
            'campain_id' => 'required|exists:campains,id',
        ]);
        if($validator->fails()){
            return $this->failure('Campain ID is missing.');
        }
        $add = CampainLike::where(['campain_id' => $request->campain_id, 'user_id' => Auth::id()])->first();
        if(!empty($add)){
            $add->delete();
        }else{
            $add = new CampainLike;
            $add->campain_id = $request->campain_id;
            $add->user_id = Auth::id();
            $add->save();
        }
        $likes_count = CampainLike::where('campain_id', $request->campain_id)->count();
        broadcast(new Like($likes_count));
        return $this->success('Like Toggled Successfully.', [
            'likes_count' => $likes_count
        ]);

    }
    
}