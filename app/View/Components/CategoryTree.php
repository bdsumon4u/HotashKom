<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class CategoryTree extends Component
{
    public $category;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($category)
    {
        $this->categories = $category;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View|string
     */
    public function render()
    {
        return view('components.categories.tree');
    }
}
