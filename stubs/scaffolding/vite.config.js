import { defineConfig } from 'vite'
import laravel, { refreshPaths } from 'laravel-vite-plugin'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/styles/app.css', 'resources/scripts/app.js'],
            refresh: [
                ...refreshPaths,
                './app/*/*/src/{Clusters,Components,Resources,Pages,Livewire}/**/*.php',
                './app/*/*/resources/{views,lang}/**/*.php',
                './resources/{views,lang}/**/*.blade.php',
                './app/*/*/routes/*.php',
            ],
        }),
    ],
})
