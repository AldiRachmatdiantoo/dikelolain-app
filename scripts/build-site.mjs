// Joins and minifies the public site CSS/JS listed in resources/site-assets.json:
// one stylesheet and one script per page instead of 7 + 11 separate requests.
//
// Usage: npm run build:site (also after editing a view: unused CSS is dropped, see below)
//
// Each output starts with a hash of everything it was built from, so SiteAssetsTest can
// tell when a rebuild is due. Outputs stay in public/css and public/js, so relative
// url(../images/...) and url(../webfonts/...) in the CSS keep working.

import { readdirSync, readFileSync, writeFileSync } from 'node:fs';
import { createHash } from 'node:crypto';
import { transform } from 'esbuild';
import { PurgeCSS } from 'purgecss';

const root = new URL('../', import.meta.url);
const read = (path) => readFileSync(new URL(path, root), 'utf8').replace(/\r\n/g, '\n');
const manifest = JSON.parse(read('resources/site-assets.json'));

const jsSources = manifest.js.map((path) => read(`public/${path}`));
const cssSources = manifest.css.map((path) => read(`public/${path}`));

// The files that can mention a CSS class, in a fixed order so the hash is stable.
const scanned = manifest.scan
    .flatMap((dir) => readdirSync(new URL(`${dir}/`, root), { recursive: true }).map((file) => `${dir}/${file.replaceAll('\\', '/')}`))
    .filter((path) => /\.(php|js)$/.test(path))
    .sort()
    .map(read);

// Plugin classes that are put together at runtime, so no file spells them out.
const runtimeClasses = [/^swiper/, /^slicknav/];

async function dropUnusedCss(css) {
    const [result] = await new PurgeCSS().purge({
        content: [...scanned, ...jsSources].map((raw) => ({ raw, extension: 'html' })),
        css: [{ raw: css }],
        safelist: { standard: runtimeClasses },
        keyframes: true,
    });

    return result.css;
}

async function build(output, loader, code, inputs) {
    const hash = createHash('sha1').update(inputs.join('\n')).digest('hex');
    const { code: minified } = await transform(code, { loader, minify: true, legalComments: 'eof' });

    // @charset only counts as the very first bytes of a stylesheet, so it stays ahead of the banner.
    const charset = minified.match(/^@charset "[^"]+";/)?.[0] ?? '';
    const banner = `/* Built by "npm run build:site" from resources/site-assets.json - do not edit. sources:${hash} */\n`;
    const content = charset + banner + minified.slice(charset.length);

    writeFileSync(new URL(`public/${output}`, root), content);
    console.log(`${output}: ${(Buffer.byteLength(content) / 1024).toFixed(0)} KB`);
}

await build('css/site.min.css', 'css', await dropUnusedCss(cssSources.join('\n')), [...cssSources, ...scanned, ...jsSources]);

// A ";" between scripts keeps one file's last statement from running into the next.
await build('js/site.min.js', 'js', jsSources.join('\n;\n'), jsSources);
