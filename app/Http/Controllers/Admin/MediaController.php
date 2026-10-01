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
                  ->orWhere('alt', 'like', '%' . $request->search . '%');
            });
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
            'file'  => ['required', 'file', 'max:' . self::MAX_SIZE_KB, 'mimes:jpg,jpeg,png,webp,svg'],
            'alt'   => ['nullable', 'string', 'max:255'],
        ]);

        $file = $request->file('file');

        // Validate MIME type server-side (don't trust browser extension)
        $mime = $file->getMimeType();
        if (! in_array($mime, self::ALLOWED_MIMES, true)) {
            return back()->withErrors(['file' => 'File type not allowed.']);
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
            'alt'           => $request->alt ?? '',
        ]);

        if ($request->wantsJson() || $request->boolean('json')) {
            return response()->json([
                'id'  => $media->id,
                'url' => $media->url(),
                'path'=> $media->path,
                'original_name' => $media->original_name,
            ]);
        }

        return back()->with('success', 'Image uploaded successfully.');
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
