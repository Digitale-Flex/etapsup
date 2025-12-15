<?php

namespace App\Filament\InfoLists\RealEstate;

use App\Enums\ApplicationStatus;
use App\Filament\InfoLists\Components\FileEntry;
use App\Filament\InfoLists\Components\TimelineEntry;
use App\Filament\Resources\RealEstate\PropertyResource;
use App\Models\RealEstate\Reservation;
use Filament\Infolists\Components;
use Filament\Infolists\Infolist;

/**
 * Infolist pour afficher les détails d'une candidature EtapSup
 * Adapté du vocabulaire Mareza vers EtapSup
 */
class ReservationInfolist
{
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->columns(5)
            ->schema([
                // Section Étudiant
                Components\Section::make('Informations sur l\'étudiant')
                    ->icon('heroicon-o-user')
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
                                    ->label('N° passeport'),
                            ]),
                    ]),

                // Section Candidature
                Components\Section::make('Détails de la candidature')
                    ->icon('heroicon-o-academic-cap')
                    ->schema([
                        Components\Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('status')
                                    ->label('Statut de la candidature')
                                    ->badge()
                                    ->color(fn ($state) => ApplicationStatus::getStatusColor($state ?? 'pending'))
                                    ->formatStateUsing(fn ($state) => ApplicationStatus::translateStatus($state ?? 'pending')),

                                Components\TextEntry::make('created_at')
                                    ->label('Date de candidature')
                                    ->dateTime('d/m/Y H:i')
                                    ->color('gray')
                                    ->icon('heroicon-o-calendar'),

                                Components\TextEntry::make('updated_at')
                                    ->label('Dernière mise à jour')
                                    ->dateTime('d/m/Y H:i')
                                    ->color('gray'),
                            ]),

                        Components\Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('start_date')
                                    ->label('Rentrée souhaitée')
                                    ->date('d M Y')
                                    ->badge()
                                    ->color('primary'),

                                Components\TextEntry::make('end_date')
                                    ->label('Fin prévue')
                                    ->date('d M Y')
                                    ->badge()
                                    ->color('primary'),

                                Components\TextEntry::make('duration')
                                    ->label('Durée du programme')
                                    ->state(fn(Reservation $record) => $record->start_date && $record->end_date
                                        ? $record->start_date->diffInMonths($record->end_date) . ' mois'
                                        : 'N/A'),
                            ]),

                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('price')
                                    ->label('Frais de scolarité estimés')
                                    ->money('EUR')
                                    ->color('success')
                                    ->icon('heroicon-o-currency-euro'),

                                // Feature 9: Accompagnement Premium
                                Components\TextEntry::make('accompagnement_status')
                                    ->label('Accompagnement Premium')
                                    ->state(fn(Reservation $record) => match(true) {
                                        ($record->accompagnement_premium ?? false) && ($record->accompagnement_paid ?? false) => 'Souscrit et payé',
                                        ($record->accompagnement_premium ?? false) => 'En attente de paiement',
                                        default => 'Non demandé'
                                    })
                                    ->badge()
                                    ->color(fn($state) => match($state) {
                                        'Souscrit et payé' => 'success',
                                        'En attente de paiement' => 'warning',
                                        default => 'gray'
                                    }),
                            ]),

                        Components\Group::make()
                            ->schema([
                                Components\TextEntry::make('reason')
                                    ->label('Motif (si refus)')
                                    ->icon('heroicon-o-document-magnifying-glass')
                                    ->visible(fn($record) => !empty($record->reason)),

                                Components\TextEntry::make('notes')
                                    ->label('Notes administratives')
                                    ->icon('heroicon-o-clipboard-document-list')
                                    ->visible(fn($record) => !empty($record->notes)),

                                Components\KeyValueEntry::make('fees')
                                    ->label('Détail des frais')
                                    ->columnSpanFull()
                                    ->state(fn($record) => [
                                        'Frais de dossier' => $record->fees['applicationFees'] ?? 'N/A',
                                        'Frais de scolarité' => $record->fees['tuitionFees'] ?? 'N/A',
                                        'Accompagnement premium' => $record->fees['accompagnement'] ?? 'N/A',
                                    ])
                                    ->visible(fn($record) => !empty($record->fees)),
                            ]),

                    ])
                    ->columnSpan(3),

                // Section Établissement
                Components\Section::make('Établissement visé')
                    ->icon('heroicon-o-building-library')
                    ->schema([
                        Components\Group::make()
                            ->schema([
                                Components\TextEntry::make('property.title')
                                    ->label('')
                                    ->weight('bold')
                                    ->url(fn(Reservation $record) => $record->property
                                        ? PropertyResource::getUrl('edit', ['record' => $record->property])
                                        : null),
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
                                    ->label('Type d\'établissement'),
                                Components\TextEntry::make('property.category.label')
                                    ->label('Domaine d\'études'),
                                Components\TextEntry::make('property.price')
                                    ->money('EUR')
                                    ->label('Frais annuels'),
                                Components\TextEntry::make('property.city.name')
                                    ->label('Ville'),
                                Components\TextEntry::make('property.city.country.name')
                                    ->label('Pays')
                                    ->badge()
                                    ->color('info'),
                                Components\TextEntry::make('property.address')
                                    ->label('Adresse')->columnSpanFull(),
                            ]),
                    ])->columnSpan(2),

                // Section Documents
                Components\Section::make('Documents de candidature')
                    ->icon('heroicon-o-folder')
                    ->schema([
                        Components\Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('files')
                                    ->label('Pièces jointes (CV, diplômes, etc.)')
                                    ->getStateUsing(fn($record) => $record->getMedia('files')->map(fn($file) => "<a href='{$file->getUrl()}' target='_blank' class='text-primary-600 hover:underline'>{$file->file_name}</a>"
                                    )->implode('<br>') ?: '<span class="text-gray-400">Aucun document</span>')
                                    ->html(),
                            ]),
                    ]),

                // Section Communication (masquée)
                Components\Section::make('Suivi & Communication')
                    ->hidden()
                    ->schema([
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

                        Components\Fieldset::make('Statut des communications')
                            ->schema([
                                Components\TextEntry::make('pdf_status')
                                    ->state(fn(Reservation $record): string => match (true) {
                                        $record->hasFlag('pdf_generated') => 'Succès',
                                        $record->hasFlag('pdf_generation_failed') => 'Échec',
                                        default => 'Non généré'
                                    })
                                    ->color(fn($state): string => match ($state) {
                                        'Succès' => 'success',
                                        'Échec' => 'danger',
                                        default => 'gray'
                                    })
                                    ->badge(),

                                Components\TextEntry::make('email_status')
                                    ->state(fn(Reservation $record): string => match (true) {
                                        $record->hasFlag('email_sent') => 'Succès',
                                        $record->hasFlag('email_failed') => 'Échec',
                                        default => 'Non envoyé'
                                    })
                                    ->color(fn($state): string => match ($state) {
                                        'Succès' => 'success',
                                        'Échec' => 'danger',
                                        default => 'gray'
                                    })
                                    ->badge(),
                            ]),
                    ]),
            ]);
    }
}
