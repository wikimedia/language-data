import { resolve } from 'path';
import { defineConfig } from 'vite';

export default defineConfig({
    build: {
        minify: false,
        lib: {
            entry: resolve(__dirname, 'src/index.js'),
            name: 'languageData',
            // The filename for the bundled output, extensions will be added automatically.
            fileName: 'language-data'
        }
    }
});