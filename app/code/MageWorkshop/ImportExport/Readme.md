## Intro ##

For now the main goal of the module is to create import functionality that supports reviews data import from Magento 1 into Magento 2. Import supports the data of the Detailed Review extension for Magento 1, so some column names in the CSV file from Magento 1 do not directly match the Magento 2 attribute codes. Below can find information about the import rules, validation and columns mapping.

To import reviews from Magento 1 first you need to export reviews into the CSV file via Catalog > Reviews and Ratings > Reviews Import/Export. Create new Profile with the following settings:
- Type - Export;
- Store - All Stores;
- Use Full Path For Review Images - No.

## Required columns ##

Required columns in the import file are:
- _entity_id_ (translated to "review_id" during import)
- _sku_
- _status_id_
- _nickname_
- _title_
- _detail_

Column names that must not be used:
- _customer_id_ - use _customer_email_ instead. Customer ID will is loaded by email. This column will have no effect;
- _admin_response_ - use _response_ for compatibility purposes with Magento 1. Otherwise, one of the fields will be ignored;
- _review_id_ - it is formed from _entity_id_ column.

## Import process ##

In order to keep the functionality as flexible as possible, we make import the decision based on the column input type. Column names in the CSV must match the field codes. So, it is recommended to create necessary fields in Magento 2 before importing data. Though, it is also possible to add the columns later and update the data. For example, you may need to create two new text attributes for the fields _good_detail_ and _no_good_detail_ with the following configuration:  
- Input Type - Textarea;
- Field Code - _good_detail_ and _no_good_detail_ respectively;
- Default Label - up to you.

These fields must be added to the review forms in order to be displayed. 

**Text/textarea fields**

These fields are imported "as is" without any additional processing. These are columns like "title", "detail", "age" etc.

**Select/mutiselect**

Not supported right now! Need to add functionality to automatically create options. 

**Images**

Image files in Magento 1 are stored in the folder _<magento_root>/media/detailedreview/_
These files must be manually moved into Magento 2 to the folder _<magento_root>/pub/media/mageworkshop/imageloader/_
Note that you'll be notified if some images can not be found in the filesystem. This is not a critical error and you can continue import. Information about the missed images will not be attached to reviews.

##ATTENTION! Module limitations!

Features that haven't been implemented yet:
- import stores data by code - for now each review belongs to all stores. Store codes are ignored;
- customer search by website - we just take the first customer with the suitable email independently of the website it belongs to;
- _Pros/Cons_ and _Recommend To_ will not be moved (these fields are empty in the target client's website);
- a Video attribute will not be moved as this attribute type is not supported yet in DetailedReview for Magento 2; 
- Select/Multiselect attributes are not supported right now! Need to add functionality to automatically create options.
- Ratings are not imported