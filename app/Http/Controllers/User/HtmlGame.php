<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Carbon\Carbon;
use Funcs;
use File;
use DB;

class HtmlGame extends Controller
{
    public function get(Request $req)
    {
        $limit = $req->get('limit');
        $blocked = \Funcs::getMisc('blocked_hosts');
        if (is_numeric($limit)) {
            return ['status' => 1, 'bk' => $blocked,'data' => DB::table('html_game')->inRandomOrder()->limit($limit)->get()];
        } else {
            return ['status' => 1, 'bk' => $blocked, 'data' => DB::table('html_game')->get()];
        }
    }

    public function rwd(Request $req)
    {
        $user = $req['user'];
        try {
            $rw = (int) env('HTML_GAME_REWARD');
            if ($rw == 0) {
                return ['status' => 0, 'message' => 'Disabled'];
            }
            $data = explode("|", Funcs::dec($req->json('d')));
            if ($data[3] != $user->userid) {
                return ['status' => 0, 'message' => 'Reward void!'];
            }
            $timestamp = Carbon::now()->timestamp;
            $bTime = (int)$data[2];
            if (abs($timestamp - $bTime) > 10) {
                return ['status' => 0, 'message' => 'Verification failed!'];
            }
            $played_time = (int) $data[1];
            if ($timestamp - (int) $data[0] < $played_time) {
                return ['status' => 0, 'message' => 'System cannot reward for now!'];
            }
            $amt = floor($played_time / (int) env('HTML_GAME_DURATION')) * $rw;
            if ($amt > 0) {
                $db = DB::table('hist_game')->where('userid', $user->userid)->where('game', 'HtmlGame');
                $check = $db->first();
                $date_time = Carbon::createFromTimestamp($bTime);
                if ($check) {
                    if ($date_time == $check->updated_at) {
                        return ['status' => 0, 'message' => 'Duplicate request!'];
                    }
                    $db->update([
                        'points' => $check->points + $amt,
                        'updated_at' => $date_time
                    ]);
                } else {
                    DB::table('hist_game')->insert([
                        'userid' => $user->userid,
                        'game' => 'HtmlGame',
                        'points' => $amt,
                        'updated_at' => $date_time
                    ]);
                }
                $user->increment('balance', $amt);
                if (env('LEADERBOARD_INCLUDE_REWARD') == 1) {
                    Funcs::leaderboard($user->name, $user->userid, $amt);
                }
                return ['status' => 1, 'message' => 'You received ' . $amt . ' ' . strtolower(env('CURRENCY_NAME') . 's for playing')];
            }
            return ['status' => 0, 'message' => 'Nothing to reward!'];
        } catch (\Exception $e) {
            return ['status' => 0, 'message' => 'Invalid request'];
        }
    }

    public function pack(Request $req)
    {
        $filename = storage_path('games/files/'.$req->get('dl').'.pack');
        if (file_exists($filename)) {
            return response()->download($filename);
        } else {
            return response('Not found!', 404);
        }
    }

    public function image(Request $req)
    {
        $path = storage_path('games/images/' . $req->get('img'));
        if (File::exists($path)) {
            $file = File::get($path);
            $type = File::mimeType($path);
            $response = \Response::make($file, 200);
            $response->header("Content-Type", $type);
            return $response;
        } else {
            return response('Not found!', 404);
        }
    }
}