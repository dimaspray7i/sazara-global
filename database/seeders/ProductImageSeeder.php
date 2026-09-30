<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductImageSeeder extends Seeder
{
    public function run(): void
    {
        $map = [
            'palm-broom'          => 'products/palm-broom.jpg',
            'crude-palm-oil-cpo'  => 'products/cpo.jpg',
            'coffee'              => 'products/coffee.jpg',
            'clove'               => 'products/clove.jpg',
            'cinnamon'            => 'products/cinnamon.jpg',
            'vanilla'             => 'products/vanilla.jpg',
            'areca-nut'           => 'products/areca-nut.jpg',
            'other-commodities'   => 'products/other.jpg',
        ];
        foreach ($map as $slug => $path) {
            Product::where('slug', $slug)->update(['image' => $path]);
        }
    }
}