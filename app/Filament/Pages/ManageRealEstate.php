<?php

namespace App\Filament\Pages;

use App\Models\RealEstate\Category;
use App\Models\RealEstate\SubCategory;
use App\Settings\RealEstateSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageRealEstate extends SettingsPage
{
    protected static ?string $title = 'Paramètres réservation';

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = RealEstateSettings::class;

    protected static ?string $navigationGroup = 'Paramètres';
    protected static ?int $navigationSort = 100;

   // protected static bool $shouldRegisterNavigation = false;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make('2')->schema([
                    Forms\Components\Grid::make()->schema([
                        Forms\Components\Section::make('Paramètres généraux')
                            ->description("Ces paramètres généraux définissent les caractéristiques essentielles et communes à tous les biens immobiliers de l'application")
                            ->columns(2)
                            ->schema([
                                Forms\Components\TextInput::make('vat')
                                    ->label('TVA')
                                    ->numeric()
                                    ->inputMode('decimal')
                                    ->suffix('%')
                                    ->required(),
                                Forms\Components\TextInput::make('tourist_tax')
                                    ->label('Taxe de séjour')
                                    ->numeric()
                                    ->inputMode('decimal')
                                    ->suffix('%')
                                    ->required(),
                                Forms\Components\TextInput::make('consumable')
                                    ->label('Frais de consommables')
                                    ->suffix('%')
                                    ->step(0.01)
                                    ->numeric()
                                    ->required(),
                                Forms\Components\TextInput::make('service_fees')
                                    ->label('Frais de service')
                                    ->suffix('%')
                                    ->step(0.01)
                                    ->numeric()
                                    ->required(),
                                Forms\Components\Select::make('rental_monthly_billing')
                                    ->label('Facture mensuelle')
                                    ->options(Category::all()->pluck('label', 'id'))
                                    ->hint('Quelle catégorie sera une facturation mensuelle ?')
                                    ->required()
                                    ->searchable()
                                ->columnSpanFull(),
                                Forms\Components\Select::make('category_supporting_documents')
                                    ->label('Exigence des justificatifs')
                                    ->options(Category::all()->pluck('label', 'id'))
                                    ->hint('Quelle catégorie exige des justificatifs ?')
                                    ->required()
                                    ->searchable()
                                ->columnSpanFull(),
                                Forms\Components\TextInput::make('application_fees')
                                    ->label('Frais de dossier')
                                    ->suffix('€')
                                    ->step(0.01)
                                    ->numeric()
                                    ->required(),
                            ]),
                    ])->columnSpan(1),

                /*    Forms\Components\Section::make()->description(
                        'Les paramètres généraux permettent de configurer les différents éléments financiers et administratifs appliqués aux biens immobiliers. Vous pouvez y définir les taux de TVA applicables, le montant de la taxe de séjour, les frais de consommables (eau, électricité, etc.), ainsi que tout autre frais ou taxe standard à appliquer à vos biens. Ces réglages servent de base pour une gestion harmonisée des aspects financiers de vos propriétés et facilitent la facturation auprès des occupants'
                    )->columnSpan(1), */
                ]),

            ]);
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'dev']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['admin', 'dev']);
    }

    public function mount(): void
    {
        abort_unless(auth()->user()->hasAnyRole(['admin', 'dev']), 403);

        parent::mount();
    }
}
