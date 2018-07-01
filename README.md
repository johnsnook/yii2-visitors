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

Then add the bare-minimum module definition
```php
    'modules' => [
        ...
        'ipFilter' => [
            'class' => 'johnsnook\ipFilter\Module',
        ],
        ...
    ],
```

The routes are defined in the Module file as $urlRules.  These can also be redefined in the module definition.  By default, they look like this for prettyUrls:
```php
    'visitor' => '/ipFilter/visitor/index',
    'visitor/index' => '/ipFilter/visitor/index',
    'visitor/blowoff' => '/ipFilter/visitor/blowoff',
    'visitor/<id>' => 'ipFilter/visitor/view',
    'visitor/update/<id>' => 'ipFilter/visitor/update',
```

### 3. Update database schema

The last thing you need to do is updating your database schema by applying the
migrations. Make sure that you have properly configured `db` application component
and run the following command:

```bash
$ php yii migrate/up --migrationPath=@vendor/johnsnook/yii2-ip-filter/migrations
```

Free API Keys
-----
1) For the map to render in Visitor view, you must have a MapQuest key.  Go to https://developer.mapquest.com/plan_purchase/steps/business_edition/business_edition_free/register for a free API key.
If you don't have this set, the map won't display.

2) Ipinfo.io limits the number of requests each day to 100 but with a key you can make 1000 a day.  Go to https://ipinfo.io/signup for a free API key
If you don't have this set, you'll be limited to 100 requests per day.

3) Proxycheck.io limits the number of requests each day to 100 but with a key you can make 1000 a day.  Go to https://proxycheck.io/ for a free API key
If you don't have this set, you'll be limited to 100 requests per day.

4) Whatismybrowser.com is serious business, so having an API is mandatory to use their service.  Go to https://developers.whatismybrowser.com/api/signup/basic for a free API key, but be prepared to provide an "app name" and website.
If you don't have this set, no data beyond the basic USER_AGENT string will be captured.

Customization
-----
So, you should be able to go to  ```http://yoursit.biz/index.php?r=ipFilter/visitor/index``` or, if you have prettyUrl enabled, ```http://yoursite.com/visitor``` and see the visitor index.

But you'll probably want to make your own views.  If it was me, I'd copy the controller and views to your backend or basic controllers & views directories.  But maybe there's some best practices way to do it.

I have left the layout empty so that the pages should be rendered with your layouts/theme.

When you're done getting all your keys, and deciding that there are some controller actions you're not interested in tracking, your module configuration might look something like this:
```php
    'modules' => [
        ...
        'ipFilter' => [
            'class' => 'johnsnook\ipFilter\Module',
            'ipInfoKey' => 'Not a real key, obviously',
            'proxyCheckKey' => 'Not a real key, obviously',
            'mapquestKey' => 'Not a real key, obviously',
            'blowOff' => 'site/nope',
            'ignorables' => [
                'acontroller' => ['ignore-me', 'ignore-that'],
                'whitelist' => ['127.0.0.1', '24.99.155.86']
            ]
        ],
        ...
    ],
```
As you see, you can add a custom 'blowoff' controller action.  The visitor will be passed in so you can display the name and blowoff message.

A couple of things to note here about the 'ignorables' configuration array.  You can add a controller and actions to ignore as well as a whitelist of IP addresses to ignore.  These will not be added to the access log.

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

Importing Existing Apache Access Logs
-----

To kickstart your visitor data, you can import apache2 logs, as long as you (not www-data) have permissions to view them and they are in the standard apache format.

This is achieved via the very excellent parser library which can be found at https://github.com/kassner/log-parser.

By default, it assumes that your access logs are at '/etc/httpd/logs' since that's where mine are.  You can specify another path as the first argument.

The second argument is for specifying which files you'd like to import.  By default, it looks for access*. but a comma delimted list with no spaces can be provided instead.

```bash
# The default, looks for /etc/httpd/logs/access*
php yii ipFilter/import/logs

# Looks for /my/own/private/idaho/access*
php yii ipFilter/import/logs '/my/own/private/idaho'

# Will process /etc/httpd/log/access_log-20180603 and /etc/httpd/log/access_log-20180610 ONLY.
php yii ipFilter/import/logs '/etc/httpd/logs' access_log-20180603,access_log-20180610
```

To see it live, check out https://snooky.biz/visitor

Screenshots!
-----

The main screen
![ipfilter1](https://user-images.githubusercontent.com/4065107/42129954-e352eaea-7ca1-11e8-8db8-8cb44ce4f2fc.png)

Detail visitor view
![ipfilter2](https://user-images.githubusercontent.com/4065107/42129951-e330717c-7ca1-11e8-9337-52f16c8c8a5e.png)

Updating the name and/or message for a visitor.
![ipfilter3](https://user-images.githubusercontent.com/4065107/42129952-e33bce3c-7ca1-11e8-9fd2-9e06a9833a2f.png)

The default blocked user view. (As seen from Tor)
![ipfilter4](https://user-images.githubusercontent.com/4065107/42129953-e3476580-7ca1-11e8-84cf-aef11158446b.png)

