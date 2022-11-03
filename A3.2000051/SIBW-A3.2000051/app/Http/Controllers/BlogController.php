<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    /**
     * index
     * 
     * @return void
     */
    public function index()
    {
        //get blogs
        $blogs = Blog::latest()->paginate(10);

        //render view with blogs
        return view('blogs.index', compact('blogs'));
    }

    /**
     * create
     * 
     * @return void
     */
    public function create()
    {
        return view('blogs.create');
    }

    /**
     * store
     * 
     * @param Request $request
     * @return void
     */
    public function  store(Request $request)
    {
        //validate form
        $this->validate($request, [
            'image'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title'     => 'required|min:5',
            'content'   => 'required|min:10'
        ]);

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/blogs', $image->hashName());

        //create post
        Blog::create([
            'image'     => $image->hashName(),
            'title'     => $request->title,
            'content'   => $request->content
        ]);

        //redirect to index
        return redirect()->route('blogs.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }
}
