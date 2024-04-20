<?php

namespace App\Http\Controllers\Reel;

use App\Models\Reel;
use App\Models\ReelView;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ReelsResource;
use App\Models\ReelCategory;
use App\Models\ReelComment;
use App\Models\ReelCountry;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\Foreach_;

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
            'categories'=>'required|json',
            'countries'=>'required|json'
        ]);
        if($validator->fails()){
            return $this->failure('Required field is missing.', $validator->errors());
        }
        $new = new Reel;
        $new->user_id = Auth::id();
        $new->title = $request->title;
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
        $categories = json_decode($request->categories, true);
        $countries = json_decode($request->countries, true);
        foreach($categories as $category){
            $newCategory = new ReelCategory();
            $newCategory->reel_id = $new->id;
            $newCategory->category_title = $category;
            $newCategory->save();
            if(!$newCategory->save()){
                return $this->failure('Something went wrong, Please try again later.');
            }
        }
        foreach($countries as $country){
            $newCountry = new ReelCountry();
            $newCountry->reel_id = $new->id;
            $newCountry->country_title = $country;
            $newCountry->save();
            if(!$newCountry->save()){
                return $this->failure('Something went wrong, Please try again later.');
            }
        }
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
        $categories = json_decode($request->categories, true);
        $countries = json_decode($request->countries, true);
        ReelCategory::where('reel_id', $update->id)->delete();
        foreach($categories as $category){
            $newCategory = new ReelCategory();
            $newCategory->reel_id = $update->id;
            $newCategory->category_title = $category;
            $newCategory->save();
            if(!$newCategory->save()){
                return $this->failure('Something went wrong, Please try again later.');
            }
        }
        ReelCountry::where('reel_id', $update->id)->delete();
        foreach($countries as $country){
            $newCountry = new ReelCountry();
            $newCountry->reel_id = $update->id;
            $newCountry->country_title = $country;
            $newCountry->save();
            if(!$newCountry->save()){
                return $this->failure('Something went wrong, Please try again later.');
            }
        }
        return $this->success('Reel Updated Successfully.');
    }

    // protected function reelsVideoUpdate(Request $request, $id) {
    //     $update = Reel::where('user_id', Auth::id())->find($id);
    //     if(empty($update)){
    //         return $this->failure('Reel Not Found.');
    //     }
    //     $update->video_manifest = $request->video_manifest;
    //     if(!$update->update()){
    //         return $this->failure('Something went wrong, Please try again later.');
    //     }
    //     return $this->success('Video Updated Successfully.');
    // }

    protected function reelsViewsUpdate(Request $request, $id) {
        $reel = Reel::find($id);
        if(empty($reel)){
            return $this->failure('Reel Not Found.');
        }
        $reel->increment('views');
        return $this->success('View Updated Successfully.');
    }

    protected function reelsClicksUpdate(Request $request, $id) {
        $reel = Reel::find($id);
        if(empty($reel)){
            return $this->failure('Reel Not Found.');
        }
        $reel->increment('clicks');
        return $this->success('Reel clicks Updated Successfully.', [
            'target_url' => $reel->target_url
        ]);
    }

    protected function reelsLikesUpdate(Request $request, $id) {
        $reel = Reel::find($id);
        if(empty($reel)){
            return $this->failure('Reel Not Found.');
        }
        $reel->increment('likes');
        return $this->success('Likes Updated Successfully.');
    }

    protected function reelsHeartsUpdate(Request $request, $id) {
        $reel = Reel::find($id);
        if(empty($reel)){
            return $this->failure('Reel Not Found.');
        }
        $reel->increment('hearts');
        return $this->success('Hearts Updated Successfully.');
    }

    protected function reelsCommentsList(Request $request, $id) {
        $reel = Reel::find($id);
        if(empty($reel)){
            return $this->failure('Reel Not Found.');
        }
        
    }

    protected function reelsCommentsDelete(Request $request, $id) {
        $reel = Reel::find($id);
        if(empty($reel)){
            return $this->failure('Reel Not Found.');
        }
        
    }

    protected function reelsCommentsAdd(Request $request, $id) {
        $validator = Validator::make($request->all(),  [
            'comment' => 'required|max:250',
        ]);
        if($validator->fails()){
            return $this->failure('Comment field is missing.');
        }
        $reel = Reel::find($id);
        if(empty($reel)){
            return $this->failure('Reel Not Found.');
        }
        $user_id = Auth::id();
        $new = new ReelComment;
        $new->user_id = $user_id;
        $new->reel_id = $reel->id;
        $new->comment = $request->comment;
        $new->save();
        return $this->success('Hearts Updated Successfully.');
    }

    protected function reelsDelete(Request $request, $id) {
        $user_id = Auth::id();
        $reel = Reel::where('user_id', $user_id)->find($id);
        if(empty($reel)){
            return $this->failure('Reel Not Found.');
        }
        $reel->delete();
        return $this->success('Reel Deleted successfully.');
    }
}
