<?php

namespace App\Filament\Partner\Resources;

use App\Filament\Partner\Resources\CertificateResource\Pages;
use App\Filament\Resources\Certificate\PartnerResource;
use App\Models\Certificate;
use App\Models\Certificate\CertificateRequest;
use App\Services\CertificateGenerationService;
use App\States\CertificateRequest\CertificateGenerated;
use App\States\CertificateRequest\PaymentInvalid;
use App\States\CertificateRequest\PaymentPending;
use App\States\CertificateRequest\PaymentValidated;
use App\States\CertificateRequest\PaymentVerification;
use Filament\Forms\Form;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CertificateResource extends Resource
{
    protected static ?string $model = Certificate\CertificateRequest::class;

    protected static ?string $modelLabel = "Demande d'attestation";
    protected static ?string $pluralModelLabel = "Demandes d'attestations";
    protected static ?string $navigationIcon = 'gmdi-document-scanner-o';
    protected static ?string $navigationGroup = 'Attestations';
    protected static ?int $navigationSort = 1;

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
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->formatStateUsing(function ($state, $record) {
                        return date('y') . '-' . str_pad($state, 3, '0', STR_PAD_LEFT);
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->description(fn(CertificateRequest $record): string => $record->user?->surname ?? '') // Fix: null-safe
                    ->label('Demandeur')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nationality')
                    ->label('Nationalité')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('user.phone')
                    ->label('Téléphone')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('budget')
                    ->label('Budget')
                    ->suffix(' €')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('location_or_city')
                    ->label('Ville')
                    ->getStateUsing(fn($record) => $record->location?->locatable?->name ?? $record->city?->name)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('genre.name')
                    ->label('Type')
                    ->alignCenter()
                    ->limit(10)
                    ->numeric()
                    ->sortable()
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();

                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        return $state;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('rentalDeposit.name')
                    ->label('Caution...')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('passport_number')
                    ->label('N° du passport')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('country.name')
                    ->label('Depuis')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('rental_start')
                    ->label('Début')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('duration')
                    ->label('Durée')
                    ->suffix(' mois')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('state')
                    ->label('Paiement')
                    ->alignCenter()
                    ->badge()
                    ->formatStateUsing(fn(CertificateRequest $record): string => $record->state->label())
                    ->color(function (CertificateRequest $record): string {
                        return match ($record->state::class) {
                            PaymentPending::class => 'warning',
                            PaymentVerification::class => 'info',
                            PaymentValidated::class => 'primary',
                            PaymentInvalid::class => 'danger',
                            CertificateGenerated::class => 'success',
                            default => 'secondary',
                        };
                    })
                    ->icon(function (CertificateRequest $record): string {
                        return match ($record->state::class) {
                            PaymentPending::class => 'heroicon-o-clock',
                            PaymentVerification::class => 'heroicon-o-magnifying-glass',
                            PaymentValidated::class => 'heroicon-o-check-circle',
                            PaymentInvalid::class => 'heroicon-o-x-circle',
                            CertificateGenerated::class => 'heroicon-o-document-check',
                            default => 'heroicon-o-question-mark-circle',
                        };
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                 Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([

            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->description('.')
                    ->aside()
                    ->schema([
                        Actions::make([
                            Actions\Action::make('view_certificate')
                                ->size('sm')
                                ->label("Ouvrir l'attestation")
                                ->icon('heroicon-o-envelope')
                                ->icon('heroicon-o-eye')
                                ->url(fn($record) => $record->file)
                                ->openUrlInNewTab()
                                ->color('primary')
                        ])->alignment(Alignment::Between),
                    ])->visible(fn(CertificateRequest $record) => $record->hasMedia('certificate'))
                    ->extraAttributes([
                        'class' => '!p-2 sm:p-4', // Ajout de classes pour réduire le padding
                    ]),

                Section::make('Informations sur le demandeur')
                    ->description("Détails complets sur la personne ayant fait la demande d'attestation de logement")
                    ->aside()
                    ->schema([
                        Group::make([
                            TextEntry::make('user.surname')->label('Nom'),
                            TextEntry::make('user.name')->label('Prénom'),
                            TextEntry::make('user.email')->label('Adresse mail'),
                            TextEntry::make('user.phone')->label('Téléphone'),
                            TextEntry::make('nationality')->label('Nationalité'),
                            TextEntry::make('passport_number')->label('Numéro de passport'),
                            TextEntry::make('user.date_birth')->label('Date de naissance')->date(),
                            TextEntry::make('user.country.name')->label('Pays'),
                            TextEntry::make('user.place_birth')->label('Lieu'),
                        ])->columns(3),
                    ])
                    ->columns(1),

                Section::make('Informations sur la demande')
                    ->description('Détails complets de la demande d\'attestation de logement')
                    ->aside()
                    ->schema([
                        Group::make([
                            TextEntry::make('partner.label')
                                ->label('Gestion du dossier')
                                ->formatStateUsing(function ($state, CertificateRequest $record) {
                                    return $record->partner_id
                                        ? "Géré par : " . $state
                                        : 'Auto-géré par le demandeur';
                                })
                                ->color(fn($record) => $record->partner_id ? 'success' : 'warning')
                                ->icon(fn($record) => $record->partner_id
                                    ? 'heroicon-o-building-office'
                                    : 'heroicon-o-user-circle')
                                ->weight(FontWeight::SemiBold),
                            TextEntry::make('coupon.code')
                                ->label('Coupon utilisé')
                                ->formatStateUsing(function ($state, CertificateRequest $record) {
                                    return $record->coupon_id
                                        ? "Code promo : {$state}"
                                        : 'Aucun code promo utilisé';
                                })
                                ->color(fn($record) => $record->coupon_id ? 'success' : 'warning')
                                ->icon(fn($record) => $record->coupon_id
                                    ? 'heroicon-o-ticket'
                                    : 'heroicon-o-user-circle')
                                ->weight(FontWeight::SemiBold),

                            TextEntry::make('paid')
                                ->label('Montant a payer')
                                ->color('success')
                                ->formatStateUsing(function ($state, CertificateRequest $record) {
                                    $formattedAmount = number_format($state, 2, ',', ' ');

                                    if ($record->coupon_id) {
                                        $originalAmount = number_format(399, 2, ',', ' ');
                                        return "{$formattedAmount} € / {$originalAmount} €";
                                    }

                                    return "{$formattedAmount} €";
                                })
                                ->weight(FontWeight::SemiBold),
                            TextEntry::make('state')
                                ->label('Statut de Paiement')
                                ->badge()
                                ->formatStateUsing(fn(CertificateRequest $record): string => $record->state->label())
                                ->color(function (CertificateRequest $record): string {
                                    return match ($record->state::class) {
                                        PaymentPending::class => 'warning',
                                        PaymentVerification::class => 'info',
                                        PaymentValidated::class => 'primary',
                                        PaymentInvalid::class => 'danger',
                                        CertificateGenerated::class => 'success',
                                        default => 'secondary',
                                    };
                                }),
                            IconEntry::make('pay_later')->label('Paiement différé')
                                ->visible(fn(CertificateRequest $record) => $record->pay_later),
                            TextEntry::make('partner.label')->label('Partenaire')
                                ->visible(fn(CertificateRequest $record) => $record->partner),
                            TextEntry::make('country.name')->label('Pays de résidence'),
                            TextEntry::make('location_or_city_name')->label('Ville souhaité')->getStateUsing(fn($record) => $record->location?->locatable?->name ?? $record->city?->name),
                            TextEntry::make('genre.name')->label('Type de logement'),
                            TextEntry::make('rentalDeposit.name')
                                ->label('Cautionnement locatif')
                                ->visible(fn($record) => $record->rental_deposit_id)
                                ->color('warning'),

                            TextEntry::make('rental_deposit_names')
                                ->label('Cautionnement locatif')
                                ->color('warning')
                                ->state(function (CertificateRequest $record) {
                                    if ($record->rentalDeposits->isNotEmpty()) {
                                        return $record->rentalDeposits->pluck('name')->join(', ');
                                    }
                                    return $record->rentalDeposit?->name ?? 'Non spécifié'; // Fix: null-safe
                                })
                                ->visible(fn($record) => $record->rentalDeposits->isNotEmpty() || $record->rental_deposit_id),
                            TextEntry::make('budget')->label('Budget')->money('EUR'),
                            TextEntry::make('rental_start')->label('Début de la location')->date(),
                            TextEntry::make('duration')->label('Durée minimale')->suffix(' mois'),
                            TextEntry::make('address')->label('Adresse'),
                            TextEntry::make('further_information')->label('Information complémentaires')->columnSpanFull(),
                        ])->columns(3),
                    ]),
                Section::make('Preuves de paiement')
                    ->description('Liste des justificatifs de paiement soumis pour cette demande')
                    ->aside()
                    ->collapsible()
                    ->schema([
                        \Filament\Infolists\Components\RepeatableEntry::make('paymentProofs')
                            ->label('')
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Date de soumission')
                                    ->dateTime(),
                                TextEntry::make('note')
                                    ->label('Note complémentaire')
                                    ->limit(60)
                                    ->tooltip(function (TextEntry $component): ?string {
                                        $state = $component->getState();

                                        if (strlen($state) <= $component->getCharacterLimit()) {
                                            return null;
                                        }

                                        return $state;
                                    }),
                                ViewEntry::make('file')
                                    ->label('Fichier')
                                    ->view('filament.infolists.components.payment-proof-file'),
                            ])
                            ->columns(3),
                    ])
                    ->visible(function (CertificateRequest $record): bool {
                        return $record->paymentProofs->isNotEmpty();
                    }),
            ]);
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
            'index' => Pages\ListCertificates::route('/'),
            //'create' => Pages\CreateCertificate::route('/create'),
            'view' => Pages\ViewCertificate::route('/{record}'),
            // 'edit' => Pages\EditCertificate::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->when(
                auth()->user()->hasRole('partner'),
                fn($query) => $query->where('partner_id', auth()->user()->partner_id)
            );
    }

    public static function getNavigationLabel(): string
    {
        return "Demandes d'attestations";
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    private static function changeState(CertificateRequest $record, string $newState): void
    {
        try {
            $record->state->transitionTo($newState);
            $record->save();
            Notification::make()
                ->title('État mis à jour avec succès')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Erreur lors du changement d\'état')
                ->body('Une erreur est survenue : ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}
