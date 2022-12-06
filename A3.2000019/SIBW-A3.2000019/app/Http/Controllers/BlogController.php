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
        $blogs = Blog::latest()->paginate(5);

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
    public function store(Request $request)
    {
        //validate form
        $this->validate($request, [
            'image'     => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'title'     => 'required|min:5',
            'content'   => 'required|min:10'
        ]);

        //upload image
        $image = $request->file('image');
        $image->storeAS('public/blogs',$image->hashName());

        //create blog
        Blog::create([
            'image'     =>$image->hashName(),
            'title'     =>$request->title,
            'content'   =>$request->content
        ]);

        //redirect to index
        return redirect()->route('blogs.index')->with(['sucess' => 'Data Berhasil disimpan!']);
    }

    /**
     * edit
     * 
     * @param mixed $blog
     * @return void
     */
    public function edit(Blog $blog)
    {
        return view('blogs.edit', compact('blog'));
    }

    /**
     * update
     * 
     * @param mixed $request
     * @param mixed $blog
     * @return void
     */
    public function update(Request $request, Blog $blog)
    {
        //validate form
        $this->validate($request, [
            'image'     => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title'     => 'required|min:5',
            'content'   => 'required|min:10'
        ]);

        //check if image is uploaded
        if ($request->hasFile('image')) {

            //upload new image
            $image = $request->file('image');
            $image->storeAs('public/blogs',$image->hashName());

            //delete old image
            storage::delete('public/blogs/'.$blog->image);

            //update post with new image
            $blog->update([
                'image'     => $image->hashName(),
                'title'     => $request->title,
                'content'   => $request->content
            ]);
        } else {

            //update post withot image
            $blog->update([
                'title'     => $request->title,
                'content'   => $request->content
            ]);
        }

        //redirect to index
        return redirect()->route('blogs.index')->with(['success' => 'Data Berhasil diubah!']);
    }

    /**
     * destroy
     * 
     * @param mixed @blog
     * @return void
     */
    public function destroy(Blog $blog)
    {
        //delete image
        Storage::delete('public/blogs/'.$blog->image);

        //delete post
        $blog->delete();

        //redirect to index
        return redirect()->route('blogs.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }

}
