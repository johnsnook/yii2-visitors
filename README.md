Ip filtering for Yii2
=====================
This extension gets the visitors ip address, logs the access and checks if it's blacklisted or whitelisted.  If it's a blacklist ip address, it redirects them to a blowoff page.

Installation
------------

### 1. Download

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```bash
php composer.phar require --prefer-dist johnsnook/yii2-ip-filter "*"
```

or add

```
"johnsnook/yii2-ip-filter": "*"
```

to the require section of your `composer.json` file.

### 2. Configure


Once the extension is installed, add 'ipFilter' to the bootstrap section of your configuration file  :

```php
    'bootstrap' => [
        'log',
        'ipFilter',
    ],
```
Then add the module definition
```php
    'modules' => [
        ...
        'ipFilter' => [
            'class' => 'johnsnook\ipFilter\Module',
            'ipInfoKey' => 'asfdasdfasdfasdfasdf',  // Get a free key from ipinfo.io
            'proxyCheckKey' => '012345-012345-012345-012345',   // Get a free key from proxycheck.io
            'mapquestKey' => 'zuppityboobopadiddlydiddly',  // Get a free key from https://developer.mapquest.com/user/me/apps
            'blowOff' => 'site/get-out'
        ],
        ...
    ],
```

The routes are defined in the Module file as $urlRules.  These can also be redefined in the module definition.  By default, they look like this:
```php
    public $urlRules = [
        'visitor' => 'ipFilter/visitor/index',
        'visitor/index' => 'ipFilter/visitor/index',
        'visitor/<id>' => 'ipFilter/visitor/view',
        'visitor/update/<id>' => 'ipFilter/visitor/update',
    ];
```

### 3. Update database schema

The last thing you need to do is updating your database schema by applying the
migrations. Make sure that you have properly configured `db` application component
and run the following command:

```bash
$ php yii migrate/up --migrationPath=@vendor/johnsnook/yii2-ip-filter/migrations
```

Customization
-----
So, you should be able to go to ```http://yoursite.com/visitor``` and see the index.

But you'll probably want to make your own views.  If it was me, I'd copy the controller and views to your backend or basic controllers & views directories.  But maybe there's some best practices way to do it.

Usage
-----

If you want to find out information on the current user, you can get the visitor model from the module and use it like so:
```php
    $visitor = \Yii::$app->getModule('ipFilter')->visitor;
    // give a special hello to people in Atlanta or your ex wife
    if ($visitor->info->city === 'Atlanta' || $visitor->info->ip_address === '99.203.4.238') {
        echo "Your city sucks balls";
    }
```

To see it live, check out https://snooky.biz/visitor