#!/bin/bash

# Database Export Script for Rivage Skincare
# This script exports the database for deployment purposes

# Configuration
DB_HOST="localhost"
DB_NAME="rivage_skincare"
DB_USER="rivage_user"
DB_PASS="your_password_here"
BACKUP_DIR="./backups"
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_FILE="$BACKUP_DIR/rivage_skincare_$DATE.sql"

# Create backup directory
mkdir -p "$BACKUP_DIR"

echo "Starting database export..."

# Export database
mysqldump -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" \
    --single-transaction \
    --routines \
    --triggers \
    --add-drop-database \
    --create-options \
    --disable-keys \
    --extended-insert \
    --quick \
    --lock-tables=false \
    "$DB_NAME" > "$BACKUP_FILE"

# Compress the backup
gzip "$BACKUP_FILE"

echo "Database exported to: $BACKUP_FILE.gz"
echo "Backup size: $(du -h "$BACKUP_FILE.gz" | cut -f1)"

# Optional: Upload to cloud storage
# aws s3 cp "$BACKUP_FILE.gz" s3://your-backup-bucket/
# gcloud storage cp "$BACKUP_FILE.gz" gs://your-backup-bucket/
