<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::latest()->get();
        return view('admin.articles.index', compact('articles'));
    }

    public function create()
    {
        $article = new Article();
        $media   = Media::latest()->get();
        return view('admin.articles.form', compact('article', 'media'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['title']);

        // Handle image
        $imagePath = $this->handleImageUpload($request);
        if ($imagePath !== null) {
            $data['thumbnail'] = $imagePath;
        }

        Article::create($data);

        return redirect()->route('admin.articles.index')->with('success', 'Article created successfully.');
    }

    public function edit(Article $article)
    {
        $media = Media::latest()->get();
        return view('admin.articles.form', compact('article', 'media'));
    }

    public function update(Request $request, Article $article)
    {
        $data = $this->validated($request, $article);
        $data['slug'] = Str::slug($data['title']);

        // Handle image
        $imagePath = $this->handleImageUpload($request);
        if ($imagePath !== null) {
            if ($article->thumbnail && Storage::disk('public')->exists($article->thumbnail)) {
                Storage::disk('public')->delete($article->thumbnail);
            }
            $data['thumbnail'] = $imagePath;
        }

        // Allow clearing image
        if ($request->boolean('clear_image') && $article->thumbnail) {
            if (Storage::disk('public')->exists($article->thumbnail)) {
                Storage::disk('public')->delete($article->thumbnail);
            }
            $data['thumbnail'] = null;
        }

        $article->update($data);

        return redirect()->route('admin.articles.index')->with('success', 'Article updated successfully.');
    }

    public function destroy(Article $article)
    {
        if ($article->thumbnail && Storage::disk('public')->exists($article->thumbnail)) {
            Storage::disk('public')->delete($article->thumbnail);
        }
        $article->delete();

        return redirect()->route('admin.articles.index')->with('success', 'Article deleted.');
    }

    public function toggle(Article $article)
    {
        $newStatus = $article->status === 'published' ? 'draft' : 'published';
        $article->update(['status' => $newStatus]);
        return back()->with('success', 'Article status updated.');
    }

    private function validated(Request $request, ?Article $article = null): array
    {
        return $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'title_id'     => ['nullable', 'string', 'max:255'],
            'excerpt'      => ['nullable', 'string'],
            'excerpt_id'   => ['nullable', 'string'],
            'body'         => ['required', 'string'],
            'body_id'      => ['nullable', 'string'],
            'status'       => ['required', 'in:draft,published'],
            'published_at' => ['nullable', 'date'],
        ]);
    }

    private function handleImageUpload(Request $request): ?string
    {
        if ($request->filled('media_path')) {
            return $request->input('media_path');
        }

        if ($request->hasFile('image_file')) {
            $file    = $request->file('image_file');
            $mime    = $file->getMimeType();
            $allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            if (! in_array($mime, $allowed, true)) return null;

            $ext  = strtolower($file->getClientOriginalExtension());
            $name = Str::uuid() . '.' . $ext;
            return $file->storeAs('media', $name, 'public');
        }

        return null;
    }
}
