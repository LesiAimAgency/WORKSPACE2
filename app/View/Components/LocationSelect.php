<?php

namespace App\View\Components;

use Illuminate\View\Component;

class LocationSelect extends Component
{
    public $name;

    public $value;

    public function __construct($name = 'location', $value = null)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function render()
    {
        return view('components.location-select');
    }
}
