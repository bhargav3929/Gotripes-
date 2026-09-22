#!/bin/sh
# Swap the four round-2 views between the pre-round-2 commit and HEAD.
#   ./state.sh before   -> views as they were on 152c491 (round 1, live 15-17 Sep)
#   ./state.sh after    -> views as committed in e1f3c35 (round 2)
set -e
cd "$(git rev-parse --show-toplevel)"
FILES="resources/views/banner.blade.php resources/views/partials/emirate_selector_modal.blade.php resources/views/partials/gold-medallion-tokens.blade.php resources/views/partials/support-widget.blade.php"
case "$1" in
  before) for f in $FILES; do git show 152c491:"$f" > "$f"; done ;;
  after)  git checkout HEAD -- $FILES ;;
  *) echo "usage: $0 before|after"; exit 1 ;;
esac
php artisan view:clear >/dev/null
echo "views: $1"
