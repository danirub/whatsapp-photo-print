<?php

namespace Database\Seeders;

use App\Models\PrintSize;
use Illuminate\Database\Seeder;

class PrintSizeSeeder extends Seeder
{
    public function run(): void
    {
        $sizes = [
            ['label' => 'A', 'name' => 'Small', 'dimensions' => '10x15 cm', 'price' => 2.50, 'sort_order' => 1],
            ['label' => 'B', 'name' => 'Medium', 'dimensions' => '13x18 cm', 'price' => 4.00, 'sort_order' => 2],
            ['label' => 'C', 'name' => 'Large', 'dimensions' => '15x21 cm', 'price' => 6.00, 'sort_order' => 3],
        ];

        foreach ($sizes as $size) {
            PrintSize::firstOrCreate(['label' => $size['label']], array_merge($size, ['is_active' => true]));
        }
    }
}
