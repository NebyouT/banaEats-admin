<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\CustomPage;
use App\Models\Food;
use App\Models\Restaurant;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class CustomPageController extends Controller
{
    public function index()
    {
        $pages = CustomPage::latest()->paginate(config('default_pagination'));
        return view('admin-views.custom-page.index', compact('pages'));
    }

    public function create()
    {
        return view('admin-views.custom-page.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'            => 'required|max:191',
            'background_color' => 'nullable|max:20',
            'background_image' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $page = new CustomPage();
        $page->title            = $request->title;
        $page->slug             = Str::slug($request->title) . '-' . Str::random(6);
        $page->subtitle         = $request->subtitle;
        $page->promotional_text = $request->promotional_text;
        $page->background_color = $request->background_color ?? '#ffffff';
        $page->product_ids      = $request->product_ids ? array_map('intval', (array) $request->product_ids) : [];
        $page->restaurant_ids   = $request->restaurant_ids ? array_map('intval', (array) $request->restaurant_ids) : [];
        $page->status           = 1;

        if ($request->hasFile('background_image')) {
            $page->background_image = Helpers::upload(dir: 'custom-page/', format: 'png', image: $request->file('background_image'));
        }

        $page->save();

        Toastr::success(translate('messages.custom_page_created_successfully'));
        return response()->json([], 200);
    }

    public function edit(CustomPage $custom_page)
    {
        $selectedProducts = Food::withoutGlobalScopes()
            ->with(['storage', 'restaurant:id,name'])
            ->whereIn('id', $custom_page->product_ids ?? [])
            ->get(['id', 'name', 'image', 'price', 'restaurant_id']);

        $selectedRestaurants = Restaurant::withoutGlobalScopes()
            ->with(['storage'])
            ->whereIn('id', $custom_page->restaurant_ids ?? [])
            ->get(['id', 'name', 'logo', 'address']);

        $preloadedProductsJson = $selectedProducts->map(function ($p) {
            return [
                'id'              => $p->id,
                'name'            => $p->name,
                'price'           => $p->price,
                'image_full_url'  => $p->image_full_url,
                'restaurant_name' => optional($p->restaurant)->name ?? '',
            ];
        })->values()->toArray();

        $preloadedRestaurantsJson = $selectedRestaurants->map(function ($r) {
            return [
                'id'            => $r->id,
                'name'          => $r->name,
                'address'       => $r->address,
                'logo_full_url' => $r->logo_full_url,
            ];
        })->values()->toArray();

        return view('admin-views.custom-page.edit', compact(
            'custom_page', 'selectedProducts', 'selectedRestaurants',
            'preloadedProductsJson', 'preloadedRestaurantsJson'
        ));
    }

    public function update(Request $request, CustomPage $custom_page)
    {
        $validator = Validator::make($request->all(), [
            'title'            => 'required|max:191',
            'background_color' => 'nullable|max:20',
            'background_image' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $custom_page->title            = $request->title;
        $custom_page->subtitle         = $request->subtitle;
        $custom_page->promotional_text = $request->promotional_text;
        $custom_page->background_color = $request->background_color ?? '#ffffff';
        $custom_page->product_ids      = $request->product_ids ? array_map('intval', (array) $request->product_ids) : [];
        $custom_page->restaurant_ids   = $request->restaurant_ids ? array_map('intval', (array) $request->restaurant_ids) : [];

        if ($request->hasFile('background_image')) {
            $custom_page->background_image = Helpers::update(
                dir: 'custom-page/',
                old_image: $custom_page->background_image,
                format: 'png',
                image: $request->file('background_image')
            );
        }

        $custom_page->save();

        Toastr::success(translate('messages.custom_page_updated_successfully'));
        return response()->json([], 200);
    }

    public function status(Request $request)
    {
        $page = CustomPage::findOrFail($request->id);
        $page->status = $request->status;
        $page->save();
        Toastr::success(translate('messages.status_updated_successfully'));
        return back();
    }

    public function delete(CustomPage $custom_page)
    {
        if ($custom_page->background_image) {
            Helpers::check_and_delete('custom-page/', $custom_page->background_image);
        }
        $custom_page->delete();
        Toastr::success(translate('messages.custom_page_deleted_successfully'));
        return back();
    }

    // AJAX: search products by name, optionally filtered by restaurant
    public function search_products(Request $request)
    {
        $key           = $request->get('search', '');
        $restaurant_id = $request->get('restaurant_id');

        $query = Food::withoutGlobalScopes()
            ->with(['storage', 'restaurant:id,name'])
            ->where('status', 1)
            ->where(function ($q) use ($key) {
                foreach (explode(' ', $key) as $word) {
                    $q->orWhere('name', 'like', "%{$word}%");
                }
            });

        if ($restaurant_id) {
            $query->where('restaurant_id', $restaurant_id);
        }

        $foods = $query->limit(30)->get(['id', 'name', 'image', 'price', 'restaurant_id']);

        return response()->json($foods->map(function ($f) {
            return [
                'id'              => $f->id,
                'name'            => $f->name,
                'price'           => $f->price,
                'image_full_url'  => $f->image_full_url,
                'restaurant_name' => $f->restaurant->name ?? '',
            ];
        }));
    }

    // AJAX: search restaurants by name
    public function search_restaurants(Request $request)
    {
        $key = $request->get('search', '');

        $restaurants = Restaurant::withoutGlobalScopes()
            ->with(['storage'])
            ->where('status', 1)
            ->where(function ($q) use ($key) {
                foreach (explode(' ', $key) as $word) {
                    $q->orWhere('name', 'like', "%{$word}%");
                }
            })
            ->limit(30)
            ->get(['id', 'name', 'logo', 'address']);

        return response()->json($restaurants->map(function ($r) {
            return [
                'id'            => $r->id,
                'name'          => $r->name,
                'address'       => $r->address,
                'logo_full_url' => $r->logo_full_url ?? null,
            ];
        }));
    }
}
