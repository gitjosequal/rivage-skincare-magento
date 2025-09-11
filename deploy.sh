#!/bin/bash

# Rivage Skincare Magento 2.4.8 Deployment Script
# This script automates the deployment process for production servers

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
MAGENTO_ROOT="/var/www/rivage"
PHP_BIN="php"
COMPOSER_BIN="composer"
MAGENTO_BIN="$MAGENTO_ROOT/bin/magento"

# Functions
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

# Check if running as root
check_user() {
    if [[ $EUID -eq 0 ]]; then
        log_error "This script should not be run as root for security reasons"
        exit 1
    fi
}

# Check if Magento directory exists
check_magento_dir() {
    if [ ! -d "$MAGENTO_ROOT" ]; then
        log_error "Magento directory $MAGENTO_ROOT does not exist"
        exit 1
    fi
}

# Check PHP version
check_php_version() {
    PHP_VERSION=$($PHP_BIN -r "echo PHP_VERSION;")
    log_info "PHP Version: $PHP_VERSION"
    
    if [[ $PHP_VERSION < "8.3" ]]; then
        log_error "PHP 8.3+ is required. Current version: $PHP_VERSION"
        exit 1
    fi
}

# Check if required services are running
check_services() {
    log_info "Checking required services..."
    
    # Check MySQL
    if ! systemctl is-active --quiet mysql; then
        log_error "MySQL is not running"
        exit 1
    fi
    
    # Check Elasticsearch
    if ! systemctl is-active --quiet elasticsearch; then
        log_error "Elasticsearch is not running"
        exit 1
    fi
    
    # Check Redis (optional)
    if systemctl is-active --quiet redis-server; then
        log_success "Redis is running"
    else
        log_warning "Redis is not running (optional)"
    fi
    
    log_success "All required services are running"
}

# Backup current installation
backup_current() {
    log_info "Creating backup..."
    BACKUP_DIR="/var/backups/rivage-$(date +%Y%m%d-%H%M%S)"
    mkdir -p "$BACKUP_DIR"
    
    # Backup database
    log_info "Backing up database..."
    mysqldump -u rivage_user -p rivage_skincare > "$BACKUP_DIR/database.sql"
    
    # Backup media files
    log_info "Backing up media files..."
    cp -r "$MAGENTO_ROOT/pub/media" "$BACKUP_DIR/"
    
    log_success "Backup created at $BACKUP_DIR"
}

# Pull latest changes from Git
pull_changes() {
    log_info "Pulling latest changes from Git..."
    cd "$MAGENTO_ROOT"
    git pull origin main
    log_success "Git pull completed"
}

# Install Composer dependencies
install_dependencies() {
    log_info "Installing Composer dependencies..."
    cd "$MAGENTO_ROOT"
    $COMPOSER_BIN install --no-dev --optimize-autoloader --no-interaction
    log_success "Dependencies installed"
}

# Set proper permissions
set_permissions() {
    log_info "Setting proper permissions..."
    cd "$MAGENTO_ROOT"
    
    # Set ownership
    sudo chown -R www-data:www-data .
    
    # Set file permissions
    sudo find . -type f -exec chmod 644 {} \;
    sudo find . -type d -exec chmod 755 {} \;
    sudo chmod +x bin/magento
    
    # Set specific permissions for sensitive directories
    sudo chmod -R 775 var/
    sudo chmod -R 775 pub/static/
    sudo chmod -R 775 pub/media/
    sudo chmod -R 775 generated/
    
    log_success "Permissions set"
}

# Run Magento setup commands
run_magento_setup() {
    log_info "Running Magento setup commands..."
    cd "$MAGENTO_ROOT"
    
    # Set deployment mode
    log_info "Setting deployment mode to production..."
    $PHP_BIN $MAGENTO_BIN deploy:mode:set production --no-interaction
    
    # Run setup upgrade
    log_info "Running setup upgrade..."
    $PHP_BIN $MAGENTO_BIN setup:upgrade --no-interaction
    
    # Compile DI
    log_info "Compiling dependency injection..."
    $PHP_BIN $MAGENTO_BIN setup:di:compile
    
    # Deploy static content
    log_info "Deploying static content..."
    $PHP_BIN $MAGENTO_BIN setup:static-content:deploy -f --no-interaction
    
    # Reindex
    log_info "Reindexing..."
    $PHP_BIN $MAGENTO_BIN indexer:reindex
    
    # Flush cache
    log_info "Flushing cache..."
    $PHP_BIN $MAGENTO_BIN cache:flush
    
    log_success "Magento setup completed"
}

# Optimize performance
optimize_performance() {
    log_info "Optimizing performance..."
    cd "$MAGENTO_ROOT"
    
    # Enable Redis cache if available
    if systemctl is-active --quiet redis-server; then
        log_info "Configuring Redis cache..."
        $PHP_BIN $MAGENTO_BIN setup:config:set --cache-backend=redis --cache-backend-redis-server=127.0.0.1 --cache-backend-redis-db=0 --no-interaction
        $PHP_BIN $MAGENTO_BIN setup:config:set --session-save=redis --session-save-redis-host=127.0.0.1 --session-save-redis-db=2 --no-interaction
        $PHP_BIN $MAGENTO_BIN setup:config:set --page-cache=redis --page-cache-redis-server=127.0.0.1 --page-cache-redis-db=1 --no-interaction
    fi
    
    # Optimize Composer autoloader
    log_info "Optimizing Composer autoloader..."
    $COMPOSER_BIN dump-autoload --optimize --no-dev
    
    log_success "Performance optimization completed"
}

# Verify installation
verify_installation() {
    log_info "Verifying installation..."
    cd "$MAGENTO_ROOT"
    
    # Check Magento status
    log_info "Checking Magento status..."
    $PHP_BIN $MAGENTO_BIN setup:db:status
    
    # Check module status
    log_info "Checking module status..."
    $PHP_BIN $MAGENTO_BIN module:status | grep -E "(Module|Enabled|Disabled)"
    
    # Check cache status
    log_info "Checking cache status..."
    $PHP_BIN $MAGENTO_BIN cache:status
    
    # Check index status
    log_info "Checking index status..."
    $PHP_BIN $MAGENTO_BIN indexer:status
    
    log_success "Installation verification completed"
}

# Main deployment function
deploy() {
    log_info "Starting Rivage Skincare deployment..."
    
    check_user
    check_magento_dir
    check_php_version
    check_services
    
    # Ask for confirmation
    read -p "Do you want to create a backup before deployment? (y/n): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        backup_current
    fi
    
    pull_changes
    install_dependencies
    set_permissions
    run_magento_setup
    optimize_performance
    verify_installation
    
    log_success "Deployment completed successfully!"
    log_info "Your Rivage Skincare website is now live!"
}

# Show usage
show_usage() {
    echo "Usage: $0 [OPTIONS]"
    echo ""
    echo "Options:"
    echo "  deploy     Run full deployment"
    echo "  backup     Create backup only"
    echo "  verify     Verify installation only"
    echo "  help       Show this help message"
    echo ""
    echo "Examples:"
    echo "  $0 deploy"
    echo "  $0 backup"
    echo "  $0 verify"
}

# Main script logic
case "${1:-deploy}" in
    deploy)
        deploy
        ;;
    backup)
        check_user
        check_magento_dir
        backup_current
        ;;
    verify)
        check_user
        check_magento_dir
        verify_installation
        ;;
    help|--help|-h)
        show_usage
        ;;
    *)
        log_error "Unknown option: $1"
        show_usage
        exit 1
        ;;
esac
