<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Carbon\Carbon;
use Funcs;
use File;
use DB;

class GameHtml extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function view(Request $req)
    {
        $data = [
            'files' => File::allFiles(storage_path('games/files')),
            'images' => File::allFiles(storage_path('games/images')),
            'games' => DB::table('html_game')->get(),
			'blocked' => str_replace(',', PHP_EOL, Funcs::getMisc('blocked_hosts'))
        ];
        return view('admin.game_html', compact('data'));
    }

    public function addImage(Request $req)
    {
        $this->validate($req, ['file' => 'required|mimes:jpeg,jpg,png|max:1000']);
        $filename = $req->file('file')->getClientOriginalName();
        $files = File::allFiles(storage_path('games/images'));
        foreach ($files as $f) {
            if ($f->getFileName() == $filename) {
                return back()->with('error', 'Duplicate image name not allowed! Either change your image name or delete the existing one.');
            }
        }
        $req->file('file')->move(storage_path('games/images'), $filename);
        return back()->with('success', 'Image uploaded to the webhost. Now check the selection list again.');
    }

    public function addFile(Request $req)
    {
        $this->validate($req, ['file' => 'required|mimes:pack']);
        $filename = $req->file('file')->getClientOriginalName();
        $files = File::allFiles(storage_path('games/files'));
        foreach ($files as $f) {
            if ($f->getFileName() == $filename) {
                return back()->with('error', 'Duplicate file name not allowed! Either change your file name or delete the existing one.');
            }
        }
        $req->file('file')->move(storage_path('games/files'), $filename);
        return back()->with('success', 'Game file uploaded to the webhost. Now check the selection list again.');
    }

    public function delImage(Request $req)
    {
        $del = $req->post('del');
        $check = DB::table('html_game')->where('image', route('img_api', ['img' => $del]))->get();
        $exist = [];
        foreach ($check as $c) {
            array_push($exist, $c->name);
        }
        if (count($exist) == 0) {
            unlink(storage_path('games/images').'/'.$del);
            return back()->with('success', 'Image file successfully removed from your webhost.');
        } else {
            return back()->with('error', 'This image cannot be deleted! It is being used by: '.implode(', ', $exist));
        }
    }

    public function delFile(Request $req)
    {
        $del = $req->post('del');
        $check = DB::table('html_game')->where('filename', pathinfo($del, PATHINFO_FILENAME))->get();
        $exist = [];
        foreach ($check as $c) {
            array_push($exist, $c->name);
        }
        if (count($exist) == 0) {
            unlink(storage_path('games/files').'/'.$del);
            return back()->with('success', 'Game file successfully removed from your webhost.');
        } else {
            return back()->with('error', 'This game file cannot be deleted! It is being used by: '.implode(', ', $exist));
        }
    }

    public function addGame(Request $req)
    {
        $this->validate($req, [
            'name' => 'required|string|max:99',
            'type' => 'required|integer|between:1,2',
            'game_file' => 'nullable|required_if:type,1|string',
            'game_url' => 'nullable|required_if:type,2|string',
            'image_file' => 'required|string|max:99',
            'orientation' =>'nullable|integer|between:0,1'
        ]);
        if ($req->post('type') == 1) {
            $file = pathinfo($req->post('game_file'), PATHINFO_FILENAME);
            if (substr($file, 0, 4) === 'http') {
                return back()->withInput()->with('error', "This is unacceptable game file name. Please delete the game file and upload again with another pack file name.");
            }
        } else {
            $file = $req->post('game_url');
            if (!filter_var($file, FILTER_VALIDATE_URL)) {
                return back()->withInput()->with('error', "Invalid URL");
            }
        }
        DB::table('html_game')->insert([
            'name' => $req->post('name'),
            'filename' => $file,
            'image' => route('img_api', ['img' => $req->post('image_file')]),
            'orientation' => $req->post('orientation'),
			'noads' => $req->has('is_blocked') ? 1 : 0
        ]);
        return back()->with('success', "That's all, game added to the system.");
    }

    public function delGame(Request $req)
    {
        DB::table('html_game')->where('id', $req->get('id'))->delete();
        return back()->with('success', 'Game item successfully dropped. However, game file and image remain in your webhost. If you want to delete them use the dropdown selection box and click on right side detete icon.');
    }

    public function cacheTime(Request $req)
    {
        $this->validate($req, [
            'time' => 'required|integer|min:5',
            'blocked' => 'nullable|string|max:2000'
        ]);
        $blocked = $req->post('blocked');
        $blocked = preg_replace('!\s+!', ',', $blocked);
        $blocked = str_replace(PHP_EOL, ',', $blocked);
        Funcs::setMisc('blocked_hosts', $blocked);
        Funcs::setEnv('HTML_GAME_REFRESH', $req->post('time'));
        return back()->with('success', 'Settings updated.');
    }
	
	public function setRwd(Request $req)
    {
        $this->validate($req, [
            'reward' => 'required|integer|min:0',
            'duration' => 'required|integer|min:1'
        ]);
        Funcs::setEnv('HTML_GAME_REWARD', $req->post('reward'));
        Funcs::setEnv('HTML_GAME_DURATION', $req->post('duration'));
        return back()->with('success', 'Settings updated.');
    }
}
