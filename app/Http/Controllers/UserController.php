<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    //display login page
    public function login()
    {
        return view('pages.login', ['page' => 'Login']);
    }

    //display register page
    public function register()
    {
        return view('pages.register', ['page' => 'Register']);
    }

    //register new user
    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'min:3'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:6'],
            'profile_image' => 'sometimes|image|max:1024'
        ]);

        $data['name'] = ucwords($data['name']);
        $data['password'] = bcrypt($data['password']);     //hash password
        $data['status'] = 1;

        if ($request->hasFile('profile_image')) {
            $data['profile_image'] = $request->file('profile_image')->store('profile_images', 'public');
        }
        //create user
        $user = User::create($data);

        //login
        auth()->login($user);

        // redirect after logging in
        return redirect('/home')->with('message', 'Welcome' . $data['name']) . "!";
    }

    //authenticate user login
    public function authenticateUser(Request $request)
    {
        $data = $request->validate(
            [
                'email' => ['required', 'email'],
                'password' => 'required'
            ]
        );

        if (auth()->attempt($data)) {
            $request->session()->regenerate();

            $isActive['status'] = 1;

            User::find(auth()->user()->id)->update($isActive);


            return redirect('/home')->with('message', 'Welcome back ' . auth()->user()->name . "!");
        }
        return back()->withErrors(['invalid' => 'Invalid Email or password'])->onlyInput('email');
    }

    //logout user
    public function logout(Request $request)
    {
        //set user status to offline
        $data['status'] = 2;

        User::find(auth()->user()->id)->update($data);

        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('message', "you have been logged out!");
    }
}
