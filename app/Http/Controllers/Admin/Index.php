<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Carbon\Carbon;
use Cache;
use DB;

class Index extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $subMonth = Carbon::now()->subDays(30);
        $c2p = (int) env('CASHTOPTS');
        $data = Cache::remember('index_admin', 300, function () use ($subMonth, $c2p) {
            return [
                'users' => $this->userChart($subMonth),
                'leads' => $this->leadChart($subMonth),
                'earns' => $this->earnChart($subMonth, $c2p),
                'withs' => $this->withChart($subMonth, $c2p),
                'lhist' => $this->leadsLatest(),
                'online' => $this->onlineUsers()
            ];
        });
        return view('admin.index', compact('data'));
    }

    private function userChart($subMonth)
    {
        $user = DB::table('users')->select([
            DB::raw('count(id) as `count`'),
            DB::raw('DATE(created_at) as day')
        ])
        ->where('created_at', '>=', $subMonth)
        ->groupBy('day')
        ->get()
        ->keyby('day');
        $userD = array();
        $userC = array();
        $count = 0;
        foreach ($user as $u) {
            $count += $u->count;
            array_push($userD, $u->day);
            array_push($userC, $u->count);
        }
        return ['total' => $count, 'date' => $userD, 'count' => $userC];
    }

    private function leadChart($subMonth)
    {
        $lead = DB::table('hist_activities')->select([
            DB::raw('count(id) as `count`'),
            DB::raw('DATE(created_at) as day')
        ])
        ->where('is_lead', 1)
        ->where('created_at', '>=', $subMonth)
        ->groupBy('day')
        ->get()
        ->keyby('day');
        $leadD = array();
        $leadC = array();
        foreach ($lead as $l) {
            array_push($leadD, $l->day);
            array_push($leadC, $l->count);
        }
        return ['total' => $lead->count(), 'date' => $leadD, 'count' => $leadC];
    }

    private function earnChart($subMonth, $c2p)
    {
        $earn = DB::table('hist_activities')->select([
            DB::raw('SUM(points) as `count`'),
            DB::raw('DATE(created_at) as day')
        ])
        ->where('is_lead', 1)
        ->where('created_at', '>=', $subMonth)
        ->groupBy('day')
        ->get()
        ->keyby('day');
        $earnD = array();
        $earnC = array();
        foreach ($earn as $e) {
            array_push($earnD, $e->day);
            array_push($earnC, $e->count / $c2p);
        }
        return ['total' => $earn->sum('count') / $c2p, 'date' => $earnD, 'count' => $earnC];
    }

    private function withChart($subMonth, $c2p)
    {
        $with = DB::table('gate_request')->select([
            DB::raw('SUM(points) as `count`'),
            DB::raw('DATE(created_at) as day')
        ])
        ->where('is_completed', 1)
        ->where('created_at', '>=', $subMonth)
        ->groupBy('day')
        ->get()
        ->keyby('day');
        $withD = array();
        $withC = array();
        foreach ($with as $w) {
            array_push($withD, $w->day);
            array_push($withC, $w->count / $c2p);
        }
        return ['total' => $with->sum('count') / $c2p, 'date' => $withD, 'count' => $withC];
    }

    private function leadsLatest()
    {
        $ll = DB::table('hist_activities')
                ->where('is_lead', 1)
                ->orderBy('id', 'desc')
                ->limit(5)
                ->get(['userid','network','points', 'created_at']);
        return $ll;
    }

    private function onlineUsers()
    {
        $ol = DB::table('online_users')->get(['country_iso', 'visitors']);
        $v = '';
        foreach ($ol as $o) {
            $v .= strtolower($o->country_iso) . ':' . $o->visitors . ',';
        }
        return rtrim($v, ',');
    }

    public function clearDash()
    {
        Cache::forget('index_admin');
        return back()->with('success', 'Dashboard cache cleared.');
    }
}