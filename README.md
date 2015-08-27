# yii2-yee-core
Yee CMS Core

Installation
------------

### Installing `yii2-app-advanced` application. 
More info: [Advanced Application Template Installation](https://github.com/yiisoft/yii2-app-advanced/blob/master/docs/guide/start-installation.md).
  
  1. Installing (using Composer)

    If you do not have [Composer](http://getcomposer.org/), follow the instructions in the
    [Installing Yii](https://github.com/yiisoft/yii2/blob/master/docs/guide/start-installation.md#installing-via-composer) section of the definitive guide to install it.

    With Composer installed, you can then install the application using the following commands:

    ```bash
    cd /var/www/
    composer global require "fxp/composer-asset-plugin:~1.0.0"
    composer create-project --prefer-dist yiisoft/yii2-app-advanced mysite.com
    ```

  2. Initialize the installed application

     Execute the `init` command and select `dev` as environment.

      ```bash
      cd /var/www/mysite.com/
      php init
      ```
  
  3. Configurate your web server:

     - For Apache config file could be the following:
       ```apacheconf
       <VirtualHost *:80>
         ServerName mysite.com
         ServerAlias www.mysite.com
         DocumentRoot "/var/www/mysite.com/"
         <Directory "/var/www/mysite.com/">
           AllowOverride All
         </Directory>
       </VirtualHost>
       ```
       
     - Create `.htaccess` file in the root folder with following content:
       ```apacheconf
       # prevent directory listings
       Options -Indexes
       RewriteEngine on

       RewriteCond %{REQUEST_URI} ^/admin/$
       RewriteRule ^(admin)/$ /$1 [R=301,L]
       RewriteCond %{REQUEST_URI} ^/admin
       RewriteRule ^admin(/.+)?$ /backend/web/$1 [L,PT]

       RewriteCond %{REQUEST_URI} ^.*$
       RewriteRule ^(.*)$ /frontend/web/$1
       ```
       
     - Create `.htaccess` file in `backend/web/` and `frontend/web/` folders with following content:
       ```apacheconf
       RewriteEngine on
       # if a directory or a file exists, use the request directly
       RewriteCond %{REQUEST_FILENAME} !-f
       RewriteCond %{REQUEST_FILENAME} !-d
       # otherwise forward the request to index.php
       RewriteRule . index.php
       ```    

  4. Create a new database and adjust the `components['db']` configuration in `common/config/main-local.php` accordingly.

  5. Apply migrations with console command `php yii migrate`.


#####Your `yii2-app-advanced` application is installed. Visit your site, the site should work and message _Congratulations! You have successfully created your Yii-powered application_ should be displayed.
       
### Update application configuration
  1. Update `frontend/config/main.php` file:
 
     add
     ```php
     'homeUrl' => '/',
     ```
     and
     
     ```php
     'components' => [
         'request' => [
             'baseUrl' => '',
         ],
      ]
     ```
     
     and
     
     ```php
     'components' => [
         'urlManager' => [
             'class' => 'yii\web\UrlManager',
             'showScriptName' => false,
             'enablePrettyUrl' => true,
             'rules' => array(
                 '<module:auth>/<action:\w+>' => '<module>/default/<action>',
                 '<action:\w+>' => 'site/<action>',
                 '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
             )
         ],
     ]
     ```

  2. Update `backend/config/main.php` file:
  
     add
     ```php
     'homeUrl' => '/admin',
     ```
     
     and
     
     ```php
     'components' => [
         'request' => [
             'baseUrl' => '/admin',
         ],
      ]
     ```
     
     and
     
     ```php
     'components' => [
         'urlManager' => [
             'class' => 'yii\web\UrlManager',
             'showScriptName' => false,
             'enablePrettyUrl' => true,
             'rules' => array(
                 '<module:\w+>/' => '<module>/default/index',
                 '<module:\w+>/<action:\w+>/<id:\d+>' => '<module>/default/<action>',
                 '<module:\w+>/<action:(create)>' => '<module>/default/<action>',
                 '<module:\w+>/<controller:\w+>' => '<module>/<controller>/index',
                 '<module:\w+>/<controller:\w+>/<action:\w+>/<id:\d+>' => '<module>/<controller>/<action>',
                 '<module:\w+>/<controller:\w+>/<action:\w+>' => '<module>/<controller>/<action>',
                 '<controller:\w+>/<id:\d+>' => '<controller>/view',
                 '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                 '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
             )
         ],
     ]
     ```

  6. Set `minimum-stability` to `dev` and add `"prefer-stable": true` in `/var/www/mysite.com/composer.json`

### Install Yee CMS Core Module

  1. Install `yeesoft/yii2-yee-core` module
     - Run this command:
       ```bash
       composer require --prefer-dist yeesoft/yii2-yee-core "*"
       ```

     - Apply migrations:
       ```bash
       yii migrate --migrationPath=@vendor/yeesoft/yii2-yee-core/migrations/
       ```
      
     - Remove `['components']['user']` setting from `frontend/config/main.php` and `backend/config/main.php` configurations.

     - Add `['components']['user']` and `['modules']['yee']` setting in `common/config/main.php`:
       ```php
       'components' => [
           'user' => [
               'class' => 'yeesoft\components\User',
               'on afterLogin' => function($event) {
                  \yeesoft\models\UserVisitLog::newVisitor($event->identity->id);
               }
           ],
       ]
       ```
       
       ```php
       'modules' => [
           'yee' => [
               'class' => 'yeesoft\Yee',
           ],
       ],
       ```
       
  1. Set the same value for `['components']['request']['cookieValidationKey']` in `frontend/config/main-local.php` and `backend/config/main-local.php`. This is necessary for correct working of cookie-based login.

  1. Configurate your mailer `['components']['mailer']` in `common/config/main-local.php`.

  1. Update backend controller:

     - Replace `backend/controllers/SiteController.php` content by this:
       ```php
       namespace backend\controllers;

       use yeesoft\controllers\admin\BaseController;

       class SiteController extends BaseController
       {
           public function actionIndex()
           {
               return $this->render('index');
           }
       }
       ```

#####Yee CMS Core is installed! Now you can sign up on the site. After registration you can set `superadmin` field to `1` in `user` table to enable super administrator rights for your user. Now log in on the site and visit `http://yoursite.com/admin`, empty control panel should be displayed. You can create your own Yee modules or install existing to extend the functionality of Yee CMS.

### Install Yee CMS Modules

  1. Install [Yee Auth Module](https://github.com/yeesoft/yii2-yee-auth)
  1. Install [Yee Settings Module](https://github.com/yeesoft/yii2-yee-settings)
  1. Install [Yee Menu Module](https://github.com/yeesoft/yii2-yee-menu)
  1. Install [Yee User Module](https://github.com/yeesoft/yii2-yee-user)
  1. Install [Yee Media Module](https://github.com/yeesoft/yii2-yee-media)
  1. Install [Yee Post Module](https://github.com/yeesoft/yii2-yee-post)
  1. Install [Yee Page Module](https://github.com/yeesoft/yii2-yee-page)
  1. Install [Comments Module](https://github.com/yeesoft/yii2-comments)
  1. Install [Yee Comments Module](https://github.com/yeesoft/yii2-yee-comment)
  1. Install [Gii Generator For Yee](https://github.com/yeesoft/yii2-yee-generator)
