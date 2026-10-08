<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    private const ALLOWED_MIMES = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/svg+xml'];
    private const MAX_SIZE_KB   = 5120; // 5 MB

    public function index(Request $request)
    {
        $query = Media::latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('original_name', 'like', '%' . $request->search . '%')
                  ->orWhere('title', 'like', '%' . $request->search . '%')
                  ->orWhere('title_id', 'like', '%' . $request->search . '%')
                  ->orWhere('alt', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $media = $query->paginate(24)->withQueryString();

        // If this is a picker modal request (AJAX/picker=1), return picker partial
        if ($request->boolean('picker')) {
            return view('admin.media.picker', compact('media'));
        }

        return view('admin.media.index', compact('media'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file'        => ['required', 'file', 'max:' . self::MAX_SIZE_KB, 'mimes:jpg,jpeg,png,webp,svg'],
            'alt'         => ['nullable', 'string', 'max:255'],
            'title'       => ['nullable', 'string', 'max:255'],
            'title_id'    => ['nullable', 'string', 'max:255'],
            'caption'     => ['nullable', 'string'],
            'caption_id'  => ['nullable', 'string'],
            'category'    => ['nullable', 'string', 'max:50'],
            'is_public'   => ['nullable', 'boolean'],
        ]);

        $file = $request->file('file');

        // Validate MIME type server-side (don't trust browser extension)
        $mime = $file->getMimeType();
        if (! in_array($mime, self::ALLOWED_MIMES, true)) {
            return back()->withErrors(['file' => 'File type not allowed.']);
        }

        // Calculate content hash for deduplication
        $fileHash = md5_file($file->getRealPath());

        // Check if an identical media file already exists
        $existingMedia = Media::where('file_hash', $fileHash)->first();
        if ($existingMedia) {
            // Update metadata if newly provided
            if ($request->filled('title') && empty($existingMedia->title)) {
                $existingMedia->update([
                    'title'      => $request->title,
                    'title_id'   => $request->title_id ?? $existingMedia->title_id,
                    'caption'    => $request->caption ?? $existingMedia->caption,
                    'caption_id' => $request->caption_id ?? $existingMedia->caption_id,
                ]);
            }

            if ($request->wantsJson() || $request->boolean('json')) {
                return response()->json([
                    'id'            => $existingMedia->id,
                    'url'           => $existingMedia->url(),
                    'path'          => $existingMedia->path,
                    'original_name' => $existingMedia->original_name,
                    'deduplicated'  => true,
                ]);
            }

            return back()->with('info', 'This exact image already exists in the Media Library. Using existing asset.');
        }

        // Generate safe unique filename
        $ext      = strtolower($file->getClientOriginalExtension());
        $safeName = Str::uuid() . '.' . $ext;
        $path     = $file->storeAs('media', $safeName, 'public');

        $media = Media::create([
            'filename'      => $safeName,
            'original_name' => pathinfo($file->getClientOriginalName(), PATHINFO_BASENAME),
            'mime_type'     => $mime,
            'size'          => $file->getSize(),
            'path'          => $path,
            'file_hash'     => $fileHash,
            'alt'           => $request->alt ?? '',
            'title'         => $request->title ?? '',
            'title_id'      => $request->title_id ?? '',
            'caption'       => $request->caption ?? '',
            'caption_id'    => $request->caption_id ?? '',
            'category'      => $request->category ?? 'commodities',
            'is_public'     => $request->has('is_public') ? $request->boolean('is_public') : true,
            'sort_order'    => 0,
        ]);

        if ($request->wantsJson() || $request->boolean('json')) {
            return response()->json([
                'id'            => $media->id,
                'url'           => $media->url(),
                'path'          => $media->path,
                'original_name' => $media->original_name,
            ]);
        }

        return back()->with('success', 'Image uploaded successfully.');
    }

    public function update(Request $request, Media $medium)
    {
        $validated = $request->validate([
            'title'       => ['nullable', 'string', 'max:255'],
            'title_id'    => ['nullable', 'string', 'max:255'],
            'caption'     => ['nullable', 'string'],
            'caption_id'  => ['nullable', 'string'],
            'category'    => ['nullable', 'string', 'max:50'],
            'is_public'   => ['nullable'],
            'sort_order'  => ['nullable', 'integer'],
        ]);

        $validated['is_public'] = $request->has('is_public') ? $request->boolean('is_public') : false;

        $medium->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'media' => $medium]);
        }

        return back()->with('success', 'Media information updated successfully.');
    }

    public function toggle(Request $request, Media $medium)
    {
        $medium->update(['is_public' => ! $medium->is_public]);

        if ($request->wantsJson()) {
            return response()->json(['is_public' => $medium->is_public]);
        }

        return back()->with('success', 'Media visibility toggled.');
    }

    public function destroy(Request $request, Media $medium)
    {
        if ($medium->isInUse()) {
            return back()->with('error', 'Cannot delete: this image is still in use by one or more pages or products.');
        }

        Storage::disk('public')->delete($medium->path);
        $medium->delete();

        if ($request->wantsJson()) {
            return response()->json(['deleted' => true]);
        }

        return back()->with('success', 'Image deleted.');
    }
}
