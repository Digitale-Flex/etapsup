import preset from './vendor/filament/support/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/**/*.blade.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
        './resources/js/**/*.vue',
        './resources/js/**/*.js',
        './resources/js/**/*.ts',
    ],
    corePlugins: {
        preflight: false, // Désactive la réinitialisation des styles de base
        container: false, // Désactive le container Tailwind pour utiliser celui de Bootstrap
    },
    important: true,
}
