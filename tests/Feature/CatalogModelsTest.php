<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\Label;

it('creates a root category', function () {
    $category = Category::create([
        'name' => 'Electronics',
        'slug' => 'electronics',
        'is_visible' => true,
        'sort_order' => 0,
    ]);

    expect($category->exists)->toBeTrue();
    expect($category->parent_id)->toBeNull();
    expect($category->is_visible)->toBeTrue();
});

it('creates a child category with parent', function () {
    $parent = Category::create([
        'name' => 'Electronics',
        'slug' => 'electronics',
    ]);

    $child = Category::create([
        'name' => 'Smartphones',
        'slug' => 'smartphones',
        'parent_id' => $parent->id,
    ]);

    expect($child->parent->id)->toBe($parent->id);
    expect($parent->children)->toHaveCount(1);
    expect($parent->children->first()->name)->toBe('Smartphones');
});

it('scopes visible categories', function () {
    Category::create(['name' => 'Visible', 'slug' => 'visible', 'is_visible' => true]);
    Category::create(['name' => 'Hidden', 'slug' => 'hidden', 'is_visible' => false]);

    expect(Category::visible()->count())->toBe(1);
    expect(Category::visible()->first()->name)->toBe('Visible');
});

it('scopes root categories', function () {
    $parent = Category::create(['name' => 'Parent', 'slug' => 'parent']);
    Category::create(['name' => 'Child', 'slug' => 'child', 'parent_id' => $parent->id]);

    expect(Category::root()->count())->toBe(1);
    expect(Category::root()->first()->name)->toBe('Parent');
});

it('creates a brand', function () {
    $brand = Brand::create([
        'name' => 'TestBrand',
        'slug' => 'testbrand',
        'is_visible' => true,
    ]);

    expect($brand->exists)->toBeTrue();
    expect($brand->is_visible)->toBeTrue();
});

it('scopes visible brands', function () {
    Brand::create(['name' => 'Visible', 'slug' => 'visible', 'is_visible' => true]);
    Brand::create(['name' => 'Hidden', 'slug' => 'hidden', 'is_visible' => false]);

    expect(Brand::visible()->count())->toBe(1);
});

it('creates a label with color', function () {
    $label = Label::create([
        'name' => 'Sale',
        'slug' => 'sale',
        'color' => '#ef4444',
    ]);

    expect($label->exists)->toBeTrue();
    expect($label->color)->toBe('#ef4444');
});
