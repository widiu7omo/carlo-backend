<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Funcs;
use DB;

class Settings extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function view(Request $req)
    {
        $data = '';
        return view('admin.settings', compact('data'));
    }

    public function update(Request $req)
    {
        $this->validate($req, [
            'backend_name' => 'required|string|between:5,50',
            'backend_url' => 'required|url|regex:/^\S*$/u',
            'enc_key' => 'required|string|between:8,15|regex:/^\S*$/u',
            'fcm_key' => 'required|string|regex:/^\S*$/u',
            'currency_name' => 'required|string|between:3,10|regex:/^\S*$/u',
            'usd-eq' => 'required|string|between:2,10|regex:/^\S*$/u',
            'cash_to_points' => 'required|integer|min:1',
            'pay_percent' => 'required|integer|min:1',
            'pay_referral' => 'required|integer|min:0',
            'pay_referred' => 'required|integer|min:0',
            'earning_notification' => 'required|integer|between:0,1',
            'balance_interval' => 'required|integer|min:10',
            'leaderboard_reward' => 'required|integer|min:3',
            'debug' => 'required|integer|between:0,1'
        ]);
        file_put_contents(\App::environmentFilePath(), str_replace(
            env('APP_DEBUG') == true ? 'APP_DEBUG=true' : 'APP_DEBUG=false',
            $req->post('debug') == 1 ? 'APP_DEBUG=true' : 'APP_DEBUG=false',
            file_get_contents(\App::environmentFilePath())
        ));
        file_put_contents(\App::environmentFilePath(), str_replace(
            'APP_NAME="' . env('APP_NAME') . '"',
            'APP_NAME="' . $req->post('backend_name') . '"',
            file_get_contents(\App::environmentFilePath())
        ));
        Funcs::setEnv('APP_URL', $req->post('backend_url'), false);
        Funcs::setEnv('ENC_KEY', $req->post('enc_key'), false);
        Funcs::setEnv('FCM_SERVER_KEY', $req->post('fcm_key'), false);
        Funcs::setEnv('CURRENCY_NAME', $req->post('currency_name'), false);
        Funcs::setEnv('USD_EQ', $req->post('usd-eq'), false);
        Funcs::setEnv('CASHTOPTS', $req->post('cash_to_points'), false);
        Funcs::setEnv('PAY_PCT', $req->post('pay_percent'), false);
        Funcs::setEnv('REF_LINK_REWARD', $req->post('pay_referral'), false);
        Funcs::setEnv('REF_USER_REWARD', $req->post('pay_referred'), false);
        Funcs::setEnv('EARNING_NOTIFICATION', $req->post('earning_notification'), false);
        Funcs::setEnv('BALANCE_INTERVAL', $req->post('balance_interval'), false);
        Funcs::setEnv('LEADERBOARD_REWARD', $req->post('leaderboard_reward'));
        return back()->with('success', 'Settings updated');
    }

    public function updateLB(Request $req)
    {
        $d = '';
        $c = 0;
        $count = 0;
        for ($i = 0; $i < $req->get('limit'); $i++) {
            $a = $req->post('rank_'.($i+1));
            if (is_numeric($a)) {
                $c += $a;
                if ($a != 0) {
                    $d .= $a.',';
                    $count += 1;
                }
            }
        }
        if ($c == 100) {
            Funcs::setEnv('LEADERBOARD_LIMIT', $count);
            Funcs::setEnv('LEADERBOARD_PCT', rtrim($d, ','));
            return ['status' => 1, 'message' => $count];
        } else {
            return ['status' => 0, 'message' => 'Summation of '.$d.' is equal to '.$c.'. It has to be 100.'];
        }
    }

    public function clearCache()
    {
        \Artisan::call('cache:clear');
        \Artisan::call('view:clear');
        array_map('unlink', array_filter((array) glob(storage_path('logs/*.log'))));
        return back()->with('success', 'Cache cleared');
    }

    public function geoApi()
    {
        $data = ['url' => '', 'deep_link_1' => null, 'deep_link_2' => null, 'key' => ''];
        $d = DB::table('misc')->where('name', 'geo_api')->first();
        if ($d) {
            $dta = json_decode($d->data);
            $data['url'] = $dta->url;
            $data['deep_link_1'] = $dta->deep_link_1;
            $data['deep_link_2'] = $dta->deep_link_2;
            $data['key'] = $dta->key;
        }
        return view('admin.geo_api', compact('data'));
    }

    public function geoUpdate(Request $req)
    {
        $this->validate($req, [
            'url' => 'required|url',
            'deep_link_1' => 'nullable|string',
            'deep_link_2' => 'nullable|string',
            'key' => 'required|string'
        ]);
        $url = $req->post('url');
        $dl1 = $req->post('deep_link_1');
        $dl2 = $req->post('deep_link_2');
        $key = $req->post('key');
        if ($dl1 == null && $dl2 != null) {
            return back()->withInput()->with('error', 'Fill the "first deep JSON object key" field to fill the secone field!');
        }
        $response = Http::get($url);
        $status = $response->status();
        if ($status == 200) {
            try {
                $data = json_decode($response->body());
                if ($dl1 != null) {
                    $cc = $data->$dl1->$key;
                } elseif ($dl2 != null) {
                    $cc = $data->$dl1->$dl2->$key;
                } else {
                    $cc = $data->$key;
                }
                if (Funcs::countryExist($cc)) {
                    Funcs::setmisc('geo_api', json_encode(['url' => $url, 'deep_link_1' => $dl1, 'deep_link_2' => $dl2, 'key' => $key]));
                    \Cache::forget('connect_geo');
                    return back()->with('success', 'Successfully updated and verified the link');
                } else {
                    return back()->withInput()->with('error', 'API verification failed. Check if your entered data is correct!');
                }
            } catch (\Exception $e) {
                return back()->withInput()->with('error', 'Could not parse json data!');
            }
        } else {
            return back()->with('error', 'Provider responded with this error code: '.$status);
        }
    }
	
	public function emailUpdate(Request $req)
    {
        $this->validate($req, [
            'host' => 'required|string',
            'port' => 'required|integer',
            'username' => 'nullable|string',
            'password' => 'nullable|string',
            'encryption' => 'nullable|string',
            'from_address' => 'nullable|string'
        ]);
        Funcs::setEnv('MAIL_HOST', $req->post('host'), false);
        if ($req->post('username') != null) {
            Funcs::setEnv('MAIL_USERNAME', $req->post('username'), false);
        }
        if ($req->post('password') != null) {
            Funcs::setEnv('MAIL_PASSWORD', $req->post('password'), false);
        }
        if ($req->post('encryption') != null) {
            Funcs::setEnv('MAIL_ENCRYPTION', $req->post('encryption'), false);
        }
        if ($req->post('from_address') != null) {
            Funcs::setEnv('MAIL_FROM_ADDRESS', $req->post('from_address'), false);
        }
        Funcs::setEnv('MAIL_PORT', $req->post('port'));
        return back()->with('success', 'SMTP setup updated');
    }
}
