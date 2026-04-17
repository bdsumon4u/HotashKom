<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryMenu;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CategoryMenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|Response|View
     */
    public function index()
    {
        abort_if(request()->user()->is('salesman'), 403, 'You don\'t have permission.');
        $remaining_cats = Category::with('parent')->whereDoesntHave('categoryMenu')->get();
        $selected_cats = CategoryMenu::nestedWithParent();

        return view('admin.categories.menu', compact('remaining_cats', 'selected_cats'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        abort_if(request()->user()->is('salesman'), 403, 'You don\'t have permission.');
        if ($request->has('category')) {
            $data = $request->validate([
                'category' => ['required', 'array'],
            ]);

            collect($data['category'])
                ->each(function ($val, $key): void {
                    CategoryMenu::updateOrInsert(['category_id' => $key], []);
                })->toArray();
        }

        if ($request->has('categories')) {
            $data = $request->validate([
                'categories' => ['required', 'array'],
            ]);

            collect($data['categories'])
                ->each(function ($val): void {
                    $menu = CategoryMenu::find($val['id']);

                    // Prevent circular reference: menu cannot be its own parent
                    if (isset($val['parent_id']) && $val['parent_id'] == $menu->id) {
                        return; // Skip this update
                    }

                    // Prevent circular reference: menu cannot be parent of its descendants
                    if (isset($val['parent_id']) && $val['parent_id'] != 0) {
                        $isDescendant = $this->isDescendant($menu, $val['parent_id']);
                        if ($isDescendant) {
                            return; // Skip this update
                        }
                    }

                    CategoryMenu::updateOrInsert(['id' => $val['id']], $val);
                })->toArray();

            cacheMemo()->forget('catmenu:nested');
            cacheMemo()->forget('catmenu:nestedwithparent');

            return true;
        }

        cacheMemo()->forget('catmenu:nested');
        cacheMemo()->forget('catmenu:nestedwithparent');

        return back();
    }

    /**
     * Check if a potential parent is a descendant of the current menu item.
     * This prevents circular references where a menu item could become its own ancestor.
     */
    private function isDescendant(CategoryMenu $menu, int $potentialParentId): bool
    {
        $potentialParent = CategoryMenu::find($potentialParentId);
        if (! $potentialParent) {
            return false;
        }

        // Traverse up the potential parent's ancestry chain
        $current = $potentialParent;
        while ($current->parent_id) {
            if ($current->parent_id == $menu->id) {
                return true; // The potential parent is a descendant of the current menu
            }
            if (! $current = CategoryMenu::find($current->parent_id)) {
                break;
            }
        }

        return false;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(CategoryMenu $categoryMenu)
    {
        abort_unless(request()->user()->is('admin'), 403, 'You don\'t have permission.');

        return DB::transaction(function () use ($categoryMenu): void {
            $categoryMenu->childrens()->delete();
            $categoryMenu->delete();
        });
    }
}
