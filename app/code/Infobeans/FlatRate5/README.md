#Infobeans FlatRate5 Magento 2 Integration
##Quick instructions

###Manual Install
- Create folder structure /app/code/Infobeans/FlatRate5/
- Download the .ZIP file from the marketplace
- Extract the contents of the .ZIP file to the folder you just created

#### Run install commands:
```
php bin/magento module:enable Infobeans_FlatRate5
```
php bin/magento setup:upgrade
```
- You may need to run the following command to flush the Magento cache:
```
php bin/magento cache:flush
