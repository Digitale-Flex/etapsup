<?php

namespace App\Filament\Resources\Certificate\PartnerResource\Pages;

use App\Filament\Resources\Certificate\PartnerResource;
use App\Models\Scopes\PublishedScope;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ManagePartnerCoupons extends ManageRelatedRecords
{
    protected static string $resource = PartnerResource::class;

    protected static string $relationship = 'coupons';

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    public static function getNavigationLabel(): string
    {
        return 'Coupons';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Toggle::make('is_published')
                    ->label('Publier')
                    ->default(false)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('discount_amount')
                    ->label('Prix')
                    ->required()
                    ->numeric()
                    ->prefix('€')
                    ->step(0.01)
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('code')
            ->columns([
                Tables\Columns\TextColumn::make('code'),
                Tables\Columns\TextColumn::make('discount_amount')
                    ->money('EUR')
                    ->label('Montant')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Publier')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->icon('heroicon-o-plus')
                    ->label('Créer un coupon')
                    ->modalIcon('heroicon-o-plus')
                    ->modalWidth('lg'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->modalWidth('lg')
                ->modalIcon('heroicon-o-pencil'),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn(Builder $query) => $query->withoutGlobalScopes([
                PublishedScope::class,
                SoftDeletingScope::class,
            ])->forPartner());
    }

}
