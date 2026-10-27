<?php

namespace App\Http\Controllers;
use tttran\viet_qr_generator\Generator;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{

    public function index(Request $request)
    {
        $type = $request->get('type', 'post'); // post hoặc page

        $posts = Post::where('post_type', $type)
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('cms.posts.index', compact('posts', 'type'));
    }

    public function create(Request $request)
    {
        $type = $request->get('type', 'post');

        return view('cms.posts.create', compact('type'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'slug' => 'nullable|unique:posts,slug',
            'excerpt' => 'nullable',
            'content' => 'required',
            'post_type' => 'required|in:post,page',
            'template' => 'nullable',
            'status' => 'required|in:draft,published,archived',
            'meta_title' => 'nullable|max:255',
            'meta_description' => 'nullable',
            'featured_image' => 'nullable',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $validated['author_id'] = auth()->id();

        if ($validated['status'] === 'published' && ! isset($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        Post::create($validated);

        return redirect()
            ->route('cms.posts.index', ['type' => $validated['post_type']])
            ->with('success', ucfirst($validated['post_type']).' đã được tạo thành công!');
    }

    public function show(Post $post)
    {
        return view('cms.posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        return view('cms.posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'slug' => 'required|unique:posts,slug,'.$post->id,
            'excerpt' => 'nullable',
            'content' => 'required',
            'template' => 'nullable',
            'status' => 'required|in:draft,published,archived',
            'meta_title' => 'nullable|max:255',
            'meta_description' => 'nullable',
            'featured_image' => 'nullable',
        ]);

        if ($validated['status'] === 'published' && $post->status !== 'published') {
            $validated['published_at'] = now();
        }

        $post->update($validated);

        return redirect()
            ->route('cms.posts.index', ['type' => $post->post_type])
            ->with('success', ucfirst($post->post_type).' đã được cập nhật thành công!');
    }

    public function destroy(Post $post)
    {
        $type = $post->post_type;
        $post->delete();

        return redirect()
            ->route('cms.posts.index', ['type' => $type])
            ->with('success', ucfirst($type).' đã được xóa thành công!');
    }
}
