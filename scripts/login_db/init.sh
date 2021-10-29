#!/bin/bash
DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" >/dev/null 2>&1 && pwd)"
cd "$DIR/../../"
php artisan migrate && \
php artisan tinker < "$DIR/create_user.php"