<?php

use App\Http\Controllers\Blog;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\GuestOrderController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/product-category/{slug}', [CategoryController::class, 'show'])->name('category.show');
Route::get('/category/{slug}', fn (string $slug) => redirect()->route('category.show', $slug, 301));
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show');
Route::get('/search', fn () => view('pages.search'))->name('search');
Route::get('/metapoiisi', fn () => view('pages.metapoiisi'))->name('metapoiisi');
Route::get('/made-to-measure', fn () => view('pages.made-to-measure'))->name('made-to-measure');

// Legal / Info pages
Route::get('/about', fn () => view('pages.about'))->name('about');
Route::get('/privacy', fn () => view('pages.privacy'))->name('privacy');
Route::get('/terms', fn () => view('pages.terms'))->name('terms');
Route::get('/cookie-policy', fn () => view('pages.cookie-policy'))->name('cookie-policy');
Route::get('/payment-methods', fn () => view('pages.payment-methods'))->name('payment-methods');
Route::get('/returns', fn () => view('pages.returns'))->name('returns');
Route::get('/shipping', fn () => view('pages.shipping'))->name('shipping');
Route::get('/contact', fn () => view('pages.contact'))->name('contact');
Route::get('/faq', fn () => view('pages.faq'))->name('faq');
Route::get('/simata-gia-plysimo-roychon', fn () => view('pages.simata-gia-plysimo-roychon'))->name('simata-gia-plysimo-roychon');

Route::get('/api/products/load-more', [HomeController::class, 'loadMore'])->name('products.loadMore');
Route::get('/api/category/{slug}/load-more', [CategoryController::class, 'loadMore'])->name('category.loadMore');

Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::get('/cart/drawer', [CartController::class, 'drawer'])->name('cart.drawer');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/{item}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{item}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
Route::get('/wishlist/drawer', [WishlistController::class, 'drawer'])->name('wishlist.drawer');
Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
Route::delete('/wishlist/{item}', [WishlistController::class, 'remove'])->name('wishlist.remove');
Route::post('/wishlist/{item}/move-to-cart', [WishlistController::class, 'moveToCart'])->name('wishlist.moveToCart');

Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'store'])->middleware('throttle:checkout')->name('checkout.store');
Route::post('/checkout/pay', [CheckoutController::class, 'pay'])->middleware('throttle:checkout')->name('checkout.pay');
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');

Route::get('/order/{orderNumber}', [GuestOrderController::class, 'show'])->name('order.guest.show');

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])->middleware('throttle:stripe-webhook')->name('stripe.webhook');

// Blog
Route::get('/blog', [Blog\PostController::class, 'index'])->name('blog.index');
Route::get('/blog/category/{slug}', [Blog\CategoryController::class, 'show'])->name('blog.category');
Route::get('/blog/tag/{slug}', [Blog\TagController::class, 'show'])->name('blog.tag');
Route::get('/blog/{slug}', [Blog\PostController::class, 'show'])->name('blog.show');
Route::post('/blog/{slug}/comment', [Blog\PostController::class, 'storeComment'])->middleware('throttle:6,1')->name('blog.comment.store');

// Load more (AJAX)
Route::get('/api/blog/load-more', [Blog\PostController::class, 'indexLoadMore'])->name('blog.loadMore');
Route::get('/api/blog/category/{slug}/load-more', [Blog\CategoryController::class, 'loadMore'])->name('blog.category.loadMore');
Route::get('/api/blog/tag/{slug}/load-more', [Blog\TagController::class, 'loadMore'])->name('blog.tag.loadMore');
Route::get('/api/blog/{slug}/comments/load-more', [Blog\PostController::class, 'loadMoreComments'])->name('blog.comments.loadMore');
