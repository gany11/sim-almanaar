import { defineConfig } from 'vite'

export default defineConfig({
    build: {
        outDir: 'public/build',
        emptyOutDir: true,
        manifest: true,
        rollupOptions: {
            input: {
                app: 'public/js/app.js',
                landing: 'public/js/landing.js'
            }
        }
    }
})