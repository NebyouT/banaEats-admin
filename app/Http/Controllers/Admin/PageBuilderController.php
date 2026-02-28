<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\BuilderPage;
use App\Models\BuilderSection;
use App\Models\BuilderComponent;
use App\Models\BuilderTemplate;
use App\Models\Food;
use App\Models\Restaurant;
use App\Models\Category;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PageBuilderController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────────
    // PAGE CRUD
    // ─────────────────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status', 'all');

        $pages = BuilderPage::when($search, function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            })
            ->when($status !== 'all', function ($q) use ($status) {
                $q->where('status', $status === 'active' ? 1 : 0);
            })
            ->withCount('sections')
            ->latest()
            ->paginate(config('default_pagination', 25));

        return view('admin-views.page-builder.index', compact('pages', 'search', 'status'));
    }

    public function create()
    {
        $templates = BuilderTemplate::active()->get();
        $sectionTypes = BuilderSection::TYPES;
        $componentTypes = BuilderComponent::TYPES;

        return view('admin-views.page-builder.create', compact('templates', 'sectionTypes', 'componentTypes'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:191',
            'description' => 'nullable|max:500',
            'page_type' => 'nullable|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        try {
            DB::beginTransaction();

            $page = BuilderPage::create([
                'title' => $request->title,
                'slug' => Str::slug($request->title) . '-' . Str::random(6),
                'description' => $request->description,
                'page_type' => $request->page_type ?? 'custom',
                'settings' => $request->settings ?? BuilderPage::defaultSettings(),
                'status' => 1,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => translate('Page created successfully'),
                'page_id' => $page->id,
                'redirect' => route('admin.page-builder.edit', $page->id),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['errors' => [['code' => 'error', 'message' => $e->getMessage()]]], 500);
        }
    }

    public function edit($id)
    {
        $page = BuilderPage::with(['sections.components'])->findOrFail($id);
        $sectionTypes = BuilderSection::TYPES;
        $componentTypes = BuilderComponent::TYPES;
        $actionTypes = BuilderComponent::ACTION_TYPES;

        return view('admin-views.page-builder.edit', compact('page', 'sectionTypes', 'componentTypes', 'actionTypes'));
    }

    public function update(Request $request, $id)
    {
        $page = BuilderPage::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|max:191',
            'description' => 'nullable|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        $page->update([
            'title' => $request->title,
            'description' => $request->description,
            'page_type' => $request->page_type ?? $page->page_type,
            'settings' => $request->settings ?? $page->settings,
        ]);

        return response()->json([
            'success' => true,
            'message' => translate('Page updated successfully'),
        ]);
    }

    public function updateSettings(Request $request, $id)
    {
        $page = BuilderPage::findOrFail($id);
        $page->settings = array_merge($page->settings ?? [], $request->settings ?? []);
        $page->save();

        return response()->json(['success' => true, 'message' => translate('Settings updated')]);
    }

    public function status(Request $request)
    {
        $page = BuilderPage::findOrFail($request->id);
        $page->status = $request->status;
        $page->save();

        Toastr::success(translate('Status updated successfully'));
        return back();
    }

    public function publish(Request $request, $id)
    {
        $page = BuilderPage::findOrFail($id);
        $page->is_published = !$page->is_published;
        $page->published_at = $page->is_published ? now() : null;
        $page->save();

        return response()->json([
            'success' => true,
            'is_published' => $page->is_published,
            'message' => $page->is_published ? translate('Page published') : translate('Page unpublished'),
        ]);
    }

    public function delete($id)
    {
        $page = BuilderPage::findOrFail($id);
        $page->delete();

        Toastr::success(translate('Page deleted successfully'));
        return back();
    }

    public function duplicate($id)
    {
        $original = BuilderPage::with(['sections.components'])->findOrFail($id);

        try {
            DB::beginTransaction();

            $newPage = $original->replicate();
            $newPage->title = $original->title . ' (Copy)';
            $newPage->slug = Str::slug($newPage->title) . '-' . Str::random(6);
            $newPage->is_published = false;
            $newPage->published_at = null;
            $newPage->save();

            foreach ($original->sections as $section) {
                $newSection = $section->replicate();
                $newSection->page_id = $newPage->id;
                $newSection->save();

                foreach ($section->components as $component) {
                    $newComponent = $component->replicate();
                    $newComponent->section_id = $newSection->id;
                    $newComponent->save();
                }
            }

            DB::commit();

            Toastr::success(translate('Page duplicated successfully'));
            return redirect()->route('admin.page-builder.edit', $newPage->id);
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error($e->getMessage());
            return back();
        }
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // SECTION CRUD
    // ─────────────────────────────────────────────────────────────────────────────

    public function addSection(Request $request, $pageId)
    {
        $page = BuilderPage::findOrFail($pageId);

        $maxOrder = $page->sections()->max('order') ?? -1;

        $section = BuilderSection::create([
            'page_id' => $pageId,
            'section_type' => $request->section_type ?? 'text_block',
            'name' => $request->name ?? BuilderSection::TYPES[$request->section_type]['name'] ?? 'Section',
            'order' => $maxOrder + 1,
            'settings' => BuilderSection::defaultSettings($request->section_type ?? 'text_block'),
            'is_visible' => true,
        ]);

        return response()->json([
            'success' => true,
            'section' => $section->toBuilderJson(),
            'message' => translate('Section added'),
        ]);
    }

    public function updateSection(Request $request, $sectionId)
    {
        $section = BuilderSection::findOrFail($sectionId);

        $section->update([
            'name' => $request->name ?? $section->name,
            'settings' => $request->settings ?? $section->settings,
            'style' => $request->style ?? $section->style,
            'is_visible' => $request->has('is_visible') ? $request->is_visible : $section->is_visible,
        ]);

        return response()->json(['success' => true, 'message' => translate('Section updated')]);
    }

    public function deleteSection($sectionId)
    {
        $section = BuilderSection::findOrFail($sectionId);
        $section->delete();

        return response()->json(['success' => true, 'message' => translate('Section deleted')]);
    }

    public function reorderSections(Request $request, $pageId)
    {
        $order = $request->order ?? [];

        foreach ($order as $index => $sectionId) {
            BuilderSection::where('id', $sectionId)->where('page_id', $pageId)->update(['order' => $index]);
        }

        return response()->json(['success' => true, 'message' => translate('Sections reordered')]);
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // COMPONENT CRUD
    // ─────────────────────────────────────────────────────────────────────────────

    public function addComponent(Request $request, $sectionId)
    {
        $section = BuilderSection::findOrFail($sectionId);

        $maxOrder = $section->components()->max('order') ?? -1;
        $type = $request->component_type ?? 'text';

        $component = BuilderComponent::create([
            'section_id' => $sectionId,
            'component_type' => $type,
            'order' => $maxOrder + 1,
            'column_span' => $request->column_span ?? 12,
            'content' => BuilderComponent::defaultContent($type),
            'settings' => BuilderComponent::defaultSettings($type),
            'is_visible' => true,
        ]);

        return response()->json([
            'success' => true,
            'component' => $component->toBuilderJson(),
            'message' => translate('Component added'),
        ]);
    }

    public function updateComponent(Request $request, $componentId)
    {
        $component = BuilderComponent::findOrFail($componentId);

        $component->update([
            'content' => $request->content ?? $component->content,
            'settings' => $request->settings ?? $component->settings,
            'style' => $request->style ?? $component->style,
            'data_source' => $request->data_source ?? $component->data_source,
            'action' => $request->action ?? $component->action,
            'column_span' => $request->column_span ?? $component->column_span,
            'is_visible' => $request->has('is_visible') ? $request->is_visible : $component->is_visible,
        ]);

        return response()->json(['success' => true, 'message' => translate('Component updated')]);
    }

    public function deleteComponent($componentId)
    {
        $component = BuilderComponent::findOrFail($componentId);
        $component->delete();

        return response()->json(['success' => true, 'message' => translate('Component deleted')]);
    }

    public function reorderComponents(Request $request, $sectionId)
    {
        $order = $request->order ?? [];

        foreach ($order as $index => $componentId) {
            BuilderComponent::where('id', $componentId)->where('section_id', $sectionId)->update(['order' => $index]);
        }

        return response()->json(['success' => true, 'message' => translate('Components reordered')]);
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // PREVIEW & RENDER
    // ─────────────────────────────────────────────────────────────────────────────

    public function preview($id)
    {
        $page = BuilderPage::with(['sections.components'])->findOrFail($id);

        return view('admin-views.page-builder.preview', compact('page'));
    }

    public function renderPage($id)
    {
        $page = BuilderPage::with(['sections.components'])->findOrFail($id);

        // Track view (silently fail if table doesn't exist)
        try {
            $page->views()->create([
                'session_id' => session()->getId(),
                'device_type' => request()->header('User-Agent'),
                'referrer' => request()->header('Referer'),
            ]);
        } catch (\Exception $e) {
            // View tracking table may not exist yet
        }

        return view('admin-views.page-builder.render', compact('page'));
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // DATA SEARCH (AJAX)
    // ─────────────────────────────────────────────────────────────────────────────

    public function searchProducts(Request $request)
    {
        $search = $request->get('search', '');
        $restaurantId = $request->get('restaurant_id');
        $categoryId = $request->get('category_id');
        $limit = $request->get('limit', 30);

        $query = Food::withoutGlobalScopes()
            ->with(['storage', 'restaurant:id,name', 'category:id,name'])
            ->where('status', 1)
            ->when($search, function ($q) use ($search) {
                foreach (explode(' ', $search) as $word) {
                    $q->where('name', 'like', "%{$word}%");
                }
            })
            ->when($restaurantId, function ($q) use ($restaurantId) {
                $q->where('restaurant_id', $restaurantId);
            })
            ->when($categoryId, function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });

        $foods = $query->limit($limit)->get(['id', 'name', 'image', 'price', 'discount', 'discount_type', 'restaurant_id', 'category_id', 'avg_rating', 'description']);

        return response()->json($foods->map(function ($f) {
            return [
                'id' => $f->id,
                'name' => $f->name,
                'price' => $f->price,
                'discount' => $f->discount,
                'discount_type' => $f->discount_type,
                'description' => Str::limit($f->description, 60),
                'avg_rating' => $f->avg_rating,
                'image_full_url' => $f->image_full_url,
                'restaurant_id' => $f->restaurant_id,
                'restaurant_name' => $f->restaurant->name ?? '',
                'category_name' => $f->category->name ?? '',
            ];
        }));
    }

    public function searchRestaurants(Request $request)
    {
        $search = $request->get('search', '');
        $limit = $request->get('limit', 30);

        $restaurants = Restaurant::withoutGlobalScopes()
            ->with(['storage'])
            ->where('status', 1)
            ->when($search, function ($q) use ($search) {
                foreach (explode(' ', $search) as $word) {
                    $q->where('name', 'like', "%{$word}%");
                }
            })
            ->limit($limit)
            ->get(['id', 'name', 'logo', 'address', 'rating']);

        return response()->json($restaurants->map(function ($r) {
            return [
                'id' => $r->id,
                'name' => $r->name,
                'address' => $r->address,
                'rating' => $r->rating,
                'logo_full_url' => $r->logo_full_url ?? null,
            ];
        }));
    }

    public function searchCategories(Request $request)
    {
        $search = $request->get('search', '');
        $limit = $request->get('limit', 30);

        $categories = Category::where('status', 1)
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->limit($limit)
            ->get(['id', 'name', 'image']);

        return response()->json($categories->map(function ($c) {
            return [
                'id' => $c->id,
                'name' => $c->name,
                'image_full_url' => $c->image_full_url ?? null,
            ];
        }));
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // EXPORT / IMPORT
    // ─────────────────────────────────────────────────────────────────────────────

    public function exportPage($id)
    {
        $page = BuilderPage::with(['sections.components'])->findOrFail($id);

        return response()->json($page->toBuilderJson())
            ->header('Content-Disposition', 'attachment; filename="page-' . $page->slug . '.json"');
    }

    public function importPage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:json|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        try {
            $content = file_get_contents($request->file('file')->getRealPath());
            $data = json_decode($content, true);

            if (!$data || !isset($data['title'])) {
                throw new \Exception('Invalid page structure');
            }

            $page = BuilderPage::importFromJson($data);

            return response()->json([
                'success' => true,
                'message' => translate('Page imported successfully'),
                'redirect' => route('admin.page-builder.edit', $page->id),
            ]);
        } catch (\Exception $e) {
            return response()->json(['errors' => [['code' => 'error', 'message' => $e->getMessage()]]], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // MEDIA UPLOAD
    // ─────────────────────────────────────────────────────────────────────────────

    public function uploadMedia(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:jpg,jpeg,png,gif,webp,mp4,webm|max:20480',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        try {
            $file = $request->file('file');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/page-builder', $filename);

            $url = dynamicStorage('storage/app/public/page-builder') . '/' . $filename;

            return response()->json([
                'success' => true,
                'url' => $url,
                'filename' => $filename,
            ]);
        } catch (\Exception $e) {
            return response()->json(['errors' => [['code' => 'error', 'message' => $e->getMessage()]]], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // SAVE FULL PAGE STRUCTURE (BULK)
    // ─────────────────────────────────────────────────────────────────────────────

    public function savePageStructure(Request $request, $id)
    {
        $page = BuilderPage::findOrFail($id);

        try {
            DB::beginTransaction();

            // Update page settings
            if ($request->has('settings')) {
                $page->settings = $request->settings;
            }
            if ($request->has('title')) {
                $page->title = $request->title;
            }
            if ($request->has('description')) {
                $page->description = $request->description;
            }
            $page->save();

            // Process sections
            if ($request->has('sections')) {
                $existingSectionIds = [];

                foreach ($request->sections as $order => $sectionData) {
                    if (!empty($sectionData['id']) && is_numeric($sectionData['id'])) {
                        // Update existing section
                        $section = BuilderSection::find($sectionData['id']);
                        if ($section && $section->page_id == $page->id) {
                            $section->update([
                                'section_type' => $sectionData['section_type'] ?? $section->section_type,
                                'name' => $sectionData['name'] ?? $section->name,
                                'order' => $order,
                                'settings' => $sectionData['settings'] ?? $section->settings,
                                'style' => $sectionData['style'] ?? $section->style,
                                'is_visible' => $sectionData['is_visible'] ?? true,
                            ]);
                            $existingSectionIds[] = $section->id;
                        }
                    } else {
                        // Create new section
                        $section = BuilderSection::create([
                            'page_id' => $page->id,
                            'section_type' => $sectionData['section_type'] ?? 'text_block',
                            'name' => $sectionData['name'] ?? 'Section',
                            'order' => $order,
                            'settings' => $sectionData['settings'] ?? BuilderSection::defaultSettings($sectionData['section_type'] ?? 'text_block'),
                            'style' => $sectionData['style'] ?? null,
                            'is_visible' => $sectionData['is_visible'] ?? true,
                        ]);
                        $existingSectionIds[] = $section->id;
                    }

                    // Process components within section
                    if (!empty($sectionData['components'])) {
                        $existingComponentIds = [];

                        foreach ($sectionData['components'] as $compOrder => $compData) {
                            if (!empty($compData['id']) && is_numeric($compData['id'])) {
                                // Update existing component
                                $component = BuilderComponent::find($compData['id']);
                                if ($component && $component->section_id == $section->id) {
                                    $component->update([
                                        'component_type' => $compData['component_type'] ?? $component->component_type,
                                        'order' => $compOrder,
                                        'column_span' => $compData['column_span'] ?? $component->column_span,
                                        'content' => $compData['content'] ?? $component->content,
                                        'settings' => $compData['settings'] ?? $component->settings,
                                        'style' => $compData['style'] ?? $component->style,
                                        'data_source' => $compData['data_source'] ?? $component->data_source,
                                        'action' => $compData['action'] ?? $component->action,
                                        'is_visible' => $compData['is_visible'] ?? true,
                                    ]);
                                    $existingComponentIds[] = $component->id;
                                }
                            } else {
                                // Create new component
                                $type = $compData['component_type'] ?? 'text';
                                $component = BuilderComponent::create([
                                    'section_id' => $section->id,
                                    'component_type' => $type,
                                    'order' => $compOrder,
                                    'column_span' => $compData['column_span'] ?? 12,
                                    'content' => $compData['content'] ?? BuilderComponent::defaultContent($type),
                                    'settings' => $compData['settings'] ?? BuilderComponent::defaultSettings($type),
                                    'style' => $compData['style'] ?? null,
                                    'data_source' => $compData['data_source'] ?? null,
                                    'action' => $compData['action'] ?? null,
                                    'is_visible' => $compData['is_visible'] ?? true,
                                ]);
                                $existingComponentIds[] = $component->id;
                            }
                        }

                        // Delete removed components
                        BuilderComponent::where('section_id', $section->id)
                            ->whereNotIn('id', $existingComponentIds)
                            ->delete();
                    }
                }

                // Delete removed sections
                BuilderSection::where('page_id', $page->id)
                    ->whereNotIn('id', $existingSectionIds)
                    ->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => translate('Page saved successfully'),
                'page' => $page->fresh(['sections.components'])->toBuilderJson(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['errors' => [['code' => 'error', 'message' => $e->getMessage()]]], 500);
        }
    }
}
