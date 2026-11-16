#!/bin/bash
cd "$(dirname "$0")"
echo "=== $(date -Is) ==="
/usr/bin/php dmz_fetchapi.php ; /usr/bin/php dmz_alerts.php
