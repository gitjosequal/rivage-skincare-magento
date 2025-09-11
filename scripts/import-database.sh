#!/bin/bash

# Database Import Script for Rivage Skincare
# This script imports the database for deployment purposes

# Configuration
DB_HOST="localhost"
DB_NAME="rivage_skincare"
DB_USER="rivage_user"
DB_PASS="your_password_here"
BACKUP_FILE="./backups/rivage_skincare_latest.sql.gz"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if backup file exists
if [ ! -f "$BACKUP_FILE" ]; then
    log_error "Backup file $BACKUP_FILE not found!"
    log_info "Please run export-database.sh first or provide the correct backup file path"
    exit 1
fi

log_info "Starting database import..."

# Create database if it doesn't exist
log_info "Creating database if it doesn't exist..."
mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Import database
log_info "Importing database from $BACKUP_FILE..."
if [[ "$BACKUP_FILE" == *.gz ]]; then
    gunzip -c "$BACKUP_FILE" | mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME"
else
    mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < "$BACKUP_FILE"
fi

if [ $? -eq 0 ]; then
    log_success "Database imported successfully!"
    
    # Show database info
    log_info "Database information:"
    mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" -e "USE $DB_NAME; SHOW TABLES;" | wc -l | xargs echo "Tables:"
    mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" -e "SELECT COUNT(*) as 'Total Products' FROM catalog_product_entity;" "$DB_NAME"
    mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" -e "SELECT COUNT(*) as 'Total Categories' FROM catalog_category_entity;" "$DB_NAME"
    
else
    log_error "Database import failed!"
    exit 1
fi
