<?php

namespace App\Http\Controllers;

use App\Models\NewsEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\File;

class NewsEventController extends Controller
{
    /**
     * Public News & Events Showcase Page.
     */
    public function index(Request $request, ?string $locale = null): Response
    {
        $currentLocale = $locale ?: app()->getLocale();

        $items = NewsEvent::published()
            ->orderBy('is_featured', 'desc')
            ->orderBy('event_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $featured = $items->firstWhere('is_featured', true) ?: $items->first();

        return Inertia::render('Storefront/NewsEvents', [
            'locale' => $currentLocale,
            'items' => $items,
            'featured' => $featured,
        ]);
    }

    /**
     * Public Detail Page for Single News or Event.
     */
    public function show(Request $request, string $slug, ?string $locale = null): Response
    {
        $currentLocale = $locale ?: app()->getLocale();

        $item = NewsEvent::where('slug', $slug)->firstOrFail();
        $item->increment('views_count');

        $related = NewsEvent::published()
            ->where('id', '!=', $item->id)
            ->where('type', $item->type)
            ->latest('event_date')
            ->take(3)
            ->get();

        return Inertia::render('Storefront/NewsEventDetail', [
            'locale' => $currentLocale,
            'item' => $item,
            'related' => $related,
        ]);
    }

    /**
     * Super Admin: Store new News or Event.
     */
    public function store(Request $request): RedirectResponse
    {
        $currentUser = $request->user();
        if (!$currentUser || !$currentUser->hasRole('super_admin')) {
            abort(403, 'Unauthorized: Only Super Administrators can publish news and events.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:news_events,slug',
            'type' => 'required|in:event,news',
            'category' => 'nullable|string|max:100',
            'event_date' => 'nullable|date',
            'event_time' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:255',
            'location_ar' => 'nullable|string|max:255',
            'summary' => 'required|string',
            'summary_ar' => 'nullable|string',
            'content' => 'required|string',
            'content_ar' => 'nullable|string',
            'badge' => 'nullable|string|max:100',
            'badge_ar' => 'nullable|string|max:100',
            'status' => 'required|in:published,draft,archived',
            'is_featured' => 'boolean',
            'is_past' => 'boolean',
            'prize_podium' => 'nullable|string|max:255',
            'image_url' => 'nullable|string|max:500',
            'image' => 'nullable|image|max:10240', // 10MB max
        ]);

        // Process uploaded image if present
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'news_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $destPath = public_path('images/news-events');
            if (!File::exists($destPath)) {
                File::makeDirectory($destPath, 0755, true);
            }
            $file->move($destPath, $filename);
            $validated['image_url'] = '/images/news-events/' . $filename;
        }

        if (empty($validated['image_url'])) {
            $validated['image_url'] = $validated['type'] === 'event' 
                ? '/images/hammer/Hammer1.jpeg' 
                : '/images/main-branch.webp';
        }

        $validated['created_by'] = $currentUser->id;

        // Auto mark past if event date has passed
        if (!empty($validated['event_date']) && strtotime($validated['event_date']) < time()) {
            $validated['is_past'] = true;
        }

        NewsEvent::create($validated);

        return redirect()->back()->with('success', 'News/Event entry has been created successfully.');
    }

    /**
     * Super Admin: Update existing News or Event.
     */
    public function update(Request $request, NewsEvent $newsEvent): RedirectResponse
    {
        $currentUser = $request->user();
        if (!$currentUser || !$currentUser->hasRole('super_admin')) {
            abort(403, 'Unauthorized: Only Super Administrators can update news and events.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:news_events,slug,' . $newsEvent->id,
            'type' => 'required|in:event,news',
            'category' => 'nullable|string|max:100',
            'event_date' => 'nullable|date',
            'event_time' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:255',
            'location_ar' => 'nullable|string|max:255',
            'summary' => 'required|string',
            'summary_ar' => 'nullable|string',
            'content' => 'required|string',
            'content_ar' => 'nullable|string',
            'badge' => 'nullable|string|max:100',
            'badge_ar' => 'nullable|string|max:100',
            'status' => 'required|in:published,draft,archived',
            'is_featured' => 'boolean',
            'is_past' => 'boolean',
            'prize_podium' => 'nullable|string|max:255',
            'image_url' => 'nullable|string|max:500',
            'image' => 'nullable|image|max:10240',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'news_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $destPath = public_path('images/news-events');
            if (!File::exists($destPath)) {
                File::makeDirectory($destPath, 0755, true);
            }
            $file->move($destPath, $filename);
            $validated['image_url'] = '/images/news-events/' . $filename;
        }

        if (!empty($validated['event_date']) && strtotime($validated['event_date']) < time()) {
            $validated['is_past'] = true;
        }

        $newsEvent->update($validated);

        return redirect()->back()->with('success', 'News/Event updated successfully.');
    }

    /**
     * Super Admin: Delete News or Event.
     */
    public function destroy(Request $request, NewsEvent $newsEvent): RedirectResponse
    {
        $currentUser = $request->user();
        if (!$currentUser || !$currentUser->hasRole('super_admin')) {
            abort(403, 'Unauthorized: Only Super Administrators can delete news and events.');
        }

        $newsEvent->delete();

        return redirect()->back()->with('success', 'News/Event item deleted successfully.');
    }

    /**
     * Super Admin: Toggle featured status.
     */
    public function toggleFeatured(Request $request, NewsEvent $newsEvent): RedirectResponse
    {
        $currentUser = $request->user();
        if (!$currentUser || !$currentUser->hasRole('super_admin')) {
            abort(403, 'Unauthorized: Only Super Administrators can modify featured status.');
        }

        $newsEvent->update(['is_featured' => !$newsEvent->is_featured]);

        return redirect()->back()->with('success', 'Featured status updated.');
    }
}
