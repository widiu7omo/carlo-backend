<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use \Exception;
use Validator;
use Cache;
use DB;

class Support extends Controller
{
    public function get(Request $req)
    {
        $user = $req['user'];
        $db = DB::table('support')->where('userid', $user->userid)->orderby('id', 'asc');
        $data = $db->get(['message','date','is_staff']);
        $db->where('is_staff', 1)->where('replied', 0)->update(['replied' => 1]);
        return ['status' => 1, 'message' => $data];
    }

    public function post(Request $req)
    {
        $validate = Validator::make($req->all(), [
            'msg' => 'required|string|min:10|max:500'
        ], [
            'required' => 'You did not enter any message.',
            'string' => 'Message hast to be readable characters.',
            'min' => 'Write at lease 10 characters long message.',
            'max' => 'Maximum 500 characters can be used per message.',
        ]);
        if ($validate->fails()) {
            return ['status' => 0, 'message' => $validate->errors()->first()];
        }
        $msg = $req->json('msg');
        $user = $req['user'];
        $check = DB::table('support')
                ->where('userid', $user->userid)
                ->where('is_staff', 0)
                ->where('replied', 0)
                ->count();
        if ($check > 2) {
            return ['status' => 0, 'message' => 'Wait until a staff reply your earlier messages.'];
        }
        $now = Carbon::now();
        DB::table('support')->insert([
            'userid' => $user->userid,
            'message' => $msg,
            'date' => $now->timestamp,
            'updated_at' => $now->format('jS M Y h:m a')

        ]);
        return ['status' => 1, 'message' => 'Request submitted.'];
    }
}
