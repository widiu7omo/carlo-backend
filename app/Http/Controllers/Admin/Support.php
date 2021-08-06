<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class Support extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function view(Request $req)
    {
        $support = DB::table('support')
                    ->select('userid', DB::raw('SUM(CASE WHEN (replied = 0 AND is_staff = 0) THEN 1 ELSE 0 END) AS new'))
                    ->groupBy('userid');
        $data = DB::table('users')
                    ->joinSub($support, 'supp', function ($join) {
                        $join->on('users.userid', '=', 'supp.userid');
                    })
                    ->orderBy('new', 'desc')
                    ->paginate(6);
        return view('admin.support', compact('data'));
    }

    public function chat(Request $req)
    {
        $msgs = DB::table('support')->where('userid', $req->get('uid'))->orderBy('id', 'asc')->get();
        return ['msgs' => $msgs];
    }

    public function mark(Request $req)
    {
        $uid = $req->get('uid');
        DB::table('support')
            ->where('userid', $uid)
            ->where('is_staff', 0)
            ->where('replied', 0)
            ->update(['replied' => 1]);
        return ['msgs' => 'success'];
    }

    public function send(Request $req)
    {
        $uid = $req->json('uid');
        $msg = $req->json('msg');
        $now = Carbon::now();
        DB::table('support')->insert([
            'userid' => $uid,
            'message' => $msg,
            'is_staff' => 1,
            'date' => $now->timestamp,
            'updated_at' => $now->format('jS M Y h:m a')
        ]);
        DB::table('support')
            ->where('userid', $uid)
            ->where('is_staff', 0)
            ->where('replied', 0)
            ->update(['replied' => 1]);
        $msgs = DB::table('support')->where('userid', $uid)->orderBy('id', 'asc')->get();
        return ['msgs' => $msgs];
    }

    public function delFull(Request $req)
    {
        $uid = $req->post('id');
        DB::table('support')->where('userid', $uid)->delete();
        return back();
    }

    public function delSingle(Request $req)
    {
        $id = $req->get('id');
        $db = DB::table('support')->where('id', $id);
        $uid = $db->first()->userid;
        $db->delete();
        $msgs = DB::table('support')->where('userid', $uid)->orderBy('id', 'asc')->get();
        return ['msgs' => $msgs];
    }
}
