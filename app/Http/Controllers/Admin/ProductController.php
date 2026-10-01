<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::ordered()->get();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $product = new Product();
        $media   = Media::latest()->get();
        return view('admin.products.form', compact('product', 'media'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['name']);

        // Handle image
        $imagePath = $this->handleImageUpload($request, null);
        if ($imagePath !== null) {
            $data['image'] = $imagePath;
        }

        Product::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $media = Media::latest()->get();
        return view('admin.products.form', compact('product', 'media'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validated($request, $product);
        $data['slug'] = Str::slug($data['name']);

        // Handle image
        $imagePath = $this->handleImageUpload($request, $product);
        if ($imagePath !== null) {
            // Delete old image if it was from storage
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $imagePath;
        }

        // Allow clearing image
        if ($request->boolean('clear_image') && $product->image) {
            if (Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = null;
        }

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted.');
    }

    public function toggle(Product $product)
    {
        $product->update(['is_active' => ! $product->is_active]);
        return back()->with('success', 'Product status updated.');
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        $slugRule = $product ? 'unique:products,slug,' . $product->id : 'unique:products,slug';

        return $request->validate([
            'name'             => ['required', 'string', 'max:200'],
            'name_id'          => ['nullable', 'string', 'max:200'],
            'description'      => ['required', 'string'],
            'description_id'   => ['nullable', 'string'],
            'specification'    => ['nullable', 'string'],
            'specification_id' => ['nullable', 'string'],
            'wa_template'      => ['nullable', 'string', 'max:500'],
            'is_featured'      => ['boolean'],
            'is_active'        => ['boolean'],
            'sort_order'       => ['integer', 'min:0'],
        ]);
    }

    private function handleImageUpload(Request $request, ?Product $product): ?string
    {
        // Media library selection (path of existing media)
        if ($request->filled('media_path')) {
            return $request->input('media_path');
        }

        // Direct file upload
        if ($request->hasFile('image_file')) {
            $file     = $request->file('image_file');
            $mime     = $file->getMimeType();
            $allowed  = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            if (! in_array($mime, $allowed, true)) return null;

            $ext  = strtolower($file->getClientOriginalExtension());
            $name = Str::uuid() . '.' . $ext;
            return $file->storeAs('media', $name, 'public');
        }

        return null;
    }
}
