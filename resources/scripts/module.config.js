import path from 'path';
import {fileURLToPath} from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

export default {
    dependencies: [],
    devDependencies: [],
    vitePlugins: [],
    viteAliases: {},
    entryPoints: [
        // path.resolve(__dirname, '../../dist/index.css'),
        path.resolve(__dirname, '../styles/index.css'),
    ],
};
