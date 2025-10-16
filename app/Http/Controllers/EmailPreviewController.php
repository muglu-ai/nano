<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserCredentialsMail;

class EmailPreviewController extends Controller
{
    public function showCredentialsEmail(Request $request, $email)
    {

    //    dd($email);
        // Use query params or defaults for preview
        $user = User::where('email', $email)->first();
        if (!$user) {
            abort(404, 'User not found');
        }
        $name = $user->name;
        $setupProfileUrl = config('app.url');
        $username = $user->email;
        $password = $user->simplePass;

        Mail::to($user->email)
            ->bcc('test.interlinks@gmail.com')
            ->queue(new UserCredentialsMail($name, $setupProfileUrl, $username, $password));
        //send this emails emails.credentials to user and test.interlinks@gmail.com
//        Mail::send('emails.credentials', ['name' => $name, 'setupProfileUrl' => $setupProfileUrl, 'username' => $username, 'password' => $password], function ($message) use ($user) {
//                    $message->to($user->email)
////                        ->cc('manish.sharma@interlinks.in')
//                        ->bcc('vivek@interlinks.in')
//                        ->subject(config('constants.EVENT_NAME') . ' Exhibitor Login Credentials');
//                });

        return view('emails.credentials', compact('name', 'setupProfileUrl', 'username', 'password'));
    }
}

