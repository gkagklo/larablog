<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Category;
use App\Models\PostView;
use DB;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function home()
    {
        // Latest post
        $latestPost = Post::where('active', '=', 1)
        ->whereDate('published_at', '<', Carbon::now())
        ->orderBy('published_at', 'desc')
        ->limit(1)
        ->first();

        // Show the most popular 3 posts based on upvotes
        $popularPosts = Post::query()
        ->leftJoin('upvote_downvotes', 'posts.id', '=', 'upvote_downvotes.post_id')
        ->select('posts.*', DB::raw('COUNT(upvote_downvotes.id) as upvote_count'))
        ->where(function($query){
            $query->whereNull('upvote_downvotes.is_upvote')
                ->orWhere('upvote_downvotes.is_upvote', '=', 1);
        })
        ->where('active', '=', 1)
        ->whereDate('published_at', '<', Carbon::now())
        ->orderByDesc('upvote_count')
        ->groupBy('posts.id')
        ->limit(3)
        ->get();

        // If authorized - Show recommended posts based on user upvotes
        // Not authorized - Popular posts based on views

        // Show recent categories with their latest posts   

        return view('home', compact('latestPost', 'popularPosts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post, Request $request)
    {
        if (!$post->active || $post->published_at > Carbon::now()){
            throw new NotFoundHttpException();
        }

        $prev = Post::query()
        ->where('active', true)
        ->whereDate('published_at', '<=', Carbon::now())
        ->whereDate('published_at', '>', $post->published_at)
        ->orderBy('published_at', 'asc')
        ->limit(1)
        ->first();

        $next = Post::query()
        ->where('active', true)
        ->whereDate('published_at', '<=', Carbon::now())
        ->whereDate('published_at', '<', $post->published_at)
        ->orderBy('published_at', 'desc')
        ->limit(1)
        ->first();

        $user = $request->user();

        PostView::create([
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'post_id' => $post->id,
            'user_id' => $user?->id,
        ]);

        return view('post.view', compact('post','next','prev'));
    }

    public function byCategory(Category $category)
    {
        $posts = Post::query()
            ->join('category_post','posts.id', '=', 'category_post.post_id')
            ->where('category_post.category_id', '=', $category->id)
            ->where('active', '=', true)
            ->whereDate('published_at', '<=', Carbon::now())
            ->orderBy('published_at', 'desc')
            ->paginate(10);

            return view('post.index',compact('posts','category'));
    }   
   
}
