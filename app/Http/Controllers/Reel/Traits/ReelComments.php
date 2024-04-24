<?php
namespace App\Http\Controllers\Reel\Traits;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Reel;
use App\Models\ReelComment;
use App\Http\Resources\ReelsComments;

trait ReelComments{

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
    
}