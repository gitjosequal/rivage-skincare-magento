MageWorkshop DetailedReview module extends default Magento 2 reviews functionality with rich features like configurable
 review fields and forms, form field validation and so on. 

## Rewritten/copied files ##

Some files were moved from the Magento core in order to fir some small bugs or get better compatibility with our code.
Here are that files/classes:

- Magento\Swatches\Helper\Media > MageWorkshop\DetailedReview\Helper;
- Magento\Catalog\Model\ResourceModel\Eav\Attribute > MageWorkshop\DetailedReview\Model\Attribute (pay a special attention to this one)
- Magento_Swatches/view/adminhtml/templates/catalog/product/attribute/js.phtml > view/adminhtml/templates/review/attribute/js.phtml
- Magento_Catalog/view/adminhtml/templates/catalog/product/attribute/options.phtml > view/adminhtml/templates/review/attribute/options.phtml
- Magento_Catalog/view/adminhtml/web/js/options.js > view/adminhtml/templates/review/attribute/js.phtml

## Coding tips ##

### Swap review status: ###
    
```sql
UPDATE m2_review SET status_id = CASE WHEN status_id = 1 THEN 2 WHEN status_id = 2 THEN 1 ELSE 3 END;
```
    
### Generating fixtures ###

**Generate reviews.** By default, from 100 to 150 reviews are generated for 50 random products. Product URLs are shown in the
console. Look for constants at the top of the *GenerateReviewFixturesCommand.php* file to change these values.
Note that this command generates only default Magento review data! Command:
   
    php bin/magento mageworkshop:detailedreview:generate-review-fixtures

**Generate review details.** This command generates random data for additional data for all review attributes configured 
in the DetailedReview module. This is especially useful for testing. Data will be moved into the flat tables via the 
reindex process. No data is generated if all reviews already have some associated data, but you can use optional 
*"--force"* key to overwrite that data (for example, if new attributes were added). Command: 

    php bin/magento mageworkshop:detailedreview:generate-detail-fixtures [--force]

**Create test customer profiles.** This command creates two customer profiles:

1) Email: qatester3131@gmail.com
Password: 1234567!
Group: General

1) Email: johnharnabi@gmail.com
Password: 1234567!
Group: Wholesale

    php bin/magento mageworkshop:detailedreview:create-test-customers
