#!/bin/bash
cd "$(dirname "$0")"
echo "=== $(date -Is) ==="
php dmz_fetchapi.php && php dmz_alerts.php
