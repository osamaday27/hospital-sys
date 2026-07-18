<?php
namespace App\View\Components;

use Illuminate\View\Component;

class Tooth extends Component
{
    public $tooth;
    public $selectedTooth;
    public $toothData;

    public function __construct($tooth, $selectedTooth = [], $toothData = [])
    {
        $this->tooth = $tooth;
        $this->selectedTooth = $selectedTooth;
        $this->toothData = $toothData;
    }

    public function render()
    {
        return view('components.tooth');
    }
}