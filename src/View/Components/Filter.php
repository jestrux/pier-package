<?php

namespace Jestrux\Pier\View\Components;

use Illuminate\View\Component;

class Filter extends Component
{
    public $activeClass;
    public $selected;
    public $field;
    public $value;
    public $comparison;

    public function __construct($field, $value = "", $comparison = "", $activeClass = 'selected')
    {
        $this->field = "where".$field;
        $this->field .= $comparison == null ? "" : 'is'.$comparison ;
        $this->value = $value;
        $this->comparison = $comparison;
        $this->activeClass = $activeClass;

        $valueComparison = "filters.$this->field == '$value'";
        if(strlen($value) == 0)
            $valueComparison = "!filters.$this->field";
            
        $this->selected = "{'$activeClass': $valueComparison}";
    }

    public function render()
    {
        return view('pier::components.filter');
    }
}
