<?php

namespace Database\Seeders;

use App\Models\RealEstate\Category;
use App\Models\RealEstate\SubCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Kdabrow\SeederOnce\SeederOnce;

class SubCategorySeeder extends SeederOnce
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            ['label' => 'Location', 'is_published' => true],
            ['label' => 'Location individuelle', 'is_published' => true],
            ['label' => 'Résidence étudiant', 'is_published' => true],
        ];

        foreach ($items as $item) {
            SubCategory::updateOrCreate(['label' => $item['label']], $item);
        }

        $category = Category::find(3);
        $category->update(['label' => 'Location étudiante']);
    }
}
