<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function account()
    {
        $user = auth()->user()->currentAccessToken()->tokenable;

        switch ($user->verified) {
            case 0:
                $status = '仮登録';
                break;
            case 1:
                $status = '本登録済';
                break;
        }

        return response()->json([
            "name" => $user->name,
            "mail_address" => $user->email,
            "status" => $status,
            "created_at" => $user->created_at,
        ]);
    }
}