# Multi Currency support for Advanced Reporting  

## DISCLAIMER: This is a proof-of-concept implementation, use at your own risk!

This module enables multi currency support for the Magento Advanced Reporting.  

The module adds reporting currency and exchange rates to the analytics settings.  

The store_config.csv and the orders.csv files are intercepted and order values are recalculated with the provided exchange rate.  

For debugging this module you can use n98-magerun2 to trigger a data collection.  

n98-magerun2.phar sys:cron:run analytics_collect_data  

An encrypted data.tgz file will then be created in the pub/media/analytics directory
