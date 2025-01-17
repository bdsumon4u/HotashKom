<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class CategoryMenu extends Component
{
    /**
     * Create a new component instance.
     *
     * @param  int  $space
     */
    public function __construct(public $categories, public $space = 0)
    {
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View|string
     */
    public function render()
    {
        return view('components.category-menu');
    }
}
