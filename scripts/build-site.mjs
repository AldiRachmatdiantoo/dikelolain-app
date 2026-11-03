// Joins and minifies the public site CSS/JS listed in resources/site-assets.json:
// one stylesheet and one script per page instead of 7 + 11 separate requests.
//
// Usage: npm run build:site
//
// Each output starts with a hash of its sources, so SiteAssetsTest can tell when
// a source changed without a rebuild. Outputs stay in public/css and public/js, so
// relative url(../images/...) and url(../webfonts/...) in the CSS keep working.

import { readFileSync, writeFileSync } from 'node:fs';
import { createHash } from 'node:crypto';
import { transform } from 'esbuild';

const root = new URL('../', import.meta.url);
const manifest = JSON.parse(readFileSync(new URL('resources/site-assets.json', root), 'utf8'));

const outputs = { css: 'css/site.min.css', js: 'js/site.min.js' };

for (const [type, output] of Object.entries(outputs)) {
    const sources = manifest[type].map((path) =>
        readFileSync(new URL(`public/${path}`, root), 'utf8').replace(/\r\n/g, '\n'),
    );
    const hash = createHash('sha1').update(sources.join('\n')).digest('hex');

    // A ";" between scripts keeps one file's last statement from running into the next.
    const { code } = await transform(sources.join(type === 'js' ? '\n;\n' : '\n'), {
        loader: type,
        minify: true,
        legalComments: 'eof',
    });

    // @charset only counts as the very first bytes of a stylesheet, so it stays ahead of the banner.
    const charset = code.match(/^@charset "[^"]+";/)?.[0] ?? '';
    const banner = `/* Built by "npm run build:site" from resources/site-assets.json - do not edit. sources:${hash} */\n`;
    const content = charset + banner + code.slice(charset.length);

    writeFileSync(new URL(`public/${output}`, root), content);
    console.log(`${output}: ${(Buffer.byteLength(content) / 1024).toFixed(0)} KB`);
}
