## Installation
1. Begin by installing this package through Composer. Just run following command to terminal.
```
composer require "siapble/sso_wso2:dev-master"
```

2. Add the service provider. Open `config/app.php`, and add a new item to the providers array.
```php
'providers' => [
    ...
    siapble\sso_wso2\SingleSignOnServiceProvider::class,
    ...
]
```

3. Publish config file, Following command to terminal.
```
php artisan vendor:publish
```

4. Add the alias.
```php
'aliases' => [
    ...
    'SSO_WSO2' => siapble\sso_wso2\Facades\SSO_WSO2Facade::class,
    ...
]
```