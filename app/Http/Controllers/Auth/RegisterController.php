<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\RegisterRequest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class RegisterController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(RegisterRequest $request)
    {
        $user = User::create(
            $request->only('name', 'email') + [
                'password' => Hash::make($request->password)
            ]
        );

        Auth::login($user);

       $request->session()->regenerate();
    }
}
