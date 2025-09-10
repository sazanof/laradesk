import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import vuetify from 'vite-plugin-vuetify'

export default defineConfig({
    resolve: {
        alias: {
            '@': '/resources'
        }
    },
    plugins: [
        laravel({
            input: [ 'resources/js/app.js', 'resources/js/error.js' ],
            refresh: true
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false
                }
            }
        }),
        vuetify({ autoImport: true })
    ]

})
