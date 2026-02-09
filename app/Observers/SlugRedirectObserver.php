<?php

namespace App\Observers;

use App\Models\UrlRedirect;
use Illuminate\Database\Eloquent\Model;

class SlugRedirectObserver
{
    /**
     * When a model's slug changes, record the old slug for 301 redirect.
     */
    public function updating(Model $model): void
    {
        if (! $model->isDirty('slug')) {
            return;
        }

        $oldSlug = $model->getOriginal('slug');
        /** @var string $newSlug */
        $newSlug = $model->getAttribute('slug');

        if ($oldSlug === null || $oldSlug === '' || $oldSlug === $newSlug) {
            return;
        }

        $type = match (true) {
            $model instanceof \App\Models\Product => 'product',
            $model instanceof \App\Models\Category => 'category',
            $model instanceof \App\Models\BlogPost => 'blog_post',
            default => 'other',
        };

        UrlRedirect::updateOrCreate(
            ['old_slug' => $oldSlug, 'type' => $type],
            ['new_slug' => $newSlug]
        );

        // Collapse chains: any redirect pointing to the old slug now points to the new slug
        UrlRedirect::where('new_slug', $oldSlug)->where('type', $type)->update(['new_slug' => $newSlug]);

        // Clean up circular redirects: if old_slug pointed to this slug, remove it
        UrlRedirect::where('old_slug', $newSlug)->where('type', $type)->delete();
    }
}
