<?php

namespace App\Filament\Resources\RealEstate\PropertyResource\Pages;

use App\Filament\Resources\RealEstate\PropertyResource;
use App\Models\RealEstate\Property;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListProperties extends ListRecords
{
    protected static string $resource = PropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->icon('heroicon-o-plus')
            ->label('Ajouter un établissement'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tous')
                ->badge(Property::count()),

            'published' => Tab::make('Publiés')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_published', true))
                ->badge(Property::where('is_published', true)->count()),

            'drafts' => Tab::make('Brouillons')
                ->icon('heroicon-o-pencil')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_published', false))
                ->badge(Property::where('is_published', false)->count()),
        ];
    }
}
