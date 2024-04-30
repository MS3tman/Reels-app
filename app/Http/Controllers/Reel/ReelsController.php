<?php

namespace App\Http\Controllers\Reel;

use Carbon\Carbon;
use App\Models\Reel;
use App\Models\Coupon;
use App\Models\Campain;
use App\Services\FileHandle;
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
use App\Models\CouponCode;

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
            'countries'=>'nullable|array',

            'coupon_name'=>'nullable|string',
            'coupon_discount'=>'required_if:coupon_name,*|integer',
            'coupon_locations'=>'required_if:coupon_name,*|array',
            'coupon_expire_date'=>'required_if:coupon_name,*|date',
            'coupon_count'=>'required_if:coupon_name,*|integer',
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
            if($request->has('logo')){
                $imagePath = (new FileHandle())->storeImage($request->logo, 'reels');
                $new->logo = $imagePath;
            }
            $new->target_url = $request->target_url;
            $new->save();
    
            $new->categories()->sync((array)$request->categories);
            $new->countries()->sync((array)$request->countries);
    
            $campain = new Campain;
            $campain->reel_id = $new->id;
            $campain->target_views = $request->target_views;
            $campain->price = view_price();
            $campain->copoun_code = $request->copoun_code;
            $campain->copoun_per = $request->copoun_per;
            $expire_date = Carbon::parse($request->expire_date);
            $campain->expire_date = $expire_date->format('Y-m-d');
            $campain->status = 1;
            $campain->save();

            if($request->filled('coupon_name')){
                $coupon = new Coupon;
                $coupon->campain_id = $campain->id;
                $coupon->name = $request->coupon_name;
                $coupon->discount = $request->coupon_discount;
                $coupon->locations = json_encode($request->coupon_locations);
                $coupon_expire_date = Carbon::parse($request->coupon_expire_date);
                $coupon->expire_date = $coupon_expire_date->format('Y-m-d');
                $coupon->count = $request->coupon_count;
                $coupon->price = coupon_price();
                $coupon->save();
                if($coupon->count){
                    for($i=0;$i<$coupon->count;$i++){
                        $code = new CouponCode;
                        $code->coupon_id = $coupon->id;
                        $code->code = generateRandomCoupon();
                        $code->save();
                    }
                }
            }
    
            DB::commit();
        }catch(QueryException $e){
            return $this->failure('Please make sure to add valid data.', $request->has('sql_error') ? $e->getMessage() : '');
        }
            
        return $this->success('Reel added Successfully.');
    }

    public function ReelUpdateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(),  [
            'status' => 'required|integer',
        ]);
        if($validator->fails()){
            return $this->failure('Status field is missing.');
        }
        $campain = Campain::find($id);
        if(empty($campain)){
            return $this->failure('Campain Not Found.');
        }
        $campain->status = (int)$request->status;
        $campain->update();
        return $this->success('Campain updated Successfully.');
    }
    
}