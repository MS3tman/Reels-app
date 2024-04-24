<?php

namespace App\Http\Controllers\Reel;

use App\Models\Reel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Reel\Traits\ReelAccessors;
use App\Http\Controllers\Reel\Traits\ReelComments;
use App\Http\Controllers\Reel\Traits\ReelVideo;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ReelsResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ReelsController extends Controller
{
    
    

    protected function reelsList(Request $request) {
        $reels = Reel::where('status', true)->paginate(10);
        return ReelsResource::collection($reels);
    }

    protected function reelsListForUser(Request $request) {
        $user_id = Auth::id();
        $reels = Reel::where('user_id', $user_id)->paginate(10);
        return ReelsResource::collection($reels);
    }

    protected function reelsById(Request $request, $id) {
        $reel = Reel::where('status', true)->find($id);
        if(empty($reel)){
            return $this->failure('Reel Not Found.');
        }
        return new ReelsResource($reel);
    }

    protected function reelsByIdForUser(Request $request, $id) {
        $user_id = Auth::id();
        $reel = Reel::where('user_id', $user_id)->find($id);
        if(empty($reel)){
            return $this->failure('Reel Not Found.');
        }
        return new ReelsResource($reel);
    }

    protected function reelsStore(Request $request) {
        $validator = Validator::make($request->all(),  [
            'title' => 'required|string',
            'categories'=>'nullable|array',
            'countries'=>'nullable|array'
        ]);
        if($validator->fails()){
            return $this->failure('Required field is missing.', $validator->errors());
        }
        DB::transaction(function ()use($request) {
            $new = new Reel;
            $new->user_id = Auth::id();
            $new->title = $request->title;
            $new->company_name = $request->company_name;
            $new->logo = $request->logo;
            $new->target_url = $request->target_url;
            $new->target_views = $request->target_views;
            $new->price = $request->price;
            $new->offer_type = $request->offer_type;
            $new->offer = $request->offer;
            //$new->video_manifest = $request->video_manifest;
            $new->status = true;
            if(!$new->save()){
                return $this->failure('Something went wrong, Please try again later.');
            }
            if(!empty($request->categories)){
                $new->categories()->sync((array)$request->categories);
            }
            if(!empty($request->countries)){
                $new->countries()->sync((array)$request->countries);
            }
        });
        return $this->success('Reel added Successfully.');
    }

    protected function reelsUpdate(Request $request, $id) {
        $validator = Validator::make($request->all(),  [
            'title' => 'required|string',
        ]);
        if($validator->fails()){
            return $this->failure('Required field is missing.', $validator->errors());
        }
        $update = Reel::where('user_id', Auth::id())->find($id);
        if(empty($update)){
            return $this->failure('Reel Not Found.');
        }
        $update->title = $request->title;
        $update->company_name = $request->company_name;
        $update->logo = $request->logo;
        $update->target_url = $request->target_url;
        $update->target_views = $request->target_views;
        $update->price = $request->price;
        $update->offer_type = $request->offer_type;
        $update->offer = $request->offer;
        //$update->video_manifest = $request->video_manifest;
        $update->status = true;
        if(!$update->update()){
            return $this->failure('Something went wrong, Please try again later.');
        }
        if(!empty($request->categories)){
            $update->categories()->detach();
            $update->categories()->sync((array)$request->categories);
        }
        if(!empty($request->countries)){
            $update->countries()->detach();
            $update->countries()->sync((array)$request->countries);
        }
        return $this->success('Reel Updated Successfully.');
    }

    protected function reelsDelete(Request $request, $id) {
        $user_id = Auth::id();
        $reel = Reel::where('user_id', $user_id)->find($id);
        if(empty($reel)){
            return $this->failure('Reel Not Found.');
        }
        $reelHls = $reel->video_manifest;
        $reel->delete();
        Storage::deleteDirectory($reelHls);
        return $this->success('Reel Deleted successfully.');
    }
}
