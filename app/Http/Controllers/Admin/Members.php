<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use \Carbon\Carbon;
use DB;

class Members extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function members(Request $req)
    {
        $btn = '<a href="'.route('members').'?online=1" class="btn btn-outline-primary small-btn">Active users first</a>';
        if ($req->has('online') && $req->get('online') == 1) {
            $users = DB::table('users')->orderBy('updated_at', 'desc')->paginate(24);
            $btn = '<a href="'.route('members').'" class="btn btn-outline-primary active small-btn">Active users first</a>';
        } elseif ($req->has('cc')) {
            $users = DB::table('users')->where('country', $req->get('cc'))->orderBy('id', 'desc')->paginate(24);
            $btn = '<a href="'.route('members').'" class="btn btn-dark small-btn">Show all users</a>';
        } else {
            $users = DB::table('users')->orderBy('id', 'desc')->paginate(24);
        }
        $htmlCode = '';
        foreach ($users as $u) {
            $readableTime = "Last seen ".Carbon::parse($u->updated_at)->diffForHumans();
            $diff = Carbon::now()->diffInMinutes($u->updated_at);
            $online = '<span class="badge bg-x"></span>';
            if ($diff < 10) {
                $online = '<span class="badge bg-green"></span>';
                $readableTime = '<span class="text-green">Online</span>';
            } elseif ($diff < 180) {
                $online = '<span class="badge bg-yellow"></span>';
            } elseif ($diff < 10080) {
                $online = '<span class="badge bg-red"></span>';
            }
            if ($u->avatar == null) {
                //$online = '<span class="avatar rounded avatar-md text-avatar">'.$online . strtoupper(substr($u->name, 0, 2)).'</span>';
                $online = '<span class="avatar rounded avatar-md flag-country-'.$u->country.'">'.$online.'</span>';
            } else {
                $online = '<span class="avatar rounded avatar-md" style="background-image: url('.$u->avatar.')">'.$online.'</span>';
            }
            $htmlCode .= '<div class="col-lg-3 col-md-4 col-sm-6 col-12">
                            <a class="card card-link" href="'.route('userinfo', ['userid' => $u->userid]).'">
                                <div class="card-body align-items-center d-flex">
                                    <div class="float-left mr-3">
                                    '. $online .'
                                    </div>
                                    <div class="lh-sm">
                                        <div class="strong text-truncate">'.$u->name.'</div>
                                        <div class="text-muted text-truncate">
                                            <h5 class="my-0 line-height-small">'.$u->email.'</h5>
                                            <h5 class="my-0 line-height-small">'.$readableTime.'</h5>
                                        </div>
                                    </div>
                                </div>
                            </a>
                          </div>';
        };
        $data = ['htmlcode' => $htmlCode, 'users' => $users, 'btn' => $btn, 'title' => 'Users Directory'];
        return view('admin.members', compact('data'));
    }

    public function memberSearch(Request $req)
    {
        try {
            $toFind = $req->input('search');
            $users =  DB::table('users')
                            ->where('id', '!=', env('ADMIN'))
                            ->where(function ($query) use($toFind) {
                                $query->where('userid', 'like', '%'.$toFind.'%')
                                      ->orWhere('email', 'like', '%'.$toFind.'%')
                                      ->orWhere('name', 'like', '%'.$toFind.'%');
                            })
                            ->paginate(24);
            $htmlCode = '';
            foreach ($users as $u) {
                $readableTime = "Last seen ".Carbon::parse($u->updated_at)->diffForHumans();
                $diff = Carbon::now()->diffInMinutes($u->updated_at);
                $online = '<span class="badge bg-x"></span>';
                if ($diff < 10) {
                    $online = '<span class="badge bg-green"></span>';
                    $readableTime = '<span class="text-green">Online</span>';
                } elseif ($diff < 180) {
                    $online = '<span class="badge bg-yellow"></span>';
                } elseif ($diff < 10080) {
                    $online = '<span class="badge bg-red"></span>';
                }
                if ($u->avatar == null) {
                    //$online = '<span class="avatar avatar-md text-avatar">'.$online . strtoupper(substr($u->name, 0, 2)).'</span>';
                    $online = '<span class="avatar rounded avatar-md flag-country-'.$u->country.'">'.$online.'</span>';
                } else {
                    $online = '<span class="avatar avatar-md" style="background-image: url('.$u->avatar.')">'.$online.'</span>';
                }
                $htmlCode .= '<div class="col-lg-3 col-md-4 col-sm-6 col-12">
                                <a class="card card-link" href="'.route('userinfo', ['userid' => $u->userid]).'">
                                <div class="card-body align-items-center d-flex">
                                    <div class="float-left mr-3">
                                    '. $online .'
                                    </div>
                                    <div class="lh-sm">
                                        <div class="strong text-truncate">'.$u->name.'</div>
                                        <div class="text-muted text-truncate">
                                            <h5 class="my-0 line-height-small">'.$u->email.'</h5>
                                            <h5 class="my-0 line-height-small">'.$readableTime.'</h5>
                                        </div>
                                    </div>
                                </div>
                            </a>
                          </div>';
            };
            $data = ['htmlcode' => $htmlCode, 'users' => $users , 'btn' => '', 'title' => 'User search result'];
            return view('admin.members', compact('data'));
        } catch (\Exception $e) {
            return back()->with('searcherror', 'Unknown error occurred!');
        }
    }

    public function bannedMembers(Request $req)
    {
        $users = DB::table('banned_users')->orderBy('created', 'desc')->leftJoin('users', 'banned_users.userid', '=', 'users.userid')->paginate(10);
        return view('admin.members_banned', compact('users'));
    }

    public function newBan(Request $req)
    {
        try {
            $user = DB::table('users')->where('userid', $req->input('userid'))->first();
            $reason = $req->input('reason');
            if ($reason == null) {
                return back()->with('error', "Write down the reason for this ban action.");
            }
            if ($user->device_id != 'none') {
                DB::table('banned_users')->insert([
                    'userid' => $user->userid,
                    'reason' => $reason,
                    'device_id' => $user->device_id,
                    'created' => Carbon::now()
                ]);
            } else {
                DB::table('banned_users')->insert([
                    'userid' => $user->userid,
                    'reason' => $reason,
                    'created' => Carbon::now()
                ]);
            }
            return back()->with('success', $user->name . ' has been banned!');
        } catch (\Exception $e) {
            return back()->with('error', 'Unknown error occurred!');
        }
    }
    public function editBan(Request $req)
    {
        try {
            DB::table('banned_users')->where('userid', $req->get('uid'))->update([
                'reason' => $req->post('reason'),
                'created' => Carbon::now()
            ]);
            return back()->with('success', 'Ban reason updated!');
        } catch (\Exception $e) {
            return back()->with('error', 'Unknown error occurred!');
        }
    }
    public function removeBan(Request $req)
    {
        try {
            DB::table('banned_users')->where('userid', $req->get('uid'))->delete();
            return back()->with('success', 'Ban lifted.');
        } catch (\Exception $e) {
            return back()->with('error', 'Unknown error occurred!');
        }
    }

    public function userInfo(Request $req)
    {
        try {
            $userinfo = DB::table('users')->where('userid', $req->get('userid'))->first();
            $data = [
                'userinfo' => $userinfo,
                'country' => DB::table('online_users')->where('country_iso', strtoupper($userinfo->country))->first()->country_name,
                'banned' => DB::table('banned_users')->where('userid', $userinfo->userid)->first(),
                'hist' => DB::table('hist_activities')->where('userid', $userinfo->userid)->orderBy('id', 'desc')->paginate(5, ['*'], 'h'),
                'g_hist' => DB::table('hist_game')->where('userid', $userinfo->userid)->where('game', '!=', 'Redeemption')->orderBy('game', 'desc')->get(),
                'wdraw' => DB::table('gate_request')->where('userid', $userinfo->userid)->orderBy('created_at', 'desc')->paginate(3, ['*'], 'w')
            ];
            return view('admin.member_info', compact('data'));
        } catch (\Exception $e) {
            return back()->with('error', 'Unknown error occurred!');
        }
    }

    public function userinfoUpdate(Request $req)
    {
        $this->validate($req, [
            'userid' => 'required|string',
            'name' => 'required|string|max:49',
            'email' => 'required|email|max:98',
            'avatar' => 'nullable|url',
            'pass' => 'nullable|string|max:50',
            'refby' => 'nullable|string|max:13'
        ]);
        $db = DB::table('users')->where('userid', $req->post('userid'));
        $check = $db->first();
        $ref = $req->post('refby');
        if ($check) {
            if ($ref == null) {
                $ref = 'none';
            } elseif ($ref == $check->userid) {
                return back()->with('error', 'User himself cannot be his referrer!');
            }
            $chk = DB::table('hist_activities')->where('note', $ref)->where('network', 'referral')->first();
            if ($chk && $chk->userid == $check->userid) {
                return back()->with('error', 'This referral code "'.$ref.'" cannot be used. Users cannot refer each other.');
            }
            if ($ref == 'none' && $check->refby != 'none') {
                $oldref = DB::table('users')->where('userid', $check->refby);
                $orf = $oldref->first();
                if ($orf) {
                    $amt_d = DB::table('hist_activities')->where('note', $check->userid)->where('network', 'referral');
                    $amt = $amt_d->first();
                    if ($amt) {
                        $oldref->decrement('balance', $amt->points);
                    }
                    $amt_d->delete();
                    $rw_d = DB::table('hist_activities')->where('userid', $check->userid)->where('network', 'referred');
                    $rw = $rw_d->first();
                    if ($rw) {
                        $db->decrement('balance', $rw->points);
                    }
                    $rw_d->delete();
                }
            } elseif ($ref != $check->refby) {
                $refuser = DB::table('users')->where('userid', $ref);
                $rf = $refuser->first();
                if ($rf) {
                    if ($check->refby == 'none') {
                        $amt_u = (int) env('REF_user_REWARD');
                        $db->increment('balance', $amt_u);
                        DB::table('hist_activities')->insert([
                            'userid' => $check->userid,
                            'network' => 'referred',
                            'points' => $amt_u
                        ]);
                    } else {
                        $oldref = DB::table('users')->where('userid', $check->refby);
                        $orf = $oldref->first();
                        if ($orf) {
                            $amt_d = DB::table('hist_activities')->where('note', $check->userid)->where('network', 'referral');
                            $amt = $amt_d->first();
                            if ($amt) {
                                $oldref->decrement('balance', $amt->points);
                            }
                            $amt_d->delete();
                        }
                    }
                    $amts = (int) env('REF_LINK_REWARD');
                    $refuser->increment('balance', $amts);
                    DB::table('hist_activities')->insert([
                        'userid' => $rf->userid,
                        'network' => 'referral',
                        'points' => $amts,
                        'note' => $check->userid
                    ]);
                } else {
                    return back()->with('error', 'Referrer ID "'.$ref.'" not found!');
                }
            }
            $db->update([
                'name' => $req->post('name'),
                'email' => $req->post('email'),
                'avatar' => $req->post('avatar'),
                'refby' => $ref,
                'password' => Hash::make($req->post('pass'))
            ]);
            return back()->with('success', 'Successfully updated user profile information.');
        } else {
            return back()->with('error', 'User not found!');
        }
    }

    public function Reward(Request $req)
    {
        $u = $req->post('userid');
        $p = (int) $req->post('points');
        if ($p < 1) {
            return back()->with('error', 'Amount must be greater than zero!');
        }
        $d = DB::table('users')->where('userid', $u);
        $d->increment('balance', $p);
        DB::table('hist_activities')->insert([
            'userid' => $u,
            'network' => 'reward',
            'points' => $p
        ]);
        return back()->with('success', 'User has been rewarded by '.$p.' '.strtolower(env('CURRENCY_NAME')).'s');
    }

    public function Penalty(Request $req)
    {
        $u = $req->post('userid');
        $p = (int) $req->post('points');
        if ($p < 1) {
            return back()->with('error', 'Amount must be greater than zero!');
        }
        $d = DB::table('users')->where('userid', $u);
        $d->decrement('balance', $p);
        DB::table('hist_activities')->insert([
            'userid' => $u,
            'network' => 'penalty',
            'points' => -$p
        ]);
        return back()->with('success', $p.' '.strtolower(env('CURRENCY_NAME'))."s has been deducted from user's balance");
    }
}
