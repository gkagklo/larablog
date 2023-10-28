<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Category;
use DB; 

class Sidebar extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $categories = Category::query()
            ->join('category_post', 'categories.id', '=', 'category_post.category_id')
            ->select('categories.title', 'categories.slug', DB::raw('count(*) as total'))
            ->groupBy('categories.id','categories.title','categories.slug')
            ->orderByDesc('total')
            ->get();
        return view('components.sidebar', compact('categories'));
    }
}
