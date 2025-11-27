<?php

namespace App\Filament\InfoLists\Components;

use Filament\Infolists\Components\Entry;
use Illuminate\Support\Collection;

class TimelineEntry extends Entry
{
    protected string $view = 'filament.infolists.components.timeline-entry';

    protected Collection $items;

    public function items(Collection $items): static
    {
        $this->items = $items;
        return $this;
    }

    public function getItems(): Collection
    {
        return $this->items;
    }
}
