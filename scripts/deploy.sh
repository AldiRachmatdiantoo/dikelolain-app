#!/usr/bin/env bash
# Puts the checked-out code live. Run it on the server for every release; DEPLOY.md has the first-time steps.
set -euo pipefail
cd "$(dirname "$0")/.."

if [ ! -f .env ]; then
    echo 'File .env belum ada. Salin dari .env.production.example lalu isi (lihat DEPLOY.md).' >&2
    exit 1
fi

composer install --no-dev --optimize-autoloader --no-interaction

# Edit mode needs public/build. A host without Node gets that folder uploaded instead.
if command -v npm >/dev/null; then
    npm ci
    npm run build
elif [ ! -f public/build/manifest.json ]; then
    echo 'public/build belum ada dan server tidak punya npm. Jalankan npm run build di komputer Anda, lalu unggah foldernya.' >&2
    exit 1
fi

grep -q '^APP_KEY=base64:' .env || php artisan key:generate --force

php artisan down --retry=15
trap 'php artisan up' EXIT

php artisan migrate --force
[ -e public/storage ] || php artisan storage:link
php artisan optimize
