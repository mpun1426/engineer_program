<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use \Symfony\Component\HttpFoundation\Response;
use App\Mail\EmailVerification;
use App\Http\Requests\PreRegisterRequest;
use App\Http\Requests\RegisterRequest;
use Mail;

class RegisterController extends Controller
{
    public function pre_register(PreRegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'verify_code' => rand(1000, 9999),
        ]);

        $email = new EmailVerification($user);
        Mail::to($user->email)->send($email);

        return response()->json([
            "status" => "仮登録完了",
            "mail_address" => $user->email,
            "verify_code" => $user->verify_code,
        ], Response::HTTP_OK);
    }

    public function register(RegisterRequest $request)
    {
        $pre_registered_user = User::where('email', $request->email)->first();

        if ($request->verify_code == $pre_registered_user->verify_code) {
            $pre_registered_user->update([
                'verified' => true,
            ]);

            return response()->json([
                "status" => "本登録完了",
                "name" => $pre_registered_user->name,
                "mail_address" => $pre_registered_user->email,
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                "status" => "入力された認証コードが間違っているため本登録できません",
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
