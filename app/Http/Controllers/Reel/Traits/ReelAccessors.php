<?php
namespace App\Http\Controllers\Reel\Traits;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Reel;
use App\Models\ReelLike;
use App\Models\ReelView;
use App\Models\ReelHeart;
use App\Models\Wishlist;
trait ReelAccessors{

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
    }    protected function reelsLikesUpdate(Request $request, $id) {
        $reel = Reel::find($id);
        if(empty($reel)){
            return $this->failure('Reel Not Found.');
        }
        $reelAction = ReelLike::firstOrNew(['reel_id' => $id, 'user_id' => Auth::id()]);
        if(!$reelAction->exists){
            $reelAction->save();
            $action = 'Like';
        }else{
            $reelAction->delete();
            $action = 'Unlike';
        }
        return $this->success($action . ' Done Successfully.');
    }

    protected function reelsHeartsUpdate(Request $request, $id) {
        $reel = Reel::find($id);
        if(empty($reel)){
            return $this->failure('Reel Not Found.');
        }
        $reelAction = ReelHeart::firstOrNew(['reel_id' => $id, 'user_id' => Auth::id()]);
        if(!$reelAction->exists){
            $reelAction->save();
            $action = 'Heart';
        }else{
            $reelAction->delete();
            $action = 'UnHeart';
        }
        return $this->success($action . ' Done Successfully.');
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
    
}