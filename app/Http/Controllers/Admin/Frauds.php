<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Funcs;
use DB;

class Frauds extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function view(Request $req)
    {
        $data = DB::table('vpn_monitor')->paginate(16);
        return view('admin.frauds', compact('data'));
    }

    public function update(Request $req)
    {
        Funcs::setEnv('SINGLE_ACCOUNT', $req->has('single_account') ? 1 : 0, false);
        if ($req->has('vpn_block')) {
            Funcs::setEnv('VPN_BLOCK', 1, false);
            Funcs::setEnv('VPN_MONITOR', 0, false);
            Funcs::setEnv('AUTO_BAN_VPN', 0, false);
        } else {
            Funcs::setEnv('VPN_BLOCK', 0, false);
            if ($req->has('auto_ban_vpn')) {
                Funcs::setEnv('AUTO_BAN_VPN', 1, false);
                Funcs::setEnv('VPN_MONITOR', 1, false);
            } else {
                Funcs::setEnv('AUTO_BAN_VPN', 0, false);
                Funcs::setEnv('VPN_MONITOR', $req->has('vpn_monitor') ? 1 : 0, false);
            }
        }
        Funcs::setEnv('ROOT_BLOCK', $req->has('root_block') ? 1 : 0, false);
        Funcs::setEnv('AUTO_BAN_MULTI', $req->has('auto_ban_multi') ? 1 : 0, false);
        Funcs::setEnv('AUTO_BAN_ROOT', $req->has('auto_ban_root') ? 1 : 0, false);
        Funcs::setEnv('BAN_CC_CHANGE', $req->has('ban_cc_change') ? 1 : 0);
        return back()->with('success', 'Fraud prevention system updated.');
    }
	
	public function clear(Request $req)
    {
        $data = DB::table('vpn_monitor')->where('id', $req->get('id'))->delete();
        return back()->with('success', 'User VPN monitor count cleared.');
    }
}
