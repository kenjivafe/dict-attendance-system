<?php

namespace App\View\Components;

use Illuminate\View\Component;

class TimeEntryCard extends Component
{
    public function __construct(
        public string $timeField,
        public string $timeValue,
        public string $title
    ) {}

    public function render()
    {
        return view('components.time-entry-card');
    }
}
