<?php

namespace App\Http\Controllers\Reel;

use Carbon\Carbon;
use App\Models\Reel;
use App\Models\Coupon;
use App\Models\Campain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ReelsResource;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Reel\Traits\ReelVideo;
use App\Http\Controllers\Reel\Traits\ReelComments;
use App\Http\Controllers\Reel\Traits\ReelAccessors;

class ReelsController extends Controller
{
    use ReelComments, ReelAccessors, ReelVideo;

    public function reelList(Request $request) {
        $reels = Campain::with('reel')->where('status', 1)->whereDate('expire_date', '>', now()->toDateString())->paginate(10);
        return ReelsResource::collection($reels);
    }

    public function ReelAddNew(Request $request)
    {
        $validator = Validator::make($request->all(),  [
            'title' => 'required|string',
            'target_url' => 'required|url',
            'company_name' => 'required|string',
            'target_views' => 'required|integer',
            'copoun_per' => 'nullable|integer',
            'copoun_code' => 'nullable|string',
            'expire_date' => 'required|date',
            'categories'=>'nullable|array',
            'countries'=>'nullable|array'
        ]);
        if($validator->fails()){
            return $this->failure('Required fields is missing.', $validator->errors());
        }
            
        try{

            DB::beginTransaction();
            $new = new Reel;
            $new->user_id = Auth::id();
            $new->title = $request->title;
            $new->company_name = $request->company_name;
            $new->logo = $request->logo;
            $new->target_url = $request->target_url;
            $new->save();
    
            $campain = new Campain;
            $campain->reel_id = $new->id;
            $campain->target_views = $request->target_views;
            $campain->price = view_price();
            $campain->copoun_per = $request->copoun_per;
            $campain->copoun_code = $request->copoun_code;
            $expire_date = Carbon::parse($request->expire_date);
            $campain->expire_date = $expire_date->format('Y-m-d');
            $campain->status = 0;
            $campain->save();
    
            $new->categories()->sync((array)$request->categories);
            $new->countries()->sync((array)$request->countries);
    
            DB::commit();
        }catch(QueryException $e){
            return $this->failure('Please make sure to add valid data.');
        }
            
        return $this->success('Reel added Successfully.');
    }

    public function ReelAddNewCampain(Request $request) 
    {
        $validator = Validator::make($request->all(),  [
            'reel_id' => 'required|integer',
            'target_views' => 'required|integer',
            'expire_date' => 'required|date',
            'copoun_per' => 'nullable|integer',
            'copoun_code' => 'nullable|string',
        ]);
        if($validator->fails()){
            return $this->failure('Required fields is missing.', $validator->errors());
        }

        $

        $campain = new Campain;
        $campain->reel_id = $request->reel_id;
        $campain->target_views = $request->target_views;
        $campain->price = view_price();
        $campain->copoun_per = $request->copoun_per;
        $campain->copoun_code = $request->copoun_code;
        $expire_date = Carbon::parse($request->expire_date);
        $campain->expire_date = $expire_date->format('Y-m-d');
        $campain->status = 0;
        $campain->save();
    }

    public function ReelAddNewCoupon(Request $request) 
    {
        $validator = Validator::make($request->all(),  [
            'campain_id' => 'required|exists:campains,id',
            'name' => 'required|string',
            'discount' => 'required|numeric',
            'locations' => 'required|array',
            'expire_date' => 'required|date',
            'count' => 'required|integer',
        ]);
        if($validator->fails()){
            return $this->failure('Required fields is missing.', $validator->errors());
        }
        //campain_id-name-discount-locations-expire_date-count-price
        $coupon = new Coupon;
        $coupon->campain_id = $request->campain_id;
        $coupon->name = $request->name;
        $coupon->discount = $request->discount;
        $coupon->locations = json_encode($request->locations);
        $coupon->expire_date = $request->expire_date;
        $coupon->count = $request->count;
        $coupon->price = coupon_price();
        if($coupon->save()){
            return $this->success('Coupon added Successfully.');
        }
        return $this->failure('Somethig went wrong, please try again later.');
    }
}