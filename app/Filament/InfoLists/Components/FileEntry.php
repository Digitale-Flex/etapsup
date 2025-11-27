<?php

namespace App\Filament\InfoLists\Components;

use Filament\Infolists\Components\Entry;
use Illuminate\Support\Collection;

class FileEntry extends Entry
{
    protected string $view = 'filament.infolists.components.file-entry';

    protected Collection $files;

    public function files(Collection $files): static
    {
        $this->files = $files;
        return $this;
    }

    public function getFiles(): Collection
    {
        return $this->files;
    }
}
