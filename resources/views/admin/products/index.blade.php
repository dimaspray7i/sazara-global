@extends('admin.layouts.admin')

@section('title', 'Products')
@section('page-title', 'Manage Products')
@section('breadcrumb')
    <span class="adm-breadcrumb-sep">/</span>
    <span>Products</span>
@endsection

@section('content')
<div class="adm-card">
    <div class="adm-card-header">
        <div class="adm-card-title">All Products ({{ $products->count() }})</div>
        <a class="adm-btn adm-btn-green" href="{{ route('admin.products.create') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add New Product
        </a>
    </div>

    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th style="width: 60px;">Order</th>
                    <th>Product</th>
                    <th>Featured</th>
                    <th>Status</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($products as $p)
                <tr>
                    <td>
                        <span class="adm-badge adm-badge-navy">#{{ $p->sort_order }}</span>
                    </td>
                    <td>
                        <div class="adm-row-info">
                            <img class="adm-table-thumb" src="{{ $p->imageUrl() }}" alt="{{ $p->name }}" onerror="this.src='{{ asset('images/hero.jpg') }}'">
                            <div>
                                <div class="adm-row-name">{{ $p->name }}</div>
                                <div class="adm-row-sub">/products/{{ $p->slug }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($p->is_featured)
                            <span class="adm-badge adm-badge-green">Featured</span>
                        @else
                            <span class="adm-badge adm-badge-gray">Standard</span>
                        @endif
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.products.toggle', $p) }}" style="display:inline">
                            @csrf
                            <button type="submit" class="adm-badge {{ $p->is_active ? 'adm-badge-green' : 'adm-badge-red' }}" style="cursor:pointer;border:none">
                                {{ $p->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </form>
                    </td>
                    <td style="text-align: right;">
                        <div class="adm-flex adm-gap-8" style="justify-content: flex-end;">
                            <a class="adm-btn-icon" href="{{ route('products.show', $p->slug) }}" target="_blank" title="View on site">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                            </a>
                            <a class="adm-btn-icon" href="{{ route('admin.products.edit', $p) }}" title="Edit product">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </a>
                            <form method="POST" action="{{ route('admin.products.destroy', $p) }}" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="adm-btn-icon danger" title="Delete product" data-confirm-delete="Delete product '{{ $p->name }}'? This action cannot be undone.">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="adm-empty">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                        <div class="adm-empty-title">No products found</div>
                        <p>Create your first product to display on the website.</p>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
