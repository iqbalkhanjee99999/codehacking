<?php

namespace App\Http\Controllers;

use App\Category;
use App\Comment;
use App\Http\Requests\PostCreateRequest;
use App\Image;
use App\Post;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;

class AdminPostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Post::orderBy('id','desc')->get();
        return view('admin.posts.index',compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::lists('name','id')->all();
        return view('admin.posts.create',compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostCreateRequest $request)
    {
//        return $request->all();
        $input = $request->all();

        if($file = $request->file('file')){
            $file_name = time().$file->getClientOriginalName();
            $file->move('images',$file_name);
            $image = Image::create(['file' => $file_name]);
            $input['image_id'] = $image->id;
        }
        $user  = Auth::user();
        $user->post()->create($input);
        return redirect('/admin/posts')->with('success','Post added successfully');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $post = Post::find($id);
        $categories = Category::lists('name','id')->all();
        return view('admin.posts.edit',compact('post','categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();

        if($file = $request->file('file')){
            $file_name = time().$file->getClientOriginalName();
            $file->move('images',$file_name);
            $image = Image::create(['file' => $file_name]);
            $input['image_id'] = $image->id;
        }

        Post::whereId($id)->first()->update($input);
        return redirect('/admin/posts')->with('success','Post updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::findORFail($id);
        unlink(public_path().$post->image->file);
        $post->delete();
        return redirect('/admin/posts')->with('success','Post deleted successfully');
    }

    public function post($id){
        $post = Post::findOrFail($id);
        $data = Comment::where('post_id',$id)->where('is_active',1)->get();
        return view('/post',compact('post','data'));
    }
}
