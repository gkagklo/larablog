<x-app-layout meta-description="This my personal blog website">
    
    <!-- Posts Section -->
<section class="w-full md:w-2/3 flex flex-col items-center px-3">

    @foreach($posts as $post)
        <x-post-item :post="$post"></x-post-item>   
    @endforeach
    <p>ss</p>
    {{$posts->links()}}

</section>

    <x-sidebar/>

</x-app-layout>