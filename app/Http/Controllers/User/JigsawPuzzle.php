<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use \Exception;
use Funcs;
use Cache;
use DB;

class JigsawPuzzle extends Controller
{
    public function cat(Request $req)
    {
        $user = $req['user'];
        $leaderboard = Funcs::rank($user->userid);
        return ['status' => 1, 'rank' => $leaderboard['rank'], 'score' => $leaderboard['score'], 'cost_p' => env('JPZ_PIECE_COST'), 'jpz' => DB::table('jpz_category')->get()];
    }
    
    public function get(Request $req)
    {
        try {
            $user = $req['user'];
            $id = (int) $req->get('id');
            $cat = DB::table('jpz_category')->where('id', $id)->first();
            if (!$cat) {
                return ['status' => 0, 'message' => 'Invalid category selected!'];
            }
            if ($user->balance < $cat->cost) {
                return ['status' => 10, 'message' => 'Insufficient balance!'];
            }
            $uDB = DB::table('jpz_player')->where('userid', $user->userid);
            $uCheck = $uDB->first();
            if ($uCheck) {
                if (Carbon::parse($uCheck->updated_at)->isToday()) {
                    $played = $uCheck->played + 1;
                } else {
                    $played = 1;
                }
                if ($played > (int) env('JPZ_MAX_ROUND')) {
                    return ['status' => 0, 'message' => 'Daily playing limit exceeded! Try again tomorrow.'];
                }
            } else {
                $played = 1;
            }
            $check = DB::table('jpz_image')->where('category', $id)->inRandomOrder()->first();
            if ($check) {
                $ran = Str::random(10);
                if ($uCheck) {
                    $uDB->update([
                    'played' => $played,
                    'category' => $id,
                    'item_id' => $check->id,
                    'rewarded' => 0,
                    'enc' => $ran,
                    'updated_at' => Carbon::now()
                ]);
                } else {
                    DB::table('jpz_player')->insert([
                    'userid' => $user->userid,
                    'played' => $played,
                    'category' => $id,
                    'item_id' => $check->id,
                    'rewarded' => 0,
                    'enc' => $ran,
                    'updated_at' => Carbon::now()
                ]);
                }
                Funcs::deductGamePoints($user, 'JigsawPuzzle', $cat->cost);
                return [
                'status' => 1,
                'id' => $check->id,
                'ran' => $ran,
                'time' => $cat->time * 1000,
                'row' => $cat->row,
                'col' => $cat->col,
                'img' => $check->image
            ];
            } else {
                return ['status' => 0, 'message' => 'Not available'];
            }
        } catch (Exception $e){
            return ['status' => 0, 'message' => $e->getMessage()];
        }
    }

    public function reward(Request $req)
    {
        $user = $req['user'];
        $data = base64_decode($req->json('id').'=');
        $data = Funcs::dec($data);
        $data = explode("|", $data);
        $pdb = DB::table('jpz_player')->where('userid', $user->userid);
        $check = $pdb->first();
        if ($check) {
            if ($data[1] != $check->enc || $check->enc == 'none') {
                return ['status' => 0, 'message' => 'Reward verification failed!'];
            }
            $id = (int) $data[0];
            if ($id != $check->item_id) {
                return ['status' => 0, 'message' => 'You cannot request reward for this puzzle!'];
            }
            $db = DB::table('jpz_category')->where('id', $check->category)->first();
            if ($db) {
                $diff = (int) Carbon::now()->diffInSeconds(Carbon::parse($check->updated_at));
                if ($diff > $db->time + (int) env('JPZ_TIME_OFFSET')) {
                    return ['status' => 0, 'message' => 'Times up!'];
                }
                if ($id == $check->item_id && $check->rewarded == 1) {
                    return ['status' => 0, 'message' => 'You already got rewarded for this puzzle!'];
                }
                DB::table('hist_game')->updateOrInsert(['userid' => $user->userid, 'game' => 'JigsawPuzzle'], ['points' => DB::raw("points + '$db->reward'")]);
                Funcs::leaderboard($user->name, $user->userid, $db->reward);
                $pdb->update(['enc' => 'none', 'rewarded' => 1]);
                return ['status' => 1, 'message' => $db->reward];
            } else {
                return ['status' => 0, 'message' => 'Category not found!'];
            }
        } else {
            return ['status' => 0, 'message' => 'First play the game then request for reward!'];
        }
    }

    public function help(Request $req)
    {
        $user = $req['user'];
        $cost = (int) env('JPZ_PIECE_COST');
        if ($user->balance < $cost) {
            return ['status' => 10, 'message' => 'Insufficient balance!'];
        }
        $bal = $user->balance - $cost;
        Funcs::deductGamePoints($user, 'JigsawPuzzle', $cost);
        return ['status' => 1, 'message' => 'Cost deducted'];
    }
}
