<?php

namespace App\Http\Controllers\Auth;
use App\Mail\successfullyActivated;
use Mail;
use App\Mail\userVerificationEmail;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

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
    protected $redirectTo = '/home';

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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
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
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'verifyToken' => str_random(30),
        ]);


        Mail::to($user->email)->send(new userVerificationEmail($user));
        return $user;

    }


    protected function verifyEmail($email, $token){
        $user = User::where(['email' => $email, 'verifyToken' => $token])->first();

        if ($user){
            $user->verifyToken = '';
            $user->status = 1;

            $verified = $user->save();

            if ($verified){

                Mail::to($user->email)->send(new successfullyActivated($user));
                return redirect()->route('login')->with('message','Your Account Has Been Activated You Can Login');
            }

            else{
                return redirect()->route('login')->with('message','Invalid Email or Token');
            }
        }

        else{
            return redirect()->route('login')->with('message','Invalid Email or Token');
        }


    }






}