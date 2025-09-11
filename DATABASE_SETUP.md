# Database Setup Guide for Rivage Skincare

## 🗄️ Database Configuration

### **Current Database Status:**
- **Database Name**: `rivage_skincare`
- **Backup File**: `rivageae_rivage.sql` (565MB)
- **Tables**: ~200+ tables including products, categories, orders, customers
- **Products**: ~500+ skincare products
- **Categories**: Complete category structure with Dead Sea products

## 📋 Database Setup Options

### **Option 1: Use Existing Backup (Recommended)**

1. **Upload the database backup to your server:**
   ```bash
   # Upload via SCP
   scp rivageae_rivage.sql user@your-server:/var/www/rivage/
   
   # Or upload via cloud storage
   # Download from: [Your cloud storage link]
   ```

2. **Import the database:**
   ```bash
   # Create database
   mysql -u root -p -e "CREATE DATABASE rivage_skincare CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   
   # Import database
   mysql -u root -p rivage_skincare < rivageae_rivage.sql
   
   # Create user and grant permissions
   mysql -u root -p -e "CREATE USER 'rivage_user'@'localhost' IDENTIFIED BY 'your_password';"
   mysql -u root -p -e "GRANT ALL PRIVILEGES ON rivage_skincare.* TO 'rivage_user'@'localhost';"
   mysql -u root -p -e "FLUSH PRIVILEGES;"
   ```

### **Option 2: Fresh Installation**

1. **Create empty database:**
   ```bash
   mysql -u root -p -e "CREATE DATABASE rivage_skincare CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   ```

2. **Run Magento installation:**
   ```bash
   php bin/magento setup:install \
       --base-url=http://your-domain.com \
       --db-host=localhost \
       --db-name=rivage_skincare \
       --db-user=rivage_user \
       --db-password=your_password \
       --admin-firstname=Admin \
       --admin-lastname=User \
       --admin-email=admin@your-domain.com \
       --admin-user=admin \
       --admin-password=admin123 \
       --language=en_US \
       --currency=USD \
       --timezone=America/New_York \
       --use-rewrites=1
   ```

3. **Import sample data (if available):**
   ```bash
   # If you have sample data files
   php bin/magento setup:upgrade
   ```

### **Option 3: Use Database Scripts**

1. **Export from current environment:**
   ```bash
   ./scripts/export-database.sh
   ```

2. **Import to new environment:**
   ```bash
   ./scripts/import-database.sh
   ```

## 🔧 Database Configuration

### **Environment Configuration (`app/etc/env.php`):**
```php
'db' => [
    'connection' => [
        'default' => [
            'host' => 'localhost',
            'dbname' => 'rivage_skincare',
            'username' => 'rivage_user',
            'password' => 'your_secure_password',
            'model' => 'mysql4',
            'engine' => 'innodb',
            'initStatements' => 'SET NAMES utf8;',
            'active' => '1',
        ]
    ]
],
```

### **MySQL Optimization Settings:**
```ini
# /etc/mysql/mysql.conf.d/mysqld.cnf
[mysqld]
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
innodb_flush_log_at_trx_commit = 2
query_cache_size = 64M
query_cache_type = 1
max_connections = 200
```

## 📊 Database Content Overview

### **Key Tables:**
- **Products**: `catalog_product_entity` (~500 products)
- **Categories**: `catalog_category_entity` (Complete category tree)
- **Orders**: `sales_order` (Customer orders)
- **Customers**: `customer_entity` (Customer accounts)
- **CMS Pages**: `cms_page` (Static pages)
- **Configuration**: `core_config_data` (Store settings)

### **Product Categories:**
- Face Care
- Body Care
- Hair Care
- Dead Sea Products
- Special Offers
- Gift Sets

### **Sample Products:**
- Dead Sea Mud Mask
- AHA Skin Glow Gel
- Bright Light Cream
- Emollient Hand Cream
- Dead Sea Salt Scrubs

## 🔒 Security Considerations

### **Database Security:**
1. **Use strong passwords**
2. **Limit database user permissions**
3. **Enable SSL connections**
4. **Regular backups**
5. **Monitor access logs**

### **Backup Strategy:**
```bash
# Daily automated backup
0 2 * * * /path/to/backup-script.sh

# Weekly full backup
0 3 * * 0 /path/to/full-backup.sh
```

## 🚀 Production Deployment

### **For Production Server:**

1. **Upload database backup securely:**
   ```bash
   # Use encrypted transfer
   scp -i your-key.pem rivageae_rivage.sql user@server:/tmp/
   ```

2. **Import with proper permissions:**
   ```bash
   # Create production database user
   mysql -u root -p -e "CREATE USER 'rivage_prod'@'localhost' IDENTIFIED BY 'strong_password';"
   mysql -u root -p -e "GRANT SELECT, INSERT, UPDATE, DELETE ON rivage_skincare.* TO 'rivage_prod'@'localhost';"
   ```

3. **Update environment configuration:**
   ```bash
   # Update app/etc/env.php with production credentials
   ```

## 📞 Support

If you need the database backup file:
1. **Download from current server**: Use the export script
2. **Cloud storage**: Upload to secure cloud storage
3. **Direct transfer**: Use secure file transfer methods

**Note**: The database contains sensitive customer data and should be handled securely according to data protection regulations.
