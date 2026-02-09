<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedCategories();
        $this->seedTags();
        $this->seedPosts();
    }

    private function seedCategories(): void
    {
        $categories = ['News', 'Tutorials', 'Product Updates', 'Style Guide'];

        foreach ($categories as $name) {
            BlogCategory::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }
    }

    private function seedTags(): void
    {
        $tags = [
            'Tips', 'How-to', 'New Arrivals',
            'Sustainability', 'Trends', 'Behind the Scenes',
        ];

        foreach ($tags as $name) {
            BlogTag::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }
    }

    private function seedPosts(): void
    {
        $admin = User::where('is_admin', true)->first();
        if (! $admin) {
            return;
        }

        $categories = BlogCategory::all();
        $tags = BlogTag::all();

        $posts = [
            [
                'title' => 'Welcome to Our Blog',
                'excerpt' => 'We are excited to launch our new blog where we will share updates, tips, and insights.',
                'body' => '<p>Welcome to our blog! Here you will find the latest news about our products, helpful guides, and insights into the world of online shopping.</p><p>Stay tuned for more updates and do not forget to check back regularly for new content.</p>',
            ],
            [
                'title' => 'How to Choose the Right Product',
                'excerpt' => 'A comprehensive guide to finding exactly what you need in our store.',
                'body' => '<p>Shopping online can be overwhelming with so many options available. In this guide, we will walk you through the key factors to consider when making your purchase.</p><p>Start by identifying your needs, compare specifications, and read our detailed product descriptions to make an informed decision.</p>',
            ],
            [
                'title' => '5 Tips for Sustainable Shopping',
                'excerpt' => 'Make eco-friendly choices without compromising on quality.',
                'body' => '<p>Sustainability matters, and small changes in our shopping habits can make a big difference. Here are five practical tips:</p><ul><li>Choose quality over quantity</li><li>Look for eco-friendly materials</li><li>Support local brands</li><li>Reduce packaging waste</li><li>Take care of your purchases to extend their life</li></ul>',
            ],
        ];

        foreach ($posts as $i => $data) {
            $post = BlogPost::firstOrCreate(
                ['slug' => Str::slug($data['title'])],
                [
                    'author_id' => $admin->id,
                    'title' => $data['title'],
                    'excerpt' => $data['excerpt'],
                    'body' => $data['body'],
                    'is_published' => true,
                    'published_at' => now()->subDays(count($posts) - $i),
                ]
            );

            if ($categories->isNotEmpty()) {
                $post->categories()->syncWithoutDetaching(
                    $categories->random(min(2, $categories->count()))->pluck('id')
                );
            }

            if ($tags->isNotEmpty()) {
                $post->tags()->syncWithoutDetaching(
                    $tags->random(min(3, $tags->count()))->pluck('id')
                );
            }
        }
    }
}
