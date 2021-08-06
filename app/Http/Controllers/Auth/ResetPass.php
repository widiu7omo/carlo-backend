<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Cache;
use DB;

class ResetPass extends Controller
{
    public function view(Request $req)
    {
        return view('auth.reset');
    }

    public function reset(Request $req)
    {
        $this->validate(request(), [
            'email' => 'required|string|email|max:190',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|string'
        ]);
        $email = $req->get('email');
        $pass1 = $req->get('password');
        $check = DB::table('users')->where('email', $email)->first();
        if (!$check) {
            return back()->withInput()->with('error', 'Account not found with this email');
        }
        $banCheck = DB::table('banned_users')->where('userid', $check->userid)->first();
        if ($banCheck) {
            return back()->withInput()->with('error', 'Banned: ' . $banCheck->reason);
        }
        if (Cache::has('reset-' . $email)) {
            return back()->with('error', 'We have already sent you an email with a validation link.');
        } else {
            $key = $this->generateRandomString();
            $data = ['email' => $email, 'password' => $pass1];
            $html = $this->htmlEmailTemplate($key, $pass1);
            try {
                \Mail::send(array(), array(), function ($message) use ($html, $email) {
                    $message->to($email)->subject('Password reset')->from(env('APP_EMAIL'))->setBody($html, 'text/html');
                });
                Cache::put('reset-' . $email, 1, 60);
                Cache::put($key, $data, 60);
                return back()->with('success', 'We have sent you an email with a validation link. Please confirm with that link.');
            } catch (\Exception $e) {
                return back()->with('error', 'Could not establish connection with mailserver');
            }
        }
    }

    public function apiReset(Request $req)
    {
        try {
            $email = \Funcs::dec($req->json('d'));
            $check = DB::table('users')->where('email', $email)->first();
            if (!$check) {
                return ['status'=> 0, 'message' => 'User not found!'];
            }
            $banCheck = DB::table('banned_users')->where('userid', $check->userid)->first();
            if ($banCheck) {
                return ['status'=> 0, 'message' => 'Banned: ' . $banCheck->reason];
            }
            if (Cache::has('reset-' . $email)) {
                return ['status'=> 0, 'message' => 'We have already sent you an email with a validation link.'];
            } else {
                $key = $this->generateRandomString();
                $val = $this->generateRandomString();
                $data = ['email' => $email, 'password' => $val];
                $html = $this->htmlEmailTemplate($key, $val);
                try {
                    \Mail::send(array(), array(), function ($message) use ($html, $email) {
                        $message->to($email)->subject('Password reset')->from(env('APP_EMAIL'))->setBody($html, 'text/html');
                    });
                    Cache::put('reset-' . $email, 1, 60);
                    Cache::put($key, $data, 60);
                    return ['status'=> 1, 'message' => 'We have sent you an email with a validation link. Please confirm with that link'];
                } catch (\Exception $e) {
                    return ['status'=> 0, 'message' => 'Could not establish connection with mailserver'];
                }
            }
        } catch (\Exception $e) {
            return ['status'=> 0, 'message' => $e->getMessage()];
        }
    }

    public function makeReset($key = null)
    {
        if ($key && Cache::has($key)) {
            $user = Cache::get($key);
            DB::table('users')->where('email', $user['email'])->update([
                'password' => bcrypt($user['password'])
            ]);
            Cache::forget($key);
            return redirect()->route('forget')->with('success', 'Password reset was successful, now you can login with your new password.');
        } else {
            return redirect()->route('forget')->with('error', 'Either reset validation link expired or invalid reset link!');
        }
    }

    private function generateRandomString()
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 10; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    
    private function htmlEmailTemplate($key, $pass)
    {
        $appName = env('APP_NAME');
        $appUrl = env('APP_URL');
        $resetUrl = $appUrl . '/login/reset/' . $key;
        return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> <html xmlns="http://www.w3.org/1999/xhtml"> <head> <meta name="viewport" content="width=device-width, initial-scale=1.0"> <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> </head> <body style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #f5f8fa; color: #74787E; height: 100%; hyphens: auto; line-height: 1.4; margin: 0; -moz-hyphens: auto; -ms-word-break: break-all; width: 100% !important; -webkit-hyphens: auto; -webkit-text-size-adjust: none; word-break: break-word;"> <style>@media only screen and (max-width: 600px){.inner-body{width: 100% !important;}.footer{width: 100% !important;}}@media only screen and (max-width: 500px){.button{width: 100% !important;}}</style> <table class="wrapper" width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #f5f8fa; margin: 0; padding: 0; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%;"> <tr> <td align="center" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;"> <table class="content" width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; margin: 0; padding: 0; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%;"> <tr> <td class="header" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; padding: 25px 0; text-align: center;"> <a href="' . $appUrl . '" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #bbbfc3; font-size: 19px; font-weight: bold; text-decoration: none; text-shadow: 0 1px 0 white;"> ' . $appName . ' </a> </td></tr><tr> <td class="body" width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #FFFFFF; border-bottom: 1px solid #EDEFF2; border-top: 1px solid #EDEFF2; margin: 0; padding: 0; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%;"> <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #FFFFFF; margin: 0 auto; padding: 0; width: 570px; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px;"> <tr> <td class="content-cell" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; padding: 35px;"> <h1 style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #2F3133; font-size: 19px; font-weight: bold; margin-top: 0; text-align: left;">Hello!</h1> <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787E; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">You are receiving this email because we received a password reset request for your account.</p>New Password will be: <b>' . $pass . '</b><table class="action" align="center" width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; margin: 30px auto; padding: 0; text-align: center; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%;"> <tr> <td align="center" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;"> <table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;"> <tr> <td align="center" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;"> <table border="0" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;"> <tr> <td style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;"> <a href="' . $resetUrl . '" class="button button-blue" target="_blank" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; border-radius: 3px; box-shadow: 0 2px 3px rgba(0, 0, 0, 0.16); color: #FFF; display: inline-block; text-decoration: none; -webkit-text-size-adjust: none; background-color: #3097D1; border-top: 10px solid #3097D1; border-right: 18px solid #3097D1; border-bottom: 10px solid #3097D1; border-left: 18px solid #3097D1;">Okay, Reset Password</a> </td></tr></table> </td></tr></table> </td></tr></table> <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787E; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">If you did not request a password reset, no further action is required.</p><p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787E; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">Regards,<br>' . $appName . '</p><table class="subcopy" width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; border-top: 1px solid #EDEFF2; margin-top: 25px; padding-top: 25px;"> <tr> <td style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;"> <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787E; line-height: 1.5em; margin-top: 0; text-align: left; font-size: 12px;">If you’re having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser: <a href="' . $resetUrl . '" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #3869D4;"></a><a href="' . $resetUrl . '" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #3869D4;">' . $resetUrl . '</a></p></td></tr></table> </td></tr></table> </td></tr><tr> <td style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;"> <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; margin: 0 auto; padding: 0; text-align: center; width: 570px; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px;"> <tr> <td class="content-cell" align="center" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; padding: 35px;"> <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; line-height: 1.5em; margin-top: 0; color: #AEAEAE; font-size: 12px; text-align: center;">© ' . date('Y') . ' ' . $appName . '. All rights reserved.</p></td></tr></table> </td></tr></table> </td></tr></table> </body> </html>';
    }
}
