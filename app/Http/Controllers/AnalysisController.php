<?php

namespace App\Http\Controllers;

use App\Http\Resources\StatisticsResource;
use App\Models\CampainHeart;
use App\Models\CampainLike;
use App\Models\CampainViews;
use App\Models\Reel;
use App\Models\ReelComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AnalysisController extends Controller
{
    public function info($reelId){
        $reel = Reel::find($reelId);
        if(!$reel->exists()){
            return $this->failure('Reel not found');
        }
        $statistics = [
            'totalLike' => CampainLike::where('reel_id', $reelId)->count(),
            'totalViews' => CampainViews::where('reel_id', $reelId)->count(),
            'totalComment' => ReelComment::where('reel_id', $reelId)->count(),
            'TotalHeart' => CampainHeart::where('reel_id', $reelId)->count(),
        ];
        return new StatisticsResource($statistics);
    }

    public function newCampain(Request $request){
        $validator = Validator::make($request->all(), [
            // campains table
            'reel_id'=>'required',
            'coupon_code'=>'nullable',
            'coupon_per'=>'nullable',
            'target_views'=>'required',
            'price'=>'required',
            'expire_date'=>'required',

            // coupons table
            'coupon_name'=>'required',
            'coupon_discount'=>'required',
            'locations'=>'required|json',
            'expire_date'=>'required',
            'count'=>'required',
            'price'=>'required',
        ]);
        if($validator->fails()){
            return $this->failure($validator->errors());
        }
        
    }
}
