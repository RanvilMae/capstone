<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class KpiCard extends Component
{
    public $label;
    public $title;
    public $value;
    public $icon;

    public function __construct($label, $title, $value, $icon = '')
    {
        $this->label = $label;
        $this->title = $title;
        $this->value = $value;
        $this->icon = $icon;
    }

    public function render()
    {
        return view('components.kpi-card');
    }
}
