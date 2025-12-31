<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ManaSymbols extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public ?string $cost = null
    ) {}

    public function symbols(): array
    {
        if (!$this->cost) {
            return [];
        }

        preg_match_all('/\{([^}]+)\}/', $this->cost, $matches);

        return $matches[1] ?? [];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.mana-symbols');
    }
}
