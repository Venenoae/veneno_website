<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class NewsEvent extends Model
{
    use HasFactory;

    protected $table = 'news_events';

    protected $fillable = [
        'title',
        'title_ar',
        'slug',
        'type',
        'category',
        'event_date',
        'event_time',
        'location',
        'location_ar',
        'summary',
        'summary_ar',
        'content',
        'content_ar',
        'image_url',
        'badge',
        'badge_ar',
        'status',
        'is_featured',
        'is_past',
        'prize_podium',
        'gallery_images',
        'views_count',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'datetime',
            'gallery_images' => 'array',
            'is_featured' => 'boolean',
            'is_past' => 'boolean',
            'views_count' => 'integer',
        ];
    }

    /**
     * Boot model to automatically generate slug if missing.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            if (empty($item->slug)) {
                $baseSlug = Str::slug($item->title);
                $slug = $baseSlug ?: 'news-event-' . time();
                $count = static::where('slug', 'LIKE', "{$slug}%")->count();
                $item->slug = $count > 0 ? "{$slug}-" . ($count + 1) : $slug;
            }
        });
    }

    /**
     * Relationship to creator user.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope for published items.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope for events.
     */
    public function scopeEvents($query)
    {
        return $query->where('type', 'event');
    }

    /**
     * Scope for news.
     */
    public function scopeNews($query)
    {
        return $query->where('type', 'news');
    }

    /**
     * Scope for featured items.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
