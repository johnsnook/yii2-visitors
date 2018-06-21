Ip filtering for Yii2
=====================
This extension gets the visitors ip address, logs the access and checks if it's blacklisted or whitelisted.  If it's a blacklist ip address, it redirects them to a blowoff page.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist johnsnook/yii2-ip-filter "*"
```

or add

```
"johnsnook/yii2-ip-filter": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code by  :

```php
<?= \johnsnook\ipFilter\AutoloadExample::widget(); ?>```