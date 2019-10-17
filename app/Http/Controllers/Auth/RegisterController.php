<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Config;
use DB;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'hrisid' => ['required', 'digits:5', 'unique:users'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'hrisid.required' => 'The HRIS ID field is required.',
            'hrisid.digits' => 'The HRIS ID field must be numeric and exactly 5 digits.',
            'hrisid.unique' => 'This HRIS ID has already been taken.',
            'name.required' => 'The Name field is required.',
            'name.string' => 'The Name field must be a string.',
            'name.max' => 'The Name field must not exceed 255 characters.',
            'email.required' => 'The Email is required.',
            'email.string' => 'The Email field must be a string.',
            'email.email' => 'The Email field needs to have a valid format.',
            'email.max' => 'The Email field must not exceed 255 characters.',
            'email.unique' => 'This Email is already taken.',
            'password.required' => 'The Password field is required.',
            'password.string' => 'The Password field must be a string.',
            'password.max' => 'The Password field must have more than 8 characters.',
            'password.confirmed' => 'The Password field needs to be confirmed.',
       ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'hrisid' => $data['hrisid'],
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
