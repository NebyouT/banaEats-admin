<?php

namespace App\Http\Controllers;

use App\Models\BuilderPage;
use Illuminate\Http\Request;

class PageBuilderPublicController extends Controller
{
    /**
     * Render a published page for WebView display
     */
    public function render(Request $request, $slug)
    {
        $page = BuilderPage::where('slug', $slug)
            ->published()
            ->with(['sections.components'])
            ->first();

        if (!$page) {
            abort(404, 'Page not found');
        }

        // Track view
        $page->views()->create([
            'user_id' => $request->user()?->id,
            'session_id' => session()->getId(),
            'device_type' => $request->header('User-Agent'),
            'referrer' => $request->header('Referer'),
        ]);

        return view('admin-views.page-builder.render', compact('page'));
    }
}
