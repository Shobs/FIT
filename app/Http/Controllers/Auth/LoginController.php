<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
    * Where to redirect users after login.
    *
    * @var string
    */
    // protected $redirectTo = '/';
    protected function redirectTo()
    {
        if (auth()->user()->hasRole('admin')) {
            return 'admin/dashboard';
        } else if(auth()->user()->hasRole('driver')) {
            return 'driver/dashboard';
        } else if(auth()->user()->hasRole('restaurant')) {
            return 'restaurant/dashboard';
        }

        return '/';
    }

    /**
    * Create a new controller instance.
    *
    * @return void
    */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
