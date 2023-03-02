<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

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
            'email' => ['required', 'email', Rule::unique('users', 'email')],
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
    //show profile settings
    public function profile()
    {
        $user = User::find(auth()->user()->id);

        return view('pages.profile', [
            'page' => 'Profile',
            'user' => $user
        ]);
    }

    //update user profile
    public function updateProfile(Request $request)
    {
        $user = User::find(auth()->user()->id);

        $formData = $request->validate([
            'name'          => 'required',
            'email'         => ['required', 'email', Rule::unique('users', 'email')->ignore($user)],
            'profile_image' => 'sometimes'
        ]);

        //check if user supplied profile image
        if ($request->hasFile('profile_image')) {
            $formData['profile_image'] = $request->file('profile_image')->store('profile_images', 'public');
        }

        //update the profile
        $user->update($formData);

        return to_route('profile')->with('message', 'Profile updated successfully');
    }

    //update user password
    public function updateUserPassword(Request $request)
    {
        $user = User::find(auth()->user()->id);

        $formData = $request->validate([
            'old_password'  => 'required',
            'password'      => ['required', 'confirmed', 'min:6'],

        ]);
        //Match The Old Password
        if (!Hash::check($formData['old_password'], $user->password)) {
            return back()->withErrors(['old_password' => 'Current password does not match'])->onlyInput('old_password');
        }

        //update the profile
        $user->update([
            'password' => bcrypt($formData['password'])
        ]);

        return to_route('profile')->with('message', 'Profile updated successfully');
    }

    //delete user
    public function deleteUser()
    {
        User::find(auth()->user()->id)->delete();

        return redirect('/login')->with('message', "You deleted your account.");
    }
}
