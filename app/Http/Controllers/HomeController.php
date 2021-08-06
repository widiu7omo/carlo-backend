<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Carbon\Carbon;
use DB;
use Auth;

class HomeController extends Controller
{
    public function hey(){
        return view("welcome");
    }
    public function Index()
    {
        if (env('INSTALLED') == 1) {
            return view('welcome');
        } else {
            $can_go = true;
            $data = [];
            $path = public_path('uploads');
            if (is_writable($path)) {
                $data['upload'] = '<div class="mx-1 mb-3 row text-success">Upload folder is writeable.</div>';
            } else {
                $can_go = false;
                $data['upload'] = '<div class="mx-1 row text-danger font-weight-bold">Set this folder permission to 0777:</div><div class="mb-3 mx-1 row"><kbd>'.$path.'</kbd></div>';
            }
            $path2 = storage_path();
            if (is_writable($path2)) {
                $data['storage'] = '<div class="mx-1 mb-3 row text-success">Storage folder is writeable.</div>';
            } else {
                $can_go = false;
                $data['storage'] = '<div class="mx-1 row text-danger font-weight-bold">Set this folder permission to 0777:</div><div class="mb-3 mx-1 row"><kbd>'.$path2.'</kbd></div>';
            }
            $path3 = base_path();
            if (is_writable($path3 . '/.env')) {
                $data['config'] = '<div class="mx-1 mb-3 row text-success">Config file is writeable.</div>';
            } else {
                $can_go = false;
                $data['config'] = '<div class="mx-1 row text-danger font-weight-bold">Set this file permission to 0777:</div><div class="mb-3 mx-1 row"><kbd>'.$path3.'\.env</kbd></div>';
            }
            $is_https = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
            if ($is_https) {
                $url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                $data['url'] = '<label class="form-label">Backend URL:</label><input type="text" class="form-control" name="url" value="'.old('url', rtrim($url, '/')).'">';
            } else {
                $can_go = false;
                $data['url'] = '<label class="form-label text-danger">Backend URL:</label><input type="text" class="form-control" name="url" placeholder="SSL (https link) mandatory" disabled>';
            }
            if ($can_go) {
                $data['btn'] = '<button type="submit" class="btn btn-primary px-6">Install the backend</button>';
            } else {
                $data['btn'] = '<button type="button" class="btn btn-secondary px-6">First fix the problem</button>';
            }
            return view('install', compact('data'));
        }
    }

    public function install(Request $req)
    {
        $this->validate($req, [
            'name' => 'required|string|max:30',
            'url' => 'required|url|max:100',
            'db_host' => 'required|string|max:100',
            'db_port' => 'required|integer|digits_between:1,6',
            'db_name' => 'required|string|max:50',
            'db_user' => 'required|string|max:50',
            'db_pass' => 'nullable|string|max:50'
        ]);
        if (substr($req->post('url'), 0, 8) != "https://") {
            return back()->withInput()->with('error', 'Url must start with https://');
        }
        if (!is_writable(public_path('uploads'))) {
            return back()->withInput()->with('error', 'Upload folder is not writeable!');
        }
        if (!is_writable(storage_path())) {
            return back()->withInput()->with('error', 'Storage folder is not writeable!');
        }
        if (!is_writable(base_path() . '/.env')) {
            return back()->withInput()->with('error', 'Config file is not writeable!');
        }
        $connection = mysqli_connect(
            $req->post('db_host'),
            $req->post('db_user'),
            $req->post('db_pass'),
            $req->post('db_name'),
            $req->post('db_port')
        );
        if (mysqli_connect_errno()) {
            return back()->withInput()->with('error', 'Failed to connect to MySQL: ' . mysqli_connect_error());
        }
        file_put_contents(\App::environmentFilePath(), str_replace(
            'APP_NAME="' . env('APP_NAME') . '"',
            'APP_NAME="' . $req->post('name') . '"',
            file_get_contents(\App::environmentFilePath())
        ));
        \Funcs::setEnv('APP_URL', $req->post('url'), false);
        \Funcs::setEnv('DB_HOST', $req->post('db_host'), false);
        \Funcs::setEnv('DB_PORT', $req->post('db_port'), false);
        \Funcs::setEnv('DB_DATABASE', $req->post('db_name'), false);
        \Funcs::setEnv('DB_USERNAME', $req->post('db_user'), false);
        \Funcs::setEnv('DB_PASSWORD', $req->post('db_pass') == null ? '' : $req->post('db_pass'));

        $connection->query('SET foreign_key_checks = 0');
        if ($result = $connection->query("SHOW TABLES")) {
            while ($row = $result->fetch_array(MYSQLI_NUM)) {
                $connection->query('DROP TABLE IF EXISTS '.$row[0]);
            }
        }
        $connection->query('SET foreign_key_checks = 1');

        $templine = '';
        $lines = file(storage_path().'/database.sql');
        $lines = str_replace('https://mintly.mintsoft.org/public', $req->post('url'), $lines);
        foreach ($lines as $line) {
            if (substr($line, 0, 2) == '--' || $line == '') {
                continue;
            }
            $templine .= $line;
            if (substr(trim($line), -1, 1) == ';') {
                if (mysqli_query($connection, $templine)) {
                    $templine = '';
                } else {
                    return back()->withInput()->with('error', 'Failed to import database!');
                }
            }
        }
        $connection->close();
        \Funcs::setEnv('INSTALLED', 1);
        return redirect()->route('main');
    }

    public function Home()
    {
        if (Auth::id() == env('ADMIN')) {
            return redirect()->route('index');
        } else {
            return view('home');
        }
    }

    public function Faq()
    {
        $data = DB::table('support_faq')->get();
        return view('faq', compact('data'));
    }

    public function Terms()
    {
        return view('terms');
    }

    public function Privacy()
    {
        return view('privacy');
    }
}
