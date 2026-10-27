<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Danh sách trang: Chuyển hướng về giao diện quản lý trang chuẩn
     */
    public function index(Request $request)
    {
        return redirect()->route('admin.posts.index', ['type' => 'page']);
    }

    /**
     * Thêm trang mới: Chuyển hướng tới form tạo post type=page
     */
    public function create(Request $request, $projectCode = null)
    {
        return redirect()->route('admin.posts.create', ['type' => 'page']);
    }

    /**
     * Lưu trang mới: Ủy quyền cho PostController xử lý
     */
    public function store(Request $request)
    {
        return app(PostController::class)->store($request);
    }

    /**
     * Xem trang ngoài giao diện
     */
    public function show(Post $page, $projectCode = null)
    {
        return redirect()->route('pages.show', $page->slug ?: $page->id);
    }

    /**
     * Sửa trang: Chuyển hướng tới form sửa bài viết type=page
     */
    public function edit($projectCodeOrPost, $postId = null)
    {
        $post = $postId ?? $projectCodeOrPost;
        return redirect()->route('admin.posts.edit', $post);
    }

    /**
     * Cập nhật trang: Ủy quyền cho PostController xử lý
     */
    public function update(Request $request, $projectCodeOrPost, $postId = null)
    {
        return app(PostController::class)->update($request, $projectCodeOrPost, $postId);
    }

    /**
     * Xóa trang: Ủy quyền cho PostController xử lý
     */
    public function destroy($projectCodeOrPost, $postId = null)
    {
        return app(PostController::class)->destroy(request(), $projectCodeOrPost, $postId);
    }
}
