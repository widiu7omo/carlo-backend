<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Funcs;
use Carbon\Carbon;
use DB;

class Scratcher extends Controller
{
    public function cat(Request $req)
    {
        $user = $req['user'];
        $cards = DB::table('scratcher_game')->get();
        $data = [];
        for ($i = 0; $i < count($cards); $i++) {
            $uCards = DB::table('scratcher_player')
                        ->where('card_id', $cards[$i]->id)
                        ->where('userid', $user->userid)
                        ->where('expiry', '>', Carbon::now())
                        ->get();
            if (count($uCards) == 0) {
                array_push($data, [
                    'id' => $cards[$i]->id,
                    'name' => $cards[$i]->name,
                    'cost' => $cards[$i]->cost,
                    'icon' => $cards[$i]->card,
                    'bg' => $cards[$i]->image,
                    'coord' => $cards[$i]->coord,
                    'purchase' => $cards[$i]->can_purchase,
                    'cards' => []
                ]);
            } else {
                foreach ($uCards as $c) {
                    $found = false;
                    for ($j = 0; $j < count($data); $j++) {
                        if ($data[$j]['id'] == $c->card_id) {
                            $found = true;
                            array_push($data[$j]['cards'], [
                                'id' => $c->id,
                                'created_at' => Carbon::parse($c->created_at)->timestamp * 1000,
                                'expiry' => Carbon::parse($c->expiry)->timestamp * 1000
                            ]);
                        }
                    }
                    if (!$found) {
                        array_push($data, [
                            'id' => $cards[$i]->id,
                            'name' => $cards[$i]->name,
                            'cost' => $cards[$i]->cost,
                            'icon' => $cards[$i]->card,
                            'bg' => $cards[$i]->image,
                            'coord' => $cards[$i]->coord,
                            'purchase' => $cards[$i]->can_purchase,
                            'cards' => [array(
                                            'id' => $c->id,
                                            'created_at' => Carbon::parse($c->created_at)->timestamp * 1000,
                                            'expiry' => Carbon::parse($c->expiry)->timestamp * 1000
                                        )]
                        ]);
                    }
                }
            }
        }
        return ['status' => 1, 'balance' => $user->balance,'data' => $data];
    }

    public function result(Request $req)
    {
        $user = $req['user'];
        $db = DB::table('scratcher_player')->where('id', $req->get('id'));
        $c = $db->first();
        if ($c) {
            $check = DB::table('scratcher_game')->where('id', $c->card_id)->first();
            if (!$check) {
                $db->delete();
                return ['status' => 0, 'message' => 'Card not found!'];
            }
            $won = [];
            $count = 9;
            $free = 0;
            $max = $check->max - $check->min;
            $max = floor(((($check->max / 100 * 90) * (9 - $check->difficulty)) / 9) + ($check->max / 100 * 10));
            $max = rand($check->min, ($max + $check->min));
            for ($i = 0; $i < 3; $i++) {
                if (rand(1, 5 * ($check->difficulty + 1)) == 3 * ($check->difficulty + 1)) {
                    $free += 1;
                    array_push($won, 0);
                }
            }
            for ($i = 0; $i < $count - $free - 1; $i++) {
                $toAdd = mt_rand(0, $max / ($count - $free - $i));
                $max -= $toAdd;
                if ($toAdd == 0) {
                    $toAdd += 1;
                    if ($max > 1) {
                        $max -= 1;
                    }
                }
                array_push($won, $toAdd);
            }
            array_push($won, $max);
            $w = array_sum($won);
            shuffle($won);
            $db->delete();
            if ($free != 0) {
                Funcs::addCard($user->userid, $check->id, $free);
            }
            $user->increment('balance', $w);
            if (env('LEADERBOARD_INCLUDE_REWARD') == 1) {
                Funcs::leaderboard($user->name, $user->userid, $w);
            }
            DB::table('hist_game')->updateOrInsert(['userid' => $user->userid, 'game' => 'Scratcher'], ['points' => DB::raw("points + '$w'")]);
            return ['status' => 1, 'won' => $w, 'data' => $won];
        } else {
            return ['status' => 0, 'message' => 'Either you used this card or card not found!'];
        }
    }

    public function purchase(Request $req)
    {
        $user = $req['user'];
        $qty = (int) $req->get('q');
        if ($qty == 0) {
            return ['status' => 0, 'message' => 'Invalid quantity!'];
        }
        $check = DB::table('scratcher_game')
                    ->where('can_purchase', 1)
                    ->where('id', $req->get('id'))
                    ->first();
        if ($check) {
            if ($user->balance >= $check->cost * $qty) {
                Funcs::deductGamePoints($user, 'Scratcher', $check->cost * $qty);
                Funcs::addCard($user->userid, $check->id, $qty);
                return ['status' => 1, 'message' => 'Purchase success.'];
            } else {
                return ['status' => 0, 'message' => 'Insufficient balance!'];
            }
        } else {
            return ['status' => 0, 'message' => 'Card not found!'];
        }
    }
}
