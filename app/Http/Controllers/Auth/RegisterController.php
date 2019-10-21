<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Branch;
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

    public function showRegistrationForm()
    {
        $branchModel = new Branch;
        $branchModel->setConnection('branch');
        $branch = $branchModel->orderBy('bname', 'asc')->get();

        return view('auth.register', compact('branch'));
    }

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
        $config = Config::get('database.connections.dtr');
        $config['database'] = "dtr_" . $data['branch'];
        config()->set('database.connections.dtr', $config);
        DB::purge('dtr');

        return Validator::make($data, [
            'hrisid' => ['required', 'digits:5', 'unique:users', 'exists:dtr.USERINFO,Badgenumber'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^\S{8,}$/'],
        ], [
            'hrisid.required' => 'The HRIS ID field is required.',
            'hrisid.digits' => 'The HRIS ID field must be exactly 5 digits.',
            'hrisid.unique' => 'This HRIS ID has already been taken.',
            'hrisid.exists' => 'This HRIS ID does not exist in the selected branch.',
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
            'password.regex' => 'The Password field must not have spaces.',
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
