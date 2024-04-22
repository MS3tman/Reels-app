<?php

namespace App\Http\Controllers\Reel;

use App\Models\Reel;
use App\Models\ReelLike;
use App\Models\ReelView;
use App\Models\ReelHeart;
use App\Models\ReelComment;
use App\Models\ReelCountry;
use App\Models\ReelCategory;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Foreach_;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ReelsComments;
use App\Http\Resources\ReelsResource;
use App\Models\Wishlist;
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
        $reelV = new ReelView;
        $reelV->user_id = Auth::id();
        $reelV->reel_id = $reel->id;
        $reelV->save();
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

    // protected function reelsLikesUpdate(Request $request, $id) {
    //     $reel = Reel::find($id);
    //     if(empty($reel)){
    //         return $this->failure('Reel Not Found.');
    //     }
    //     $reelL = new ReelLike;
    //     $reelL->user_id = Auth::id();
    //     $reelL->reel_id = $reel->id;
    //     $reelL->save();
    //     return $this->success('Likes Updated Successfully.');
    // }

    protected function reelsLikesUpdate(Request $request, $id) {
        $reel = Reel::find($id);
        if(empty($reel)){
            return $this->failure('Reel Not Found.');
        }
        $reelL = ReelLike::where('id', $id)->where('user_id', Auth::user()->id)->first();
        if(!empty($reelL)){
            $reelL->delete();
            return $this->success('Like Removed Successfully.');
        }
        $reelL = new ReelLike;
        $reelL->user_id = Auth::id();
        $reelL->reel_id = $id;
        $reelL->save();
        return $this->success('Like Created Successfully.');
    }

    protected function reelsHeartsUpdate(Request $request, $id) {
        $reel = Reel::find($id);
        if(empty($reel)){
            return $this->failure('Reel Not Found.');
        }
        $reelH = ReelHeart::where('id', $id)->where('user_id', Auth::user()->id)->first();
        if(!empty($reelH)){
            $reelH->delete();
            return $this->failure('Heart Removed Successfully.');
        }
        $reelH = new ReelHeart;
        $reelH->user_id = Auth::id();
        $reelH->reel_id = $id;
        $reelH->save();
        return $this->success('Hearts Created Successfully.');
    }

    protected function reelsCommentsList(Request $request, $reelId) {
        $reel = Reel::find($reelId);
        if(empty($reel)){
            return $this->failure('Reel Not Found.');
        }
        $comments = ReelComment::where('reel_id', $reel->id)->paginate(10);
        return ReelsComments::collection($comments);
    }

    protected function reelsCommentsDelete(Request $request, $reelId, $id) {
        $del = ReelComment::where(['reel_id' => $reelId, 'id' => $id])->delete();
        if(!$del){
            return $this->failure('Comment is Not Exists.');
        }
        return $this->success('Comment Deleted Successfully.');
    }

    protected function reelsCommentsAdd(Request $request, $reelId) {
        $validator = Validator::make($request->all(),  [
            'comment' => 'required|max:250',
        ]);
        if($validator->fails()){
            return $this->failure('Comment field is missing.');
        }
        $reel = Reel::find($reelId);
        if(empty($reel)){
            return $this->failure('Reel Not Found.');
        }
        $user_id = Auth::id();
        $new = new ReelComment;
        $new->user_id = $user_id;
        $new->reel_id = $reel->id;
        $new->comment = $request->comment;
        $new->save();
        return $this->success('Comment Added Successfully.');
    }

    protected function reelsWishlistUpdate(Request $request, $id){
        $reel = Reel::find($id);
        if(empty($reel)){
            return $this->failure('Reel Not Found.');
        }
        $wishlist = Wishlist::where('id', $id)->where('user_id', Auth::user()->id)->first();
        if(!empty($wishlist)){
            $wishlist->delete();
            return $this->failure('Reel Removed from Wishlist.');
        }
        $wishlist = new Wishlist();
        $wishlist->user_id = Auth::id();
        $wishlist->reel_id = $id;
        $wishlist->save();
        return $this->success('Reel Added Successfully in Wishlist.');
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
