<?php
namespace App\Http\Controllers\Reel\Traits;
use App\Models\Reel;
use App\Models\ReelComment;
use App\Models\CommentHeart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ReelsComments;
use Illuminate\Support\Facades\Validator;

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
        $del = ReelComment::where(['reel_id' => $reelId, 'id' => $id, 'user_id' => Auth::id()])->delete();
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
        $new = new ReelComment;
        $new->user_id = Auth::id();
        $new->reel_id = $reel->id;
        $new->comment = $request->comment;
        $new->save();
        return $this->success('Comment Added Successfully.');
    }

    public function CommentToggleHeart(Request $request, $reelId, $id) {
        $reel = Reel::find($reelId);
        if(empty($reel)){
            return $this->failure('Reel Not Found.');
        }
        $add = CommentHeart::where(['reel_id' => $request->reel_id, 'id' => $id, 'user_id' => Auth::id()])->first();
        if(!empty($add)){
            $add->delete();
        }else{
            $add = new CommentHeart;
            $add->reel_id = $request->reel_id;
            $add->user_id = Auth::id();
            $add->save();
        }
        $hearts_count = CommentHeart::where(['reel_id' => $request->reel_id, 'id' => $id ])->count();
        return $this->success('Heart Toggled Successfully.', [
            'love_count' => $hearts_count
        ]);

    }
    
}