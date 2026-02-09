<?php

use App\Models\BlogCategory;
use App\Models\BlogComment;
use App\Models\BlogPost;
use App\Models\BlogTag;
use App\Models\UrlRedirect;
use App\Models\User;

// ──── Helper ────

function createBlogPost(array $overrides = []): BlogPost
{
    $post = BlogPost::create(array_merge([
        'title' => 'Test Post',
        'slug' => 'test-post-'.Str::random(6),
        'excerpt' => 'Test excerpt',
        'body' => '<p>Test body content</p>',
        'is_published' => true,
        'published_at' => now()->subDay(),
    ], $overrides));
    $post->refresh();

    return $post;
}

function createBlogAuthor(): User
{
    $user = User::create([
        'name' => 'Blog Author',
        'email' => 'author-'.Str::random(6).'@test.com',
        'password' => bcrypt('password'),
        'is_admin' => true,
    ]);
    $user->refresh();

    return $user;
}

// ──── Blog Index ────

it('displays the blog index page with published posts', function () {
    $post1 = createBlogPost(['title' => 'First Article', 'slug' => 'first-article']);
    $post2 = createBlogPost(['title' => 'Second Article', 'slug' => 'second-article']);

    $response = $this->get('/blog');

    $response->assertOk();
    $response->assertSee('First Article');
    $response->assertSee('Second Article');
});

it('hides unpublished posts from blog index', function () {
    createBlogPost(['title' => 'Published Post', 'slug' => 'published-post']);
    createBlogPost(['title' => 'Draft Post', 'slug' => 'draft-post', 'is_published' => false]);

    $response = $this->get('/blog');

    $response->assertOk();
    $response->assertSee('Published Post');
    $response->assertDontSee('Draft Post');
});

it('paginates blog posts at 12 per page', function () {
    for ($i = 1; $i <= 13; $i++) {
        createBlogPost(['title' => "Post $i", 'slug' => "post-$i", 'published_at' => now()->subMinutes($i)]);
    }

    $page1 = $this->get('/blog');
    $page1->assertOk();
    $page1->assertSee('Post 1');
    $page1->assertSee('Post 12');
    $page1->assertDontSee('Post 13');

    $page2 = $this->get('/blog?page=2');
    $page2->assertOk();
    $page2->assertSee('Post 13');
});

// ──── Single Post ────

it('displays a single blog post', function () {
    $post = createBlogPost([
        'title' => 'My Detailed Post',
        'slug' => 'my-detailed-post',
        'body' => '<p>Detailed content here</p>',
    ]);

    $response = $this->get('/blog/my-detailed-post');

    $response->assertOk();
    $response->assertSee('My Detailed Post');
    $response->assertSee('Detailed content here');
});

it('returns 404 for non-existent blog post', function () {
    $response = $this->get('/blog/does-not-exist');

    $response->assertNotFound();
});

it('redirects from old blog slug via url_redirects', function () {
    $post = createBlogPost(['slug' => 'new-slug']);

    UrlRedirect::create([
        'old_slug' => 'old-slug',
        'new_slug' => 'new-slug',
        'type' => 'blog_post',
    ]);

    $response = $this->get('/blog/old-slug');

    $response->assertRedirect(route('blog.show', 'new-slug'));
    $response->assertStatus(301);
});

it('outputs BlogPosting JSON-LD on single post page', function () {
    $author = createBlogAuthor();
    $post = createBlogPost([
        'title' => 'JSON-LD Post',
        'slug' => 'json-ld-post',
        'author_id' => $author->id,
    ]);

    $response = $this->get('/blog/json-ld-post');

    $response->assertOk();
    $response->assertSee('"@type":"BlogPosting"', false);
    $response->assertSee('"headline":"JSON-LD Post"', false);
});

it('sanitizes blog post body with clean_html', function () {
    $post = createBlogPost([
        'slug' => 'xss-post',
        'body' => '<p>Safe content</p><script>alert("xss")</script>',
    ]);

    $response = $this->get('/blog/xss-post');

    $response->assertOk();
    $response->assertSee('Safe content');
    $response->assertDontSee('<script>', false);
});

// ──── Categories & Tags ────

it('displays posts filtered by blog category', function () {
    $category = BlogCategory::create(['name' => 'Tech', 'slug' => 'tech']);
    $category->refresh();

    $post = createBlogPost(['title' => 'Tech Article', 'slug' => 'tech-article']);
    $post->categories()->attach($category->id);

    $other = createBlogPost(['title' => 'Other Article', 'slug' => 'other-article']);

    $response = $this->get('/blog/category/tech');

    $response->assertOk();
    $response->assertSee('Tech Article');
    $response->assertDontSee('Other Article');
});

it('displays posts filtered by blog tag', function () {
    $tag = BlogTag::create(['name' => 'Laravel', 'slug' => 'laravel']);
    $tag->refresh();

    $post = createBlogPost(['title' => 'Laravel Tips', 'slug' => 'laravel-tips']);
    $post->tags()->attach($tag->id);

    $other = createBlogPost(['title' => 'Untagged Post', 'slug' => 'untagged-post']);

    $response = $this->get('/blog/tag/laravel');

    $response->assertOk();
    $response->assertSee('Laravel Tips');
    $response->assertDontSee('Untagged Post');
});

it('returns 404 for non-existent blog category', function () {
    $response = $this->get('/blog/category/fake-category');

    $response->assertNotFound();
});

// ──── Comments ────

it('allows submitting a comment on a blog post', function () {
    $post = createBlogPost(['slug' => 'commentable-post']);

    $response = $this->post('/blog/commentable-post/comment', [
        'author_name' => 'John Doe',
        'author_email' => 'john@example.com',
        'body' => 'Great article!',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('blog_comments', [
        'blog_post_id' => $post->id,
        'author_name' => 'John Doe',
        'is_approved' => false,
    ]);
});

it('validates comment form fields', function () {
    $post = createBlogPost(['slug' => 'validated-post']);

    $response = $this->post('/blog/validated-post/comment', []);

    $response->assertSessionHasErrors(['author_name', 'author_email', 'body']);
});

it('does not display unapproved comments', function () {
    $post = createBlogPost(['slug' => 'moderated-post']);

    BlogComment::create([
        'blog_post_id' => $post->id,
        'author_name' => 'Spammer',
        'author_email' => 'spam@example.com',
        'body' => 'Buy cheap stuff!',
        'is_approved' => false,
    ]);

    $response = $this->get('/blog/moderated-post');

    $response->assertOk();
    $response->assertDontSee('Buy cheap stuff!');
});

// ──── Related Posts ────

it('shows related posts sharing categories', function () {
    $category = BlogCategory::create(['name' => 'Shared', 'slug' => 'shared']);
    $category->refresh();

    $post = createBlogPost(['title' => 'Main Post', 'slug' => 'main-post']);
    $post->categories()->attach($category->id);

    $related = createBlogPost(['title' => 'Related Post', 'slug' => 'related-post']);
    $related->categories()->attach($category->id);

    $unrelated = createBlogPost(['title' => 'Unrelated Post', 'slug' => 'unrelated-post']);

    $response = $this->get('/blog/main-post');

    $response->assertOk();
    $response->assertSee('Related Post');
    $response->assertDontSee('Unrelated Post');
});
