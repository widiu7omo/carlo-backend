<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class Maintain extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function view(Request $req)
    {
        $currAppVer = env('APP_VERSION');
        $currBackendVer = env('BACKEND_VERSION');
        $data = [
            'app' => '<div><span class="font-weight-bold">Your app version:</span> '.$currAppVer.'</div>',
            'app_update' => '',
            'backend' => '<div><span class="font-weight-bold">Your backend version:</span> '.$currBackendVer.'</div>',
            'new_backend' => '<div><span class="font-weight-bold">Latest backend version:</span> '.$currBackendVer.'</div>'
        ];
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, 'https://mintly.mintsoft.org/latestver');
            $result = curl_exec($ch);
            curl_close($ch);
            $obj = json_decode($result);
            if (is_numeric($currAppVer) && $currAppVer < $obj->app) {
                $data['app'] = '<div class="text-danger"><span class="font-weight-bold">Your app version:</span> '.$currAppVer.' (outdated)</div>';
                $data['app_update'] = '<a href="' . route('app_update', ['ver' => $obj->app]) . '" class="btn btn-sm btn-primary">I received latest app source code</button>';
            }
            if (is_numeric($currBackendVer) && $currBackendVer < $obj->backend) {
                $data['backend'] = '<div class="text-danger"><span class="font-weight-bold">Your backend version:</span> '.$currBackendVer.'</div>';
                $data['new_backend'] = '<div class="text-success"><span class="font-weight-bold">Latest backend version:</span> '.$obj->backend.'</div>';
            }
        } catch (\Exception $e) {
        }

        $tos = resource_path('views')."/terms.blade.php";
        try {
            $p = file_get_contents(resource_path('views')."/privacy.blade.php");
            $privacy = str_replace(["@extends('privacy_inc') @section('privacy')\r\n","\r\n@endsection"], '', $p);
            $t = file_get_contents(resource_path('views')."/terms.blade.php");
            $terms = str_replace(["@extends('terms_inc') @section('terms')\r\n","\r\n@endsection"], '', $t);
        } catch (\Exception $e) {
            $privacy = 'Could not load the file. Make sure "resources\views\privacy.blade.php" got full read-write permission (0777)';
            $terms = 'Could not load the file. Make sure "resources\views\terms.blade.php" got full read-write permission (0777)';
        }
        $data['tos'] = $terms;
        $data['privacy'] = $privacy;
        return view('admin.maintain', compact('data'));
    }

    public function appUpdate(Request $req){
        $this->validate($req, ['ver' => 'required|numeric']);
        \Funcs::setEnv('APP_VERSION', $req->get('ver'));
        return back()->with('success', 'Update successful');
    }

    public function userUpdate(Request $req){
        $this->validate($req, [
            'update_type' => 'required|integer|between:0,1',
            'versioncode' => 'required|integer'
        ]);
        \Funcs::setEnv('USER_VERSIONCODE', $req->post('versioncode'), false);
        \Funcs::setEnv('USER_FORCE_UPDATE', $req->post('update_type'));
        return back()->with('success', 'Update successful');
    }

    public function faq()
    {
        $faq = DB::table('support_faq')->paginate(5);
        return view('admin.faq', compact('faq'));
    }
    
    public function faqAdd(Request $req)
    {
        $this->validate($req, [
            'faq_question' => 'required|string|min:10',
            'faq_answer' => 'required|string|min:10'
        ]);
        DB::table('support_faq')->insert(['question' => $req->post('faq_question'), 'answer' => $req->post('faq_answer')]);
        return back()->with('success', 'FAQ added successfully.');
    }

    public function faqDel(Request $req)
    {
        DB::table('support_faq')->where('id', $req->get('id'))->delete();
        return back()->with('success', 'FAQ deleted successfully.');
    }

    public function tosUpdate(Request $req)
    {
        try {
            $path = resource_path('views')."/terms.blade.php";
            $tos = "@extends('terms_inc') @section('terms')\r\n" . $req->post('tos') . "\r\n@endsection";
            file_put_contents($path, $tos);
            return back()->with('success', 'Terms updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Make sure "resources\views\terms.blade.php" got full read-write permission (0777)');
        }
    }

    public function privacyUpdate(Request $req)
    {
        try {
            $path = resource_path('views')."/privacy.blade.php";
            $tos = "@extends('privacy_inc') @section('privacy')\r\n" . $req->post('privacy') . "\r\n@endsection";
            file_put_contents($path, $tos);
            return back()->with('success', 'Privacy policy updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Make sure "resources\views\privacy.blade.php" got full read-write permission (0777)');
        }
    }
}
