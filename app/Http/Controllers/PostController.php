<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function delete(Post $post) {
       
       $post->delete();
       return redirect('/profile/'. auth()->user()->username)->with('success', 'Post successfully deleted!');
    }
    
    public function showEditForm(Post $post) {
        return view('edit-post', ['post' => $post]);
    }
    
    public function update(Request $request, Post $post) {
        $incomingFields = $request->validate([
            'title'=> 'required',
            'body' => 'required',
        ]);

        $incomingFields['title'] = strip_tags($incomingFields['title']);
        $post->update($incomingFields);
        return back() -> with('success', 'Post successfully updated!');
    }
    
    public function showCreateForm() {
        // if (!auth()->check()) {
        //     return redirect('/')->with('Please login to create a post.');
        // }
        return view('create-post');
    }

    public function storeNewPost(Request $request) {
        $incomingFields = $request->validate([
            'title' => 'required',
            'body' => 'required'
        ]);
        
        $incomingFields['title'] = strip_tags($incomingFields['title']);
        // $incomingFields['body'] = strip_tags($incomingFields['body']);
        $incomingFields['user_id'] = auth()->id();

        $newPost = Post::create($incomingFields);
        return redirect("/post/{$newPost->id}")->with('success', 'New post has been successfully created!');
    }

    // parameter's variable name should match the name of router's url variable /post/{this name}
    public function showSinglePost(Post $post) {
        // $post['body'] = Str::markdown($post->body);
        return view('single-post', ['post' => $post]);
    }
}