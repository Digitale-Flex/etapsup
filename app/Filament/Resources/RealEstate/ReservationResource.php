<?php

namespace App\Filament\Resources\RealEstate;

use App\Filament\InfoLists\RealEstate\ReservationInfolist;
use App\Filament\Resources\RealEstate\ReservationResource\Pages;
use App\Filament\Resources\RealEstate\ReservationResource\RelationManagers;
use App\Models\RealEstate\Reservation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;

    protected static ?string $modelLabel = 'Réservation';

    protected static ?string $pluralLabel = 'Réservations';

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('property.images')
                    ->collection('images')
                    ->square()
                    ->stacked()
                    ->limit(1)
                    ->label(''),
                Tables\Columns\TextColumn::make('property.title')
                    ->label('Propriété')
                    ->limit(25)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();

                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        return $state;
                    })
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.full_name')
                    ->label('Utilisateur')
                    ->description(fn(Reservation $record): string => $record->user->email)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('period')
                    ->label('Période')
                    ->formatStateUsing(fn($record) => $record->start_date->translatedFormat('d M') . ' - ' . $record->end_date->translatedFormat('d M Y'))
                    ->description(fn($record) => $record->start_date->diffInDays($record->end_date) . ' jours')
                    ->sortable()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->label('Type'),

                Tables\Columns\TextColumn::make('price')
                    ->label('Montant')
                    ->money('EUR')
                    ->color('success')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('state')
                    ->options([
                        'pending' => 'En attente',
                        'confirmed' => 'Confirmée',
                        'cancelled' => 'Annulée',
                        'completed' => 'Terminée',
                    ]),

                Tables\Filters\Filter::make('dates')
                    ->form([
                        Forms\Components\DatePicker::make('start_from')->label('De'),
                        Forms\Components\DatePicker::make('start_until')->label("jusqu'à"),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['start_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('start_date', '>=', $date),
                            )
                            ->when(
                                $data['start_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('end_date', '<=', $date),
                            );
                    }),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return ReservationInfolist::infolist($infolist);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReservations::route('/'),
            'create' => Pages\CreateReservation::route('/create'),
            'view' => Pages\ViewReservation::route('/{record}'),
            'edit' => Pages\EditReservation::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
