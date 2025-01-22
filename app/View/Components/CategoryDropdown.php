<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CategoryDropdown extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(public $categories, public $name, public $placeholder, public $id, public $multiple = false, public $selected = 0, public $disabled = 0) {}

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.categories.dropdown');
    }
}
