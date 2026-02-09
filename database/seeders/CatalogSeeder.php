<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Label;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedCategories();
        $this->seedBrands();
        $this->seedLabels();
    }

    private function seedCategories(): void
    {
        $categories = [
            ['name' => 'Πουκάμισα', 'children' => ['Casual', 'Επίσημα', 'Λινά']],
            ['name' => 'Μπλούζες & T-Shirts', 'children' => ['T-Shirts', 'Polo', 'Μακρυμάνικα']],
            ['name' => 'Παντελόνια', 'children' => ['Chinos', 'Jeans', 'Επίσημα Παντελόνια']],
            ['name' => 'Κοστούμια', 'children' => ['Σακάκια', 'Κοστούμια Set', 'Γιλέκα']],
            ['name' => 'Πλεκτά & Φούτερ', 'children' => ['Πουλόβερ', 'Ζακέτες', 'Φούτερ']],
            ['name' => 'Μπουφάν & Παλτό', 'children' => ['Μπουφάν', 'Παλτό', 'Αντιανεμικά']],
            ['name' => 'Αξεσουάρ', 'children' => ['Γραβάτες', 'Ζώνες', 'Κάλτσες', 'Εσώρουχα']],
        ];

        foreach ($categories as $sortOrder => $data) {
            $parent = Category::firstOrCreate(
                ['slug' => Str::slug($data['name'])],
                [
                    'name' => $data['name'],
                    'sort_order' => $sortOrder,
                    'is_visible' => true,
                ]
            );

            foreach ($data['children'] as $childSort => $childName) {
                Category::firstOrCreate(
                    ['slug' => Str::slug($childName)],
                    [
                        'parent_id' => $parent->id,
                        'name' => $childName,
                        'sort_order' => $childSort,
                        'is_visible' => true,
                    ]
                );
            }
        }
    }

    private function seedBrands(): void
    {
        $brands = ['Dressman'];

        foreach ($brands as $name) {
            Brand::firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'is_visible' => true,
                ]
            );
        }
    }

    private function seedLabels(): void
    {
        $labels = [
            ['name' => 'Νέο', 'color' => '#22c55e'],
            ['name' => 'Προσφορά', 'color' => '#ef4444'],
            ['name' => 'Best Seller', 'color' => '#f59e0b'],
            ['name' => 'Limited Edition', 'color' => '#8b5cf6'],
        ];

        foreach ($labels as $label) {
            Label::firstOrCreate(
                ['slug' => Str::slug($label['name'])],
                [
                    'name' => $label['name'],
                    'color' => $label['color'],
                ]
            );
        }
    }
}
