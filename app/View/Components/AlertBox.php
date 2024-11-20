<?php

namespace App\View\Components;

use Illuminate\Support\Facades\Session;
use Illuminate\View\Component;

class AlertBox extends Component
{
    public $type;

    public $message;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($if = true, $key = '', $message = '', $type = '')
    {
        if ($if) {
            if (($this->message = $message) || ($this->message = Session::get($key))) {
                $this->type = $type ?: 'info';
            } else {
                $this->type = $this->alertType();
            }
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        if ($this->type) {
            return view('components.alert-box');
        }
    }

    public function alertType()
    {
        if ($this->message = Session::get('info')) {
            return 'info';
        }
        if ($this->message = Session::get('success')) {
            return 'success';
        }
        if ($this->message = Session::get('warning')) {
            return 'warning';
        }
        if ($this->message = Session::get('danger')) {
            return 'danger';
        }
    }
}
