<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class CategoryMenu extends Component
{
    public $categories;

    /**
     * @var int
     */
    public $space;

    /**
     * Create a new component instance.
     *
     * @param  int  $space
     */
    public function __construct($categories, $space = 0)
    {
        $this->categories = $categories;
        $this->space = $space;
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
