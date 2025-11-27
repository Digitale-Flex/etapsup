<?php

namespace App\Filament\Resources\RealEstate\PropertyResource\Pages;

use App\Filament\Resources\RealEstate\PropertyResource;
use App\Models\RealEstate\Category;
use App\Models\RealEstate\Property;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ListProperties extends ListRecords
{
    protected static string $resource = PropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->icon('heroicon-o-plus')
            ->label('Ajouter une propriété'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tous')
                ->badge(Property::count()),

            ...Category::query()
                ->orderBy('label')
                ->withCount('properties')
                ->get()
                ->map(fn(Category $category) => Tab::make($category->label)
                    //->icon('heroicon-o-tag')
                    ->modifyQueryUsing(
                        fn(Builder $query) => $query->where('category_id', $category->id)
                    )
                    ->badge($category->properties_count)
                )
        ];
    }
}
