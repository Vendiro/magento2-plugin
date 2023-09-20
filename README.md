<p align="center">
  <img src="https://github-production-user-asset-6210df.s3.amazonaws.com/24823946/269228391-1e4a0d54-673b-4b4d-acf2-fdb82d5dc707.png" alt="Vendiro Magento Plugin Logo">
</p>

# Vendiro Magento Plugin

The Vendiro Magento plugin offers seamless integration, allowing you to connect your Magento store with the Vendiro platform. This robust plugin empowers you to effortlessly import orders from more than 40 European marketplaces into your Magento® 2 store. Beyond order management, it also handles stock level synchronization and shipment confirmation, enhancing the efficiency of your e-commerce operations.

## Installation
Prior to initiating the installation process, we strongly advise creating backups of both your webshop files and the database.

### Installation using Composer ###
Magento® 2 utilizes Composer for managing module packages and libraries. Composer serves as a PHP dependency manager, allowing you to declare the libraries your project relies on and handling their installation and updates.

To determine if Composer is installed on your server, execute the following command:

```
composer –v
``` 
If your server doesn’t have composer installed, you can easily install it by using this manual: https://getcomposer.org/doc/00-intro.md

Step-by-step to install the Magento® 2 extension through Composer:

1.	Connect to your server running Magento® 2 using SSH or other method (make sure you have access to the command line).
2.	Locate your Magento® 2 project root.
3.	Install the Magento® 2 extension through composer and wait till it's completed:
```
composer require vendiro/magento2-plugin
``` 
4.	Once completed run the Magento® module enable command:
```
bin/magento module:enable Vendiro_Connect
``` 
5.	After that run the Magento® upgrade and clean the caches:
```
php bin/magento setup:upgrade
php bin/magento cache:flush
```
6.  If Magento® is running in production mode you also need to redeploy the static content:
```
php bin/magento setup:static-content:deploy
```
7.  After the installation: Go to your Magento® admin portal and open ‘Stores’ > ‘Configuration’ > ‘Vendiro’ 
   

## Compatibility

* PHP Versions: PHP 7.4, PHP 8.1, PHP 8.2
* Magento Versions: Magento 2.3.5 up to Magento 2.4.6

  
