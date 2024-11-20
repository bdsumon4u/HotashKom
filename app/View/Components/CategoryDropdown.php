<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CategoryDropdown extends Component
{
    public $categories;

    public $name;

    public $placeholder;

    public $id;

    public $multiple;

    public $selected;

    public $disabled;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($categories, $name, $placeholder, $id, $multiple = false, $selected = 0, $disabled = 0)
    {
        $this->categories = $categories;
        $this->name = $name;
        $this->placeholder = $placeholder;
        $this->id = $id;
        $this->multiple = $multiple;
        $this->selected = $selected;
        $this->disabled = $disabled;
    }

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
