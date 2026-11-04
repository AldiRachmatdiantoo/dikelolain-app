// Joins and minifies the public site CSS/JS listed in resources/site-assets.json:
// one stylesheet and one script per page instead of 7 + 11 separate requests.
//
// Usage: npm run build:site (also after editing a view: unused CSS and icons are dropped, see below)
//
// Each output starts with a hash of everything it was built from, so SiteAssetsTest can
// tell when a rebuild is due. Outputs stay in public/css and public/js, so relative
// url(../images/...) and url(../webfonts/...) in the CSS keep working.

import { mkdirSync, readdirSync, readFileSync, writeFileSync } from 'node:fs';
import { createHash } from 'node:crypto';
import { transform } from 'esbuild';
import { PurgeCSS } from 'purgecss';
import subsetFont from 'subset-font';

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

// The Font Awesome files css/all.min.css loads. Admin pages keep using them in full.
const iconFonts = ['fa-brands-400', 'fa-regular-400', 'fa-solid-900', 'fa-v4compatibility'];

async function dropUnusedCss(css) {
    const [result] = await new PurgeCSS().purge({
        content: [...scanned, ...jsSources].map((raw) => ({ raw, extension: 'html' })),
        css: [{ raw: css }],
        safelist: { standard: runtimeClasses },
        keyframes: true,
    });

    return result.css;
}

const sha1 = (content) => createHash('sha1').update(content).digest('hex');

// Every character the stylesheet can show: icons are set through content: "..." or --fa: "...".
function shownCharacters(css) {
    const text = [...css.matchAll(/(?:content|--fa)\s*:\s*(["'])(.*?)\1/g)]
        .map(([, , value]) => value)
        .join('')
        .replace(/\\([0-9a-f]{1,6})\s?/gi, (_, hex) => String.fromCodePoint(parseInt(hex, 16)))
        .replace(/\\(.)/g, '$1');

    return [...new Set(text)].join('');
}

// Keeps only the icons left in the CSS. The ?v= makes browsers fetch a font again when its icons change.
async function trimIconFonts(css) {
    const characters = shownCharacters(css);
    mkdirSync(new URL('public/webfonts/site/', root), { recursive: true });

    for (const font of iconFonts) {
        const full = readFileSync(new URL(`public/webfonts/${font}.woff2`, root));
        const trimmed = await subsetFont(full, characters, { targetFormat: 'woff2' });

        writeFileSync(new URL(`public/webfonts/site/${font}.woff2`, root), trimmed);
        css = css.replaceAll(
            `url(../webfonts/${font}.woff2) format("woff2"),url(../webfonts/${font}.ttf) format("truetype")`,
            `url(../webfonts/site/${font}.woff2?v=${sha1(trimmed).slice(0, 8)}) format("woff2")`,
        );
    }

    return css;
}

async function build(output, loader, code, inputs) {
    const hash = sha1(inputs.join('\n'));
    const { code: minified } = await transform(code, { loader, minify: true, legalComments: 'eof' });

    // @charset only counts as the very first bytes of a stylesheet, so it stays ahead of the banner.
    const charset = minified.match(/^@charset "[^"]+";/)?.[0] ?? '';
    const banner = `/* Built by "npm run build:site" from resources/site-assets.json - do not edit. sources:${hash} */\n`;
    const content = charset + banner + minified.slice(charset.length);

    writeFileSync(new URL(`public/${output}`, root), content);
    console.log(`${output}: ${(Buffer.byteLength(content) / 1024).toFixed(0)} KB`);
}

const siteCss = await trimIconFonts(await dropUnusedCss(cssSources.join('\n')));
await build('css/site.min.css', 'css', siteCss, [...cssSources, ...scanned, ...jsSources]);

// A ";" between scripts keeps one file's last statement from running into the next.
await build('js/site.min.js', 'js', jsSources.join('\n;\n'), jsSources);
