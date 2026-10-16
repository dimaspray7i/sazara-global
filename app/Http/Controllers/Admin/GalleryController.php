<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\GalleryCategory;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class GalleryController extends Controller
{
    // Allowed image/video MIME types for uploaded files
    private const ALLOWED_IMAGE_MIMES = [
        'image/jpeg', 'image/jpg', 'image/png', 'image/webp',
    ];
    private const ALLOWED_VIDEO_MIMES = [
        'video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/mpeg',
    ];
    private const MAX_SINGLE_KB     = 10240;    // 10 MB per image/video
    private const MAX_ZIP_KB        = 102400;   // 100 MB per ZIP
    private const MAX_ZIP_FILES     = 100;      // max files in one ZIP
    private const MAX_ZIP_UNCOMPRESSED = 500 * 1024 * 1024; // 500 MB uncompressed guard

    // ── Index ────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = Gallery::with(['coverMedia', 'category'])->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('title_id', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('category')) {
            $query->where('category_slug', $request->category);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $galleries   = $query->paginate(20)->withQueryString();
        $categories  = GalleryCategory::active()->get();

        return view('admin.gallery.index', compact('galleries', 'categories'));
    }

    // ── Create ───────────────────────────────────────────────────────

    public function create()
    {
        $categories = GalleryCategory::active()->get();
        return view('admin.gallery.create', compact('categories'));
    }

    // ── Store (single photo, multi-photo, or ZIP) ─────────────────────

    public function store(Request $request)
    {
        $request->validate([
            'title'          => ['required', 'string', 'max:255'],
            'title_id'       => ['nullable', 'string', 'max:255'],
            'description'    => ['nullable', 'string'],
            'description_id' => ['nullable', 'string'],
            'category_slug'  => ['nullable', 'string', 'max:60'],
            'type'           => ['required', 'in:single,group'],
            'status'         => ['required', 'in:draft,published'],
            'sort_order'     => ['nullable', 'integer'],
            // Single/multi photo
            'photos'         => ['nullable', 'array'],
            'photos.*'       => ['file', 'max:' . self::MAX_SINGLE_KB],
            // ZIP upload
            'zip_file'       => ['nullable', 'file', 'max:' . self::MAX_ZIP_KB, 'mimes:zip'],
        ]);

        DB::beginTransaction();
        try {
            $gallery = Gallery::create([
                'title'          => $request->title,
                'title_id'       => $request->title_id,
                'description'    => $request->description,
                'description_id' => $request->description_id,
                'category_slug'  => $request->category_slug,
                'type'           => $request->type,
                'status'         => $request->status,
                'sort_order'     => $request->sort_order ?? 0,
                'media_count'    => 0,
            ]);

            $mediaIds = [];

            // -- A. Multi-photo upload --
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $file) {
                    $media = $this->storeMediaFile($file);
                    if ($media) $mediaIds[] = $media->id;
                }
            }

            // -- B. ZIP upload --
            if ($request->hasFile('zip_file')) {
                $zipMedia = $this->extractAndStoreZip($request->file('zip_file'), $gallery);
                $mediaIds = array_merge($mediaIds, $zipMedia);
            }

            // -- C. Existing media IDs (selected from media library) --
            if ($request->filled('existing_media_ids')) {
                $existing = array_filter(explode(',', $request->existing_media_ids), 'is_numeric');
                $mediaIds = array_merge($mediaIds, array_map('intval', $existing));
            }

            // Attach media with sort order
            $pivot = [];
            foreach (array_values(array_unique($mediaIds)) as $i => $mid) {
                $pivot[$mid] = ['sort_order' => $i];
            }
            $gallery->media()->sync($pivot);

            // Set cover: first media if none chosen
            $coverId = $request->cover_media_id
                ? (int) $request->cover_media_id
                : ($mediaIds[0] ?? null);
            $gallery->update([
                'cover_media_id' => $coverId,
                'media_count'    => count($mediaIds),
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gallery creation failed: ' . $e->getMessage()]);
        }

        return redirect()->route('admin.gallery.index')
            ->with('success', 'Gallery "' . $gallery->title . '" created successfully.');
    }

    // ── Edit ─────────────────────────────────────────────────────────

    public function edit(Gallery $gallery)
    {
        $gallery->load(['media', 'coverMedia', 'category']);
        $categories = GalleryCategory::active()->get();
        return view('admin.gallery.edit', compact('gallery', 'categories'));
    }

    // ── Update ────────────────────────────────────────────────────────

    public function update(Request $request, Gallery $gallery)
    {
        $request->validate([
            'title'          => ['required', 'string', 'max:255'],
            'title_id'       => ['nullable', 'string', 'max:255'],
            'description'    => ['nullable', 'string'],
            'description_id' => ['nullable', 'string'],
            'category_slug'  => ['nullable', 'string', 'max:60'],
            'type'           => ['required', 'in:single,group'],
            'status'         => ['required', 'in:draft,published'],
            'sort_order'     => ['nullable', 'integer'],
            'cover_media_id' => ['nullable', 'integer'],
            'photos'         => ['nullable', 'array'],
            'photos.*'       => ['file', 'max:' . self::MAX_SINGLE_KB],
            'zip_file'       => ['nullable', 'file', 'max:' . self::MAX_ZIP_KB, 'mimes:zip'],
            'media_order'    => ['nullable', 'string'],  // JSON array of media IDs in new order
            'remove_media'   => ['nullable', 'array'],   // media IDs to detach
        ]);

        DB::beginTransaction();
        try {
            $gallery->update([
                'title'          => $request->title,
                'title_id'       => $request->title_id,
                'description'    => $request->description,
                'description_id' => $request->description_id,
                'category_slug'  => $request->category_slug,
                'type'           => $request->type,
                'status'         => $request->status,
                'sort_order'     => $request->sort_order ?? $gallery->sort_order,
                'cover_media_id' => $request->cover_media_id ?: $gallery->cover_media_id,
            ]);

            // Remove detached media (detach only pivot, don't delete Media row)
            if ($request->filled('remove_media')) {
                $gallery->media()->detach($request->remove_media);
            }

            // Add new photos
            $newMediaIds = [];
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $file) {
                    $media = $this->storeMediaFile($file);
                    if ($media) $newMediaIds[] = $media->id;
                }
            }

            // Add from ZIP
            if ($request->hasFile('zip_file')) {
                $zipMedia = $this->extractAndStoreZip($request->file('zip_file'), $gallery);
                $newMediaIds = array_merge($newMediaIds, $zipMedia);
            }

            // Add existing media from library
            if ($request->filled('existing_media_ids')) {
                $existing = array_filter(explode(',', $request->existing_media_ids), 'is_numeric');
                $newMediaIds = array_merge($newMediaIds, array_map('intval', $existing));
            }

            // Attach new media
            if ($newMediaIds) {
                $currentMax = $gallery->media()->max('gallery_media.sort_order') ?? -1;
                $attach = [];
                foreach (array_unique($newMediaIds) as $i => $mid) {
                    if (!$gallery->media()->where('media.id', $mid)->exists()) {
                        $attach[$mid] = ['sort_order' => $currentMax + $i + 1];
                    }
                }
                if ($attach) $gallery->media()->attach($attach);
            }

            // Re-order from drag-drop JSON
            if ($request->filled('media_order')) {
                $order = json_decode($request->media_order, true);
                if (is_array($order)) {
                    foreach ($order as $i => $mid) {
                        DB::table('gallery_media')
                            ->where('gallery_id', $gallery->id)
                            ->where('media_id', (int) $mid)
                            ->update(['sort_order' => $i]);
                    }
                }
            }

            $gallery->syncMediaCount();

            // Auto-set cover if removed
            if (!$gallery->cover_media_id || !$gallery->media()->where('media.id', $gallery->cover_media_id)->exists()) {
                $firstMedia = $gallery->media()->first();
                $gallery->update(['cover_media_id' => $firstMedia?->id]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Update failed: ' . $e->getMessage()]);
        }

        return redirect()->route('admin.gallery.edit', $gallery)
            ->with('success', 'Gallery updated successfully.');
    }

    // ── Toggle publish status ─────────────────────────────────────────

    public function toggle(Gallery $gallery)
    {
        $gallery->update([
            'status' => $gallery->status === 'published' ? 'draft' : 'published',
        ]);

        if (request()->wantsJson()) {
            return response()->json(['status' => $gallery->status]);
        }

        return back()->with('success', 'Gallery status updated to ' . $gallery->status . '.');
    }

    // ── Destroy ──────────────────────────────────────────────────────

    public function destroy(Gallery $gallery)
    {
        // Detach media (pivot rows) — do NOT delete Media files themselves
        // because those media may be used elsewhere
        $gallery->media()->detach();
        $gallery->delete();

        return redirect()->route('admin.gallery.index')
            ->with('success', 'Gallery deleted. Media files retained in Media Library.');
    }

    // ── Remove single media from gallery (AJAX) ───────────────────────

    public function removeMedia(Request $request, Gallery $gallery, Media $media)
    {
        $gallery->media()->detach($media->id);
        $gallery->syncMediaCount();

        // If this was the cover, reassign
        if ($gallery->cover_media_id === $media->id) {
            $gallery->update(['cover_media_id' => $gallery->media()->first()?->id]);
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'media_count' => $gallery->media_count]);
        }

        return back()->with('success', 'Media removed from gallery.');
    }

    // ── Set cover (AJAX) ─────────────────────────────────────────────

    public function setCover(Request $request, Gallery $gallery)
    {
        $request->validate(['media_id' => ['required', 'integer']]);

        // Ensure the media actually belongs to this gallery
        $isMember = $gallery->media()->where('media.id', $request->media_id)->exists();
        if (!$isMember) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'The selected media does not belong to this gallery.'
                ], 422);
            }
            return back()->with('error', 'The selected media does not belong to this gallery.');
        }

        $gallery->update(['cover_media_id' => $request->media_id]);
        $media = Media::find($request->media_id);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'   => true,
                'message'   => 'Cover photo updated successfully.',
                'media_id'  => $media->id,
                'media_url' => $media ? $media->url() : '',
                'is_video'  => $media ? str_starts_with($media->mime_type, 'video/') : false,
            ]);
        }
        return back()->with('success', 'Cover photo updated successfully.');
    }

    // ── Reorder (AJAX drag-drop) ─────────────────────────────────────

    public function reorder(Request $request, Gallery $gallery)
    {
        $request->validate(['order' => ['required', 'array']]);
        DB::beginTransaction();
        foreach ($request->order as $i => $mid) {
            DB::table('gallery_media')
                ->where('gallery_id', $gallery->id)
                ->where('media_id', (int) $mid)
                ->update(['sort_order' => $i]);
        }
        DB::commit();

        return response()->json(['success' => true]);
    }

    // ── Category CRUD ─────────────────────────────────────────────────

    public function categories()
    {
        $categories = GalleryCategory::orderBy('sort_order')->get();
        return view('admin.gallery.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name'       => ['required', 'string', 'max:100'],
            'name_id'    => ['nullable', 'string', 'max:100'],
            'slug'       => ['required', 'string', 'max:60', 'regex:/^[a-z0-9_-]+$/', 'unique:gallery_categories,slug'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $cat = GalleryCategory::create([
            'name'       => $request->name,
            'name_id'    => $request->name_id,
            'slug'       => $request->slug,
            'sort_order' => $request->sort_order ?? 0,
            'is_active'  => true,
        ]);
        $cat->syncTranslations();

        return back()->with('success', 'Category "' . $request->name . '" added.');
    }

    public function updateCategory(Request $request, GalleryCategory $category)
    {
        $request->validate([
            'name'       => ['required', 'string', 'max:100'],
            'name_id'    => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer'],
            'is_active'  => ['nullable'],
        ]);

        $category->update([
            'name'       => $request->name,
            'name_id'    => $request->name_id,
            'sort_order' => $request->sort_order ?? $category->sort_order,
            'is_active'  => $request->has('is_active'),
        ]);
        $category->syncTranslations();

        if ($request->wantsJson()) return response()->json(['success' => true]);
        return back()->with('success', 'Category updated.');
    }

    public function destroyCategory(GalleryCategory $category)
    {
        // Unset from galleries using this category
        Gallery::where('category_slug', $category->slug)->update(['category_slug' => null]);
        $category->delete();
        return back()->with('success', 'Category deleted.');
    }

    // ── Private: Store a single media file ────────────────────────────

    private function storeMediaFile(\Illuminate\Http\UploadedFile $file): ?Media
    {
        $mime = $file->getMimeType();
        $allowed = array_merge(self::ALLOWED_IMAGE_MIMES, self::ALLOWED_VIDEO_MIMES);
        if (!in_array($mime, $allowed, true)) {
            return null;
        }

        $fileHash = md5_file($file->getRealPath());

        // Dedup: if same file already stored, return existing
        $existing = Media::where('file_hash', $fileHash)->first();
        if ($existing) {
            return $existing;
        }

        $ext      = strtolower($file->getClientOriginalExtension());
        $safeName = Str::uuid() . '.' . $ext;

        // Determine folder
        $folder = str_starts_with($mime, 'video/') ? 'gallery/videos' : 'gallery/images';
        $path   = $file->storeAs($folder, $safeName, 'public');

        return Media::create([
            'filename'      => $safeName,
            'original_name' => $file->getClientOriginalName(),
            'mime_type'     => $mime,
            'size'          => $file->getSize(),
            'path'          => $path,
            'file_hash'     => $fileHash,
            'alt'           => '',
            'title'         => '',
            'category'      => 'gallery',
            'is_public'     => true,
            'is_gallery'    => false, // managed via gallery_media
            'sort_order'    => 0,
        ]);
    }

    // ── Private: Extract ZIP and store media ──────────────────────────

    private function extractAndStoreZip(\Illuminate\Http\UploadedFile $zipFile, Gallery $gallery): array
    {
        $zip = new ZipArchive();
        $tmpPath = $zipFile->getRealPath();

        if ($zip->open($tmpPath) !== true) {
            throw new \RuntimeException('Could not open ZIP file.');
        }

        // ZIP bomb guard: check uncompressed size
        $totalUncompressed = 0;
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);
            $totalUncompressed += $stat['size'];
            if ($totalUncompressed > self::MAX_ZIP_UNCOMPRESSED) {
                $zip->close();
                throw new \RuntimeException('ZIP file is too large (max 500 MB uncompressed).');
            }
        }

        if ($zip->numFiles > self::MAX_ZIP_FILES) {
            $zip->close();
            throw new \RuntimeException('ZIP contains too many files (max ' . self::MAX_ZIP_FILES . ').');
        }

        $mediaIds   = [];
        $tempDir    = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sazara_zip_' . Str::random(16);
        @mkdir($tempDir, 0755, true);

        try {
            $allowedExts  = ['jpg', 'jpeg', 'png', 'webp', 'mp4', 'mov', 'avi', 'mpeg'];
            $allowedMimes = array_merge(self::ALLOWED_IMAGE_MIMES, self::ALLOWED_VIDEO_MIMES);

            for ($i = 0; $i < $zip->numFiles; $i++) {
                $stat = $zip->statIndex($i);
                $name = str_replace('\\', '/', $stat['name']);

                // Skip directories, hidden files, and OS metadata (__MACOSX, etc.)
                if (str_ends_with($name, '/') || str_contains($name, '__MACOSX') || str_starts_with(basename($name), '.')) {
                    continue;
                }

                $basename = basename($name);

                // Extension check
                $ext = strtolower(pathinfo($basename, PATHINFO_EXTENSION));
                if (!in_array($ext, $allowedExts, true)) {
                    continue;
                }

                // Extract stream directly to temp file
                $stream = $zip->getStream($stat['name']);
                if (!$stream) {
                    continue;
                }

                $tmpFilePath = $tempDir . DIRECTORY_SEPARATOR . Str::random(8) . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $basename);
                $outHandle = fopen($tmpFilePath, 'wb');
                stream_copy_to_stream($stream, $outHandle);
                fclose($stream);
                fclose($outHandle);

                if (!file_exists($tmpFilePath) || filesize($tmpFilePath) === 0) {
                    @unlink($tmpFilePath);
                    continue;
                }

                // MIME check via finfo
                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                $detectedMime = $finfo->file($tmpFilePath);
                if (!in_array($detectedMime, $allowedMimes, true)) {
                    @unlink($tmpFilePath);
                    continue;
                }

                // Hash for dedup
                $fileHash = md5_file($tmpFilePath);
                $existing = Media::where('file_hash', $fileHash)->first();
                if ($existing) {
                    @unlink($tmpFilePath);
                    $mediaIds[] = $existing->id;
                    continue;
                }

                // Store to public disk using putFileAs
                $safeName   = Str::uuid() . '.' . $ext;
                $folder     = str_starts_with($detectedMime, 'video/') ? 'gallery/videos' : 'gallery/images';
                $storedPath = Storage::disk('public')->putFileAs($folder, new \Illuminate\Http\File($tmpFilePath), $safeName);
                $fileSize   = filesize($tmpFilePath);
                @unlink($tmpFilePath);

                if (!$storedPath) {
                    continue;
                }

                $media = Media::create([
                    'filename'      => $safeName,
                    'original_name' => $basename,
                    'mime_type'     => $detectedMime,
                    'size'          => $fileSize,
                    'path'          => $storedPath,
                    'file_hash'     => $fileHash,
                    'alt'           => '',
                    'title'         => '',
                    'category'      => $gallery->category_slug ?? 'gallery',
                    'is_public'     => true,
                    'is_gallery'    => false,
                    'sort_order'    => 0,
                ]);

                $mediaIds[] = $media->id;
            }
        } finally {
            $zip->close();
            // Cleanup temp dir
            if (is_dir($tempDir)) {
                $files = glob($tempDir . '/*');
                if ($files) {
                    array_map('unlink', $files);
                }
                @rmdir($tempDir);
            }
        }

        return $mediaIds;
    }
}
