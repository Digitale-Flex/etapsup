<?php

namespace App\Filament\Pages;

// Sprint1 Update: Feature 1.2.1 — Livret explicatif (PDF administrable)

use App\Settings\GeneralSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Filament\Notifications\Notification;

/**
 * Page Filament pour gérer les paramètres généraux
 *
 * Feature 1.2.1 : Upload du livret explicatif PDF
 * Accessible via : /gate/general
 */
class ManageGeneral extends SettingsPage
{
    protected static ?string $title = 'Paramètres généraux';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $settings = GeneralSettings::class;

    protected static ?string $navigationGroup = 'Paramètres';

    protected static ?int $navigationSort = 10;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Livret explicatif du parcours étudiant')
                    ->description('Téléversez le PDF du livret d\'arrivée qui sera consultable par tous les étudiants depuis leur tableau de bord.')
                    ->schema([
                        Forms\Components\FileUpload::make('livret_path')
                            ->label('📘 Livret d\'arrivée (PDF)')
                            ->disk('public')
                            ->directory('livrets')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(10240) // 10 Mo max
                            ->downloadable()
                            ->openable()
                            ->previewable(false)
                            ->helperText('Formats acceptés : PDF uniquement. Taille maximale : 10 Mo.')
                            ->hint('Ce fichier sera accessible par tous les étudiants')
                            ->hintIcon('heroicon-m-information-circle')
                            ->deleteUploadedFileUsing(function ($file) {
                                // Supprimer l'ancien fichier lors du remplacement
                                if (\Storage::disk('public')->exists($file)) {
                                    \Storage::disk('public')->delete($file);
                                }
                            })
                            ->afterStateUpdated(function () {
                                Notification::make()
                                    ->title('Livret mis à jour')
                                    ->success()
                                    ->body('Le nouveau livret sera disponible pour tous les étudiants.')
                                    ->send();
                            }),
                    ]),
            ]);
    }

    /**
     * Restreindre l'accès aux admins et devs
     */
    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'dev']);
    }

    /**
     * Afficher dans la navigation uniquement pour admins et devs
     */
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['admin', 'dev']);
    }

    /**
     * Vérification supplémentaire au mount
     */
    public function mount(): void
    {
        abort_unless(auth()->user()->hasAnyRole(['admin', 'dev']), 403);

        parent::mount();
    }
}
