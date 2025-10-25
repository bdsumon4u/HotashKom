<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Traits\PreventsSourcedResourceDeletion;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class CategoryController extends Controller
{
    use PreventsSourcedResourceDeletion;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(request()->user()->is('salesman'), 403, 'You don\'t have permission.');

        return $this->view([
            'categories' => Category::nested(enabledOnly: false),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): void
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_if(request()->user()->is('salesman'), 403, 'You don\'t have permission.');
        if ($request->has('categories')) {
            $data = $request->validate([
                'categories' => ['required', 'array'],
            ]);

            collect($data['categories'])
                ->each(function ($data): void {
                    $category = Category::find($data['id']);

                    // Prevent circular reference: category cannot be its own parent
                    if (isset($data['parent_id']) && $data['parent_id'] == $category->id) {
                        return; // Skip this update
                    }

                    // Prevent circular reference: category cannot be parent of its descendants
                    if (isset($data['parent_id']) && $data['parent_id'] != 0) {
                        $isDescendant = $this->isDescendant($category, $data['parent_id']);
                        if ($isDescendant) {
                            return; // Skip this update
                        }
                    }

                    $category->update($data);
                });

            cache()->memo()->forget('categories:nested');

            return true;
        }
        $data = $request->validate([
            'parent_id' => ['nullable', 'integer'],
            'name' => ['required', 'unique:categories'],
            'slug' => ['required', 'unique:categories'],
            'base_image' => ['nullable', 'integer'],
            'is_enabled' => ['boolean'],
        ]);

        $data['image_id'] = Arr::pull($data, 'base_image');

        Category::create($data);

        return back()->with('success', 'Category Has Been Created.');
    }

    /**
     * Check if a potential parent is a descendant of the current category
     * This prevents circular references where a category could become its own ancestor
     */
    private function isDescendant(Category $category, int $potentialParentId): bool
    {
        // Check if the potential parent is a descendant of the current category
        $potentialParent = Category::find($potentialParentId);
        if (! $potentialParent) {
            return false;
        }

        // Traverse up the potential parent's ancestry chain
        $current = $potentialParent;
        while ($current->parent_id) {
            if ($current->parent_id == $category->id) {
                return true; // The potential parent is a descendant of the current category
            }
            if (! $current = Category::find($current->parent_id)) {
                break;
            }
        }

        return false;
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        abort_if(request()->user()->is('salesman'), 403, 'You don\'t have permission.');
        $data = $request->validate([
            'parent_id' => ['nullable', 'integer'],
            'name' => 'required|unique:categories,name,'.$category->id,
            'slug' => 'required|unique:categories,slug,'.$category->id,
            'base_image' => ['nullable', 'integer'],
            'is_enabled' => ['boolean'],
        ]);

        // Prevent circular reference: category cannot be its own parent
        if (isset($data['parent_id']) && $data['parent_id'] == $category->id) {
            return back()->withErrors(['parent_id' => 'A category cannot be its own parent.']);
        }

        // Prevent circular reference: category cannot be parent of its descendants
        if (isset($data['parent_id']) && $data['parent_id'] != 0) {
            $isDescendant = $this->isDescendant($category, $data['parent_id']);
            if ($isDescendant) {
                return back()->withErrors(['parent_id' => 'A category cannot be a parent of its descendants.']);
            }
        }

        $data['image_id'] = Arr::pull($data, 'base_image');

        $category->update($data);

        return back()->with('success', 'Category Has Been Updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        abort_unless(request()->user()->is('admin'), 403, 'You don\'t have permission.');

        if (($result = $this->preventSourcedResourceDeletion($category)) !== true) {
            return $result;
        }

        $category->delete();

        return to_route('admin.categories.index')->with('success', 'Category Has Been Deleted.');
    }
}
