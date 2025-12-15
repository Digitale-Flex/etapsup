<?php

namespace App\Filament\InfoLists\RealEstate;

use App\Enums\ReservationType;
use App\Filament\InfoLists\Components\FileEntry;
use App\Filament\InfoLists\Components\TimelineEntry;
use App\Filament\Resources\RealEstate\PropertyResource;
use App\Models\RealEstate\Reservation;
use Filament\Infolists\Components;
use Filament\Infolists\Infolist;

class ReservationInfolist
{
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->columns(5)
            ->schema([
                Components\Section::make('Informations sur le client')
                    ->schema([
                        Components\Grid::make(4)
                            ->schema([
                                Components\TextEntry::make('user.full_name')
                                    ->label('Nom complet')
                                    ->weight('bold'),

                                Components\TextEntry::make('user.email')
                                    ->label('Email')
                                    ->icon('heroicon-o-envelope'),

                                Components\TextEntry::make('user.phone')
                                    ->label('Téléphone')
                                    ->icon('heroicon-o-phone'),

                                Components\TextEntry::make('user.nationality')
                                    ->label('Nationalité')
                            ]),

                        Components\Grid::make(4)
                            ->schema([
                                Components\TextEntry::make('user.place_birth')
                                    ->label('Lieu de naissance'),

                                Components\TextEntry::make('user.date_birth')
                                    ->label('Date de naissance')
                                    ->date('d/m/Y'),

                                Components\TextEntry::make('user.country.name')
                                    ->label('Pays de résidence')
                                    ->color('primary'),

                                Components\TextEntry::make('user.passport_number')
                                    ->label('N° passport'),
                            ]),
                    ]),

                Components\Section::make('Détails de la réservation')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Components\Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('type')
                                    ->label('Type de réservation')
                                    ->badge(),
                                Components\TextEntry::make('guests')
                                    ->label(function ($record) {
                                        return match ($record->type?->value) { // Fix: null-safe
                                            ReservationType::Stay => 'Voyageurs',
                                            ReservationType::Monthly => 'Durée (Mois)',
                                            default => 'Invités'
                                        };
                                    })
                                    ->icon(function ($record) {
                                        return match ($record->type?->value) { // Fix: null-safe
                                            ReservationType::Stay => 'heroicon-o-user-group',
                                            ReservationType::Monthly => 'heroicon-o-calendar',
                                            default => 'heroicon-o-user'
                                        };
                                    }),
                                Components\TextEntry::make('created_at')
                                    ->label('Créée le')
                                    ->dateTime('d/m/Y H:i')
                                    ->color('gray')
                                    ->icon('heroicon-o-calendar'),

                                Components\TextEntry::make('start_date')
                                    ->label('Début de location')
                                    ->date('d M Y')
                                    ->badge()
                                    ->color('primary'),
                                Components\TextEntry::make('end_date')
                                    ->label('Fin de location')
                                    ->date('d M Y')
                                    ->badge()
                                    ->color('primary'),
                                Components\TextEntry::make('duration')
                                    ->label('Durée de location')
                                    ->state(fn(Reservation $record) => $record->start_date && $record->end_date
                                        ? $record->start_date->diffInDays($record->end_date) . ' jours'
                                        : 'N/A'), // Fix: null-safe
                            ]),
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('period')
                                    ->label('Période')
                                    ->formatStateUsing(fn($state) => $state
                                        ? "Du {$state->start()->format('d/m/Y')} au {$state->end()->format('d/m/Y')}"
                                        : 'N/A') // Fix: null-safe
                                    ->icon('heroicon-o-calendar-days'),
                                Components\TextEntry::make('price')
                                    ->label(function ($record) {
                                        return match ($record->type?->value) { // Fix: null-safe
                                            ReservationType::Stay => "Payer",
                                            ReservationType::Monthly => "A Payer",
                                            default => 'Prix total',
                                        };
                                    })
                                    ->money('EUR')
                                    ->color('success')
                                    ->icon('heroicon-o-currency-euro'),
                            ]),

                        Components\Group::make()
                            ->schema([
                                Components\TextEntry::make('reason')
                                    ->label('Motif')
                                    ->icon('heroicon-o-document-magnifying-glass'),
                                Components\KeyValueEntry::make('fees')
                                    ->label('Détail des frais')
                                    ->columnSpanFull()
                                    ->state(fn($record) => match ($record->type?->value) { // Fix: null-safe
                                        ReservationType::Stay => [
                                            'Taxe de séjour' => $record->fees['touristTax'] ?? 'N/A',
                                            'Consommable' => $record->fees['consommable'] ?? 'N/A',
                                            'Frais de service' => $record->fees['serviceFees'] ?? 'N/A',
                                            'Frais de ménage' => $record->fees['cleaningFees'] ?? 'N/A',
                                        ],
                                        ReservationType::Monthly => [
                                            'Cumule des loyers' => $record->fees['total'] ?? 'N/A',
                                            'Caution' => $record->fees['caution'] ?? 'N/A',
                                            '1er mois de loyer' => $record->fees['firstMonthRent'] ?? 'N/A',
                                            'Frais de dossier' => $record->fees['applicationFees'] ?? 'N/A',
                                        ],
                                        default => [],
                                    }),
                            ]),

                    ])
                    ->columnSpan(3),

                Components\Section::make('Détails du logement')
                    ->icon('heroicon-o-home-modern')
                    ->schema([
                        Components\Group::make()
                            ->schema([
                                Components\TextEntry::make('property.title')
                                    ->label('')
                                    ->weight('bold')
                                    ->url(fn(Reservation $record) => PropertyResource::getUrl('edit', ['record' => $record->property])),
                                Components\SpatieMediaLibraryImageEntry::make('property.images')
                                    ->label('')
                                    ->collection('images')
                                    ->square()
                                    ->limit(3)
                                    ->conversion('thumb'),
                            ]),

                        Components\Group::make()
                            ->columns(2)
                            ->schema([
                                Components\TextEntry::make('property.propertyType.label')
                                    ->label('Type de logement'),
                                Components\TextEntry::make('property.category.label')
                                    ->label('Catégorie'),
                                Components\TextEntry::make('property.price')
                                    ->money('EUR')
                                    ->label('Prix'),
                                Components\TextEntry::make('property.city.name')
                                    ->label('Ville'),
                                Components\TextEntry::make('property.address')
                                    ->label('Adresse')->columnSpanFull(),
                            ]),
                    ])->columnSpan(2),

                Components\Section::make('Documents')
                    ->icon('heroicon-o-folder')
                    ->schema([
                        Components\Grid::make(3)
                            ->schema([
                                Components\ImageEntry::make('contract')
                                    ->hidden()
                                    ->label('Contrat')
                                    ->getStateUsing(fn($record) => $record->getFirstMediaUrl('contract'))
                                    ->height(200)
                                    ->extraImgAttributes(['class' => 'rounded-lg shadow']),

                                /*  FileEntry::make('files')
                                      ->label('Pièces jointes')
                                      ->label('Pièces jointes')
                                      ->getStateUsing(fn ($record) => $record->getMedia('files')) */
                                Components\TextEntry::make('files')
                                    ->label('Pièces jointes')
                                    ->getStateUsing(fn($record) => $record->getMedia('files')->map(fn($file) => "<a href='{$file->getUrl()}' target='_blank' class='text-primary-600 hover:underline'>{$file->file_name}</a>"
                                    )->implode('<br>'))
                                    ->html(),
                            ]),
                    ]),


                Components\Section::make('Contrat & Communication')
                    ->hidden()
                    ->schema([
                        Components\SpatieMediaLibraryImageEntry::make('contract')
                            ->collection('contract')
                            ->label('Contrat PDF'),

                        Components\Grid::make(2)
                            ->schema([
                                Components\IconEntry::make('pdf_generated')
                                    ->state(fn(Reservation $record) => $record->hasFlag('pdf_generated'))
                                    ->boolean()
                                    ->label('PDF Généré'),
                                Components\IconEntry::make('email_sent')
                                    ->state(fn(Reservation $record) => $record->hasFlag('email_sent'))
                                    ->boolean()
                                    ->label('Email Envoyé'),
                            ]),

                        Components\Fieldset::make('Statut')
                            ->schema([
                                Components\TextEntry::make('pdf_status')
                                    ->state(fn(Reservation $record): string => match (true) {
                                        $record->hasFlag('pdf_generated') => 'Succès',
                                        $record->hasFlag('pdf_generation_failed') => 'Échec Génération',
                                        default => 'Non généré'
                                    })
                                    ->color(fn($state): string => match ($state) {
                                        'Succès' => 'success',
                                        'Échec Génération' => 'danger',
                                        default => 'gray'
                                    })
                                    ->badge(),

                                Components\TextEntry::make('email_status')
                                    ->state(fn(Reservation $record): string => match (true) {
                                        $record->hasFlag('email_sent') => 'Succès',
                                        $record->hasFlag('email_failed') => 'Échec Envoi',
                                        default => 'Non envoyé'
                                    })
                                    ->color(fn($state): string => match ($state) {
                                        'Succès' => 'success',
                                        'Échec Envoi' => 'danger',
                                        default => 'gray'
                                    })
                                    ->badge(),
                            ]),
                    ]),
            ]);
    }
}
