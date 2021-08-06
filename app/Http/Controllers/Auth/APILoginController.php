<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\User;
use Exception;
use Funcs;
use Auth;
use DB;

class APILoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('apithrottle:10,30')->only('login');
    }
    
    public function fLogin(Request $req)
    {
        try {
            $tok = $req->get('t');
            $fburl = 'https://graph.facebook.com/v6.0/me?fields=id,name,email,picture&access_token='.$tok;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $fburl);
            $result = curl_exec($ch);
            curl_close($ch);
            $infos = json_decode($result);  //id, name, email, picture->url
            $avatar = $infos->picture->data->url;
            if (!$infos) {
                return ['data' => Funcs::enc(json_encode(['status' => 0,'message' => 'Could not get required information from your Facebook profile!']))];
            }
            $uid = 'F'.substr($infos->id, -12);
            if (isset($infos->email)) {
                $e = $infos->email;
            } else {
                $e = $uid.'@nullfb.tld';
            };
            $user = User::where('userid', $uid)->first();
            $did = $req->json('did');
            if (Funcs::isBanned($user, $did)) {
                return ['data' => Funcs::enc(json_encode(['status' => 0, 'message' => $isBanned->reason]))];
            }
            if ($user === null) {
                $user = User::create([
                    'userid' => $uid,
                    'email' => $e,
                    'name' => $infos->name,
                    'password' => bcrypt(Str::random(15)),
                    'device_id' => $did,
                    'ip' => \Request::ip(),
                    'avatar' => $avatar,
                    'country' => $req->json('cc')
                ]);
            } else {
                if ($user->email != $infos->email) {
                    $user->update(['email' => $infos->email]);
                }
                if ($user->avatar != $avatar) {
                    $user->update(['avatar' => $avatar]);
                }
                if ($did != $user->device_id) {
                    $user->update(['device_id' => $did]);
                }
            }
            //Funcs::updateFCT($user->userid, $user->email, $req->json('fid'));
            $token = $user->id.'|'.Str::random(80);
            $toUpdate = ['remember_token' => $token, 'updated_at' => Carbon::now()];
            if ($did != $user->device_id) {
                $toUpdate['device_id'] = $did;
            };
            DB::table('users')->where('userid', $user->userid)->update($toUpdate);
            $res = ['status' => 1, 'message' => $token, 'u' => $user->userid];
            return ['data' => Funcs::enc(json_encode($res))];
        } catch (Exception $e) {
            return ['data' => Funcs::enc(json_encode(['status' => 0,'message' => $e->getMessage()]))];
        }
    }
    
    public function gLogin(Request $req)
    {
        try {
            $gotok = $req->get('t');
            $gourl = 'https://www.googleapis.com/oauth2/v3/tokeninfo?id_token='.$gotok;
            $goch = curl_init();
            curl_setopt($goch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($goch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($goch, CURLOPT_URL, $gourl);
            $goresult = curl_exec($goch);
            curl_close($goch);
            $infos = json_decode($goresult);  //id, name, email, picture->url
            if (!$infos) {
                return ['data' => Funcs::enc(json_encode(['status' => 0,'message' => 'Could not get required information from your Google account!']))];
            }
            $uid = 'G'.substr($infos->sub, -12);
            $user = User::where('userid', $uid)->first();
            $did = $req->json('did');
            if (Funcs::isBanned($user, $did)) {
                return ['data' => Funcs::enc(json_encode(['status' => 0, 'message' => $isBanned->reason]))];
            }
            $avatar = $infos->picture;
            if ($user === null) {
                $user = User::create([
                    'userid' => $uid,
                    'email' => $infos->email,
                    'name' => $infos->given_name.' '.$infos->family_name,
                    'password' => bcrypt(Str::random(15)),
                    'device_id' => $did,
                    'ip' => \Request::ip(),
                    'avatar' => $avatar,
                    'country' => $req->json('cc')
                ]);
                Funcs::addref($user, $req->json('rb'));
            } else {
                if ($user->email != $infos->email) {
                    $user->update(['email' => $infos->email]);
                }
                if ($user->avatar != $avatar) {
                    $user->update(['avatar' => $avatar]);
                }
                if ($did != $user->device_id) {
                    $user->update(['device_id' => $did]);
                }
            }
            //Funcs::updateFCT($user->userid, $user->email, $req->json('fid'));
            $token = $user->id.'|'.Str::random(80);
            $toUpdate = ['remember_token' => $token, 'updated_at' => Carbon::now()];
            if ($did != $user->device_id) {
                $toUpdate['device_id'] = $did;
            };
            DB::table('users')->where('userid', $user->userid)->update($toUpdate);
            $res = ['status' => 1, 'message' => $token, 'u' => $user->userid];
            return ['data' => Funcs::enc(json_encode($res))];
        } catch (Exception $e) {
            return ['data' => Funcs::enc(json_encode(['status' => 0,'message' => $e->getMessage()]))];
        }
    }

    public function pLogin(Request $req)
    {
        try {
            $num = Funcs::dec($req->json('t'));
            $did = $req->json('did');
            $cc = $req->json('cc');
            if ($num == null || $num == '' || !is_numeric($num)) {
                return ['data' => Funcs::enc(json_encode([
                    'status' => 0,
                    'message' => 'You cannot register or login with this number!'
                ]))];
            }
            $user = User::where('email', $num . '@null.tld')->first();
            if (Funcs::isBanned($user, $did)) {
                return ['data' => Funcs::enc(json_encode(['status' => 0, 'message' => $isBanned->reason]))];
            }
            if ($user === null) {
                $uid = substr(str_pad($num, 13, "U12", STR_PAD_LEFT), 0, 13);
                $user = User::create([
                    'userid' => $uid,
                    'email' => $num . '@null.tld',
                    'name' => "Anon" . Str::random(5),
                    'password' => bcrypt(Str::random(15)),
                    'device_id' => $did,
                    'ip' => \Request::ip(),
                    'country' => $req->json('cc')
                ]);
                Funcs::addref($user, $req->json('rb'));
            }
            //Funcs::updateFCT($user->userid, $user->email, $req->json('fid'));
            $token = $user->id.'|'.Str::random(80);
            $toUpdate = ['remember_token' => $token, 'updated_at' => Carbon::now()];
            if ($did != $user->device_id) {
                $toUpdate['device_id'] = $did;
            };
            DB::table('users')->where('userid', $user->userid)->update($toUpdate);
            $res = ['status' => 1, 'message' => $token, 'u' => $user->userid];
            return ['data' => Funcs::enc(json_encode($res))];
        } catch (Exception $e) {
            $res = ['status' => 0,'message' => $e->getMessage()];
            return ['data' => \Funcs::enc(json_encode($res))];
        }
    }

    public function login(Request $req)
    {
        try {
            if (Auth::attempt(['email' => $req->json('email'), 'password' => $req->json('password')])) {
                if (Auth::id() == env('ADMIN')) {
                    Auth::logout();
                    $res = ['status' => 0, 'message' => 'Invalid login credentals!'];
                };
                $user = User::where('id', Auth::id())->first();
                $did = $req->json('did');
                $check = Funcs::isBanned($user, $did);
                if ($check) {
                    $res = ['status' => 0, 'message' => 'Banned: '.$check->reason];
                } else {
                    //Funcs::updateFCT($user->userid, $user->email, $req->json('fid'));
                    $token = $user->id.'|'.Str::random(80);
                    $toUpdate = [
                        'remember_token' => $token,
                        'updated_at' => Carbon::now()
                    ];
                    if ($did != $user->device_id) {
                        $toUpdate['device_id'] = $did;
                    };
                    DB::table('users')->where('userid', $user->userid)->update($toUpdate);
                    $res = ['status' => 1, 'message' => $token, 'u' => $user->userid];
                }
            } else {
                $res = ['status' => 0, 'message' => 'Invalid login credentals!'];
            }
            return ['data' => Funcs::enc(json_encode($res))];
        } catch (Exception $e) {
            return ['data' => \Funcs::enc(json_encode(['status' => 0, 'message' => 'Login method error!']))];
        }
    }
}