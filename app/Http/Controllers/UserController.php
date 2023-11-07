<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function logout() {
        auth()->logout();
        return redirect('/')->with('success', 'You are now logged out!');
    }

    public function login(Request $request) {
        $incomingFields = $request->validate([
            'loginusername' => 'required',
            'loginpassword' => 'required'
        ]);
        
        if (auth()->attempt(['username' => $incomingFields['loginusername'], 'password' => $incomingFields['loginpassword']])) {
            $request->session()->regenerate();
            return redirect('/')->with('success', 'You have successfully logged in!');
        } else {
            return redirect('/')->with('failure', 'Invalid login.');
        }
    }
    public function register(Request $request) {
        $incomingFields = $request -> validate([
            'username' => ['required','min:3', 'max:20', Rule::unique('users','username')],
            'email' => ['required', 'email', Rule::unique('users','email')],
            'password' => ['required', 'min:8', 'confirmed']
        ]);

        $incomingFields['password'] = bcrypt($incomingFields['password']);
        $incomingFields['avatar'] = null;
        
        $newUser = User::create($incomingFields);
        auth()->login($newUser);
        return back()->with('success', 'Thank you for creating an account!');
    }

    public function showCorrectHomepage() {
        if (auth()->check()) {
            return view('homepage-feed');
        } else {
            return view('homepage');
        }
    }

    public function profile(User $user) {
        $currentlyFollowing = 0;

        if (auth()->check()) {
            $currentlyFollowing = Follow::where([['user_id', '=', auth()->user()->id], ['followeduser', '=', $user->id]])->count();
        }
        return view('profile-posts',['avatar'=> $user->avatar,'username' => $user->username, 'posts' =>$user->posts()->latest()->get(), 'currentlyFollowing'=>$currentlyFollowing]);
    }

    public function showAvatarForm(User $user) {
        return view('avatar-form', ['user' => $user]);
    }

    public function storeAvatar(Request $request) {
        $request->validate([
            'avatar' => 'required|image|max:300'
        ]);

        $user = auth()->user();
        $filename = $user->id . '-' . uniqid() . '.jpg';

        $imageResized = Image::make($request->file('avatar'))->fit(300)->encode('jpg');
        Storage::put('public/avatars/' . $filename, $imageResized);

        $oldAvatar = $user->avatar;
        
        $user->avatar = $filename;
        $user->save();

        if ($oldAvatar != "/null-avatar.jpg") {
            Storage::delete(str_replace('/storage/','public/', $oldAvatar));
        }
        
        return redirect('/manage-avatar')->with('success', 'Avatar successfully saved!');
    }
 
}