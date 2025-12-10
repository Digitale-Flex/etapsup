<?php

namespace App\Filament\Resources\Certificate;

use App\Filament\Resources\Certificate;
use App\Models\Certificate\CertificateRequest;
use App\Models\Certificate\Partner;
use App\Services\CertificateGenerationService;
use App\States\CertificateRequest\CertificateGenerated;
use App\States\CertificateRequest\PaymentInvalid;
use App\States\CertificateRequest\PaymentPending;
use App\States\CertificateRequest\PaymentValidated;
use App\States\CertificateRequest\PaymentVerification;
use Filament\Forms;
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

class CertificateRequestResource extends Resource
{
    protected static ?string $model = CertificateRequest::class;

    protected static ?string $modelLabel = "Demandes d'attestations";

    protected static ?string $navigationIcon = 'gmdi-document-scanner-o';

    protected static ?string $activeNavigationIcon = 'heroicon-o-check-circle';

    protected static ?string $navigationGroup = 'Attestations';

    protected static ?int $navigationSort = 1;

    /**
     * Masquer la ressource du menu de navigation
     * Quick Win 10/12/2025 : Désactiver temporairement les demandes d'attestations
     *
     * @return bool
     */
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Utilisateur')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\Select::make('city_id')
                    ->label('Ville souhaitée')
                    ->relationship('city', 'name')
                    ->required(),
                Forms\Components\Select::make('rental_deposit_id')
                    ->label('Cautionnement locatif :')
                    ->relationship('rentalDeposit', 'name')
                    ->required(),
                Forms\Components\TextInput::make('passport_number')
                    ->label('Numéro de passeport')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('requesting_country')
                    ->label('Pays de demande')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('budget')
                    ->label('Budget')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('rental_start')
                    ->label('Date de début de location')
                    ->required(),
                Forms\Components\TextInput::make('duration')
                    ->label('Durée minimale de location')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('further_information')
                    ->label('Informations complémentaires')
                    ->maxLength(255),
                Forms\Components\SpatieMediaLibraryFileUpload::make('certificate')
                    ->label('Attestation')
                    ->collection('certificate')
                    ->openable(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->formatStateUsing(function ($state, $record) {
                        return date('y').'-'.str_pad($state, 3, '0', STR_PAD_LEFT);
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->description(fn (CertificateRequest $record): string => $record->user->surname)
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
                    ->getStateUsing(fn ($record) => $record->location?->locatable?->name ?? $record->city?->name)
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
                    ->toggleable(),
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
                    ->formatStateUsing(fn (CertificateRequest $record): string => $record->state->label())
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
                Tables\Filters\SelectFilter::make('gestion')
                    ->label('Type de gestion')
                    ->options(function () {
                        $baseQuery = CertificateRequest::withoutGlobalScopes([SoftDeletingScope::class]);
                        $total = $baseQuery->count();

                        // Compter uniquement les partenaires publiés
                        $partnerManaged = $baseQuery->clone()
                            ->whereHas('partner', fn($q) => $q->isPublished())
                            ->count();
                        $selfManaged = $total - $partnerManaged;

                        return [
                            'partner' => "Gérées par un partenaire ($partnerManaged)",
                            'self' => "Auto-gérées ($selfManaged)",
                        ];
                    })
                    //->default('all')
                    ->query(function (Builder $query, array $data) {
                        return match ($data['value'] ?? 'all') {
                            'partner' => $query->whereNotNull('partner_id'),
                            'self' => $query->whereNull('partner_id'),
                            default => $query,
                        };
                    })
                    ->indicateUsing(function (array $data): ?string {
                        return match ($data['value'] ?? 'all') {
                            'partner' => 'Gérées par un partenaire',
                            'self' => 'Auto-gérées',
                            'all' => null,
                        };
                    }),

                Tables\Filters\SelectFilter::make('partner_id')
                    ->label('Filtrer par partenaire')
                    ->options(function () {
                        return Partner::withoutGlobalScopes([SoftDeletingScope::class])
                            ->orderBy('label')
                            ->pluck('label', 'id');
                    })
                    ->query(function (Builder $query, array $data) {
                        $value = $data['value'] ?? null;
                        return $value
                            ? $query->where('partner_id', $value)
                            : $query;
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['value']) return null;
                        return 'Partenaire : ' . Partner::withoutGlobalScopes([SoftDeletingScope::class])
                                ->find($data['value'])?->label;
                    }),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
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
        return $infolist
            ->schema([
                Section::make('Gestion du statut de paiement')
                    ->description("Chaque changement de statut envoi un email à l'utilisateur. Veuillez vous assurer de la précision du nouveau statut avant de procéder au changement.")
                    ->aside()
                    ->schema([
                        Actions::make([
                            Actions\Action::make('to_verification')
                                ->label('Mettre en vérification')
                                ->icon('heroicon-o-magnifying-glass')
                                ->color('info')
                                ->size('sm')
                                ->requiresConfirmation()
                                ->modalHeading('Confirmer le changement d\'état')
                                ->modalDescription('Êtes-vous sûr de vouloir mettre cette demande en vérification ?')
                                ->modalSubmitActionLabel('Confirmer')
                                ->modalCancelActionLabel('Annuler')
                                ->visible(fn (CertificateRequest $record) => $record->state instanceof PaymentPending ||
                                    $record->state instanceof PaymentInvalid
                                )
                                ->action(fn (CertificateRequest $record) => static::changeState($record, PaymentVerification::class)),
                            Actions\Action::make('to_validated')
                                ->label('Valider le paiement')
                                ->icon('heroicon-o-check-circle')
                                ->color('success')
                                ->size('sm')
                                ->requiresConfirmation()
                                ->modalHeading('Confirmer la validation du paiement')
                                ->modalDescription('Êtes-vous sûr de vouloir valider le paiement de cette demande ?')
                                ->modalSubmitActionLabel('Confirmer')
                                ->modalCancelActionLabel('Annuler')
                                ->visible(fn (CertificateRequest $record) => $record->state instanceof PaymentVerification ||
                                    $record->state instanceof PaymentInvalid
                                )
                                ->action(fn (CertificateRequest $record) => static::changeState($record, PaymentValidated::class)),
                            Actions\Action::make('to_invalid')
                                ->label('Invalider le paiement')
                                ->icon('heroicon-o-x-circle')
                                ->color('danger')
                                ->size('sm')
                                ->requiresConfirmation()
                                ->modalHeading('Confirmer l\'invalidation du paiement')
                                ->modalDescription('Êtes-vous sûr de vouloir invalider le paiement de cette demande ?')
                                ->modalSubmitActionLabel('Confirmer')
                                ->modalCancelActionLabel('Annuler')
                                ->visible(fn (CertificateRequest $record) => $record->state instanceof PaymentPending ||
                                    $record->state instanceof PaymentVerification
                                )
                                ->action(fn (CertificateRequest $record) => static::changeState($record, PaymentInvalid::class)),
                        ])->alignment(Alignment::End),
                    ])
                    ->columnSpan('full')
                    ->visible(function (CertificateRequest $record) {
                        if ($record->state instanceof CertificateGenerated) {
                            return false;
                        }

                        return ($record->state instanceof PaymentPending) ||
                            ($record->state instanceof PaymentVerification) ||
                            ($record->state instanceof PaymentInvalid);
                    }),

                Section::make()
                    ->description('.')
                    ->aside()
                    ->schema([
                        Actions::make([
                            Actions\Action::make('generate_pdf')
                                ->label("Générer les documents")
                                ->size('sm')
                                ->icon('heroicon-o-document-arrow-down')
                                ->requiresConfirmation()
                                ->modalHeading("Confirmation de génération de l'attestation et du contrat de bail")
                                ->modalDescription("Êtes-vous sûr de vouloir générer ces documents ? Un email sera automatiquement envoyé au demandeur avec les documents en pièce jointe.")
                                ->modalSubmitActionLabel('Générer et Envoyer')
                                ->modalCancelActionLabel('Annuler')
                                ->action(function (CertificateRequest $record, CertificateGenerationService $service) {
                                    $pdfPath = $service->generate($record, true);

                                    if ($pdfPath) {
                                        static::changeState($record, CertificateGenerated::class);

                                        Notification::make()
                                            ->title('Documents générés et envoyés')
                                            ->body('Le certificat et le contrat de bail ont été créés, un courriel a été envoyé au demandeur.')
                                            ->success()
                                            ->send();
                                    }
                                })
                                ->openUrlInNewTab()
                                ->color('success')
                                ->visible(fn (CertificateRequest $record) => ! $record->hasMedia('certificate')),
                            Actions\Action::make('view_certificate')
                                ->size('sm')
                                ->label("Ouvrir l'attestation")
                                ->icon('heroicon-o-eye')
                                ->url(fn ($record) => $record->getFirstMediaUrl('certificate'))
                                ->openUrlInNewTab()
                                ->color('primary')
                                ->visible(fn (CertificateRequest $record) => $record->hasMedia('certificate')),
                            Actions\Action::make('view_contract')
                                ->size('sm')
                                ->label("Ouvrir le contrat")
                                ->icon('heroicon-o-eye')
                                ->url(fn ($record) => $record->getFirstMediaUrl('contract'))
                                ->openUrlInNewTab()
                                ->color('primary')
                                ->visible(fn (CertificateRequest $record) => $record->hasMedia('contract')),
                        ])->alignment(Alignment::Between),
                    ])->visible(function (CertificateRequest $record): bool {
                        return $record->state instanceof PaymentValidated || $record->state instanceof CertificateGenerated;
                    })->extraAttributes([
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
                                ->color(fn ($record) => $record->partner_id ? 'success' : 'warning')
                                ->icon(fn ($record) => $record->partner_id
                                    ? 'heroicon-o-building-office'
                                    : 'heroicon-o-user-circle')
                                ->weight(FontWeight::SemiBold)
                                ->url(function ($record) {
                                    return $record->partner_id
                                        ? PartnerResource::getUrl('view', [$record->partner->hashid])
                                        : null;
                                }),
                            TextEntry::make('coupon.code')
                                ->label('Coupon utilisé')
                                ->formatStateUsing(function ($state, CertificateRequest $record) {
                                    return $record->coupon_id
                                        ? "Code promo : {$state}"
                                        : 'Aucun code promo utilisé';
                                })
                                ->color(fn ($record) => $record->coupon_id ? 'success' : 'warning')
                                ->icon(fn ($record) => $record->coupon_id
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
                                ->formatStateUsing(fn (CertificateRequest $record): string => $record->state->label())
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
                                ->visible(fn (CertificateRequest $record) => $record->pay_later),
                            TextEntry::make('partner.label')->label('Partenaire')
                                ->visible(fn (CertificateRequest $record) => $record->partner),
                            TextEntry::make('country.name')->label('Pays de résidence'),
                            TextEntry::make('location_or_city_name')->label('Ville souhaité')->getStateUsing(fn ($record) => $record->location?->locatable?->name ?? $record->city?->name),
                            TextEntry::make('genre.name')->label('Type de logement'),
                            TextEntry::make('rentalDeposit.name')
                                ->label('Cautionnement locatif')
                                ->visible(fn ($record) => $record->rental_deposit_id)
                                ->color('warning'),

                            TextEntry::make('rental_deposit_names')
                                ->label('Cautionnement locatif')
                                ->color('warning')
                                ->state(function (CertificateRequest $record) {
                                    if ($record->rentalDeposits->isNotEmpty()) {
                                        return $record->rentalDeposits->pluck('name')->join(', ');
                                    }
                                    return $record->rentalDeposit->name ?? 'Non spécifié';
                                })
                                ->visible(fn ($record) => $record->rentalDeposits->isNotEmpty() || $record->rental_deposit_id),
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
            'index' => Certificate\CertificateRequestResource\Pages\ListCertificateRequests::route('/'),
            //'create' => Pages\CreateRentalCertificateRequest::route('/create'),
            'view' => Certificate\CertificateRequestResource\Pages\ViewCertificateRequest::route('/{record}'),
            //'edit' => Pages\EditRentalCertificateRequest::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function getNavigationLabel(): string
    {
        return "Demandes d'attestations";
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
                ->body('Une erreur est survenue : '.$e->getMessage())
                ->danger()
                ->send();
        }
    }
}
