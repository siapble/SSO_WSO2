# This package is compatible with Laravel 5.2

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

3. Add the alias.
```php
'aliases' => [
    ...
    'SSO_WSO2' => siapble\sso_wso2\Facades\SSO_WSO2Facade::class,
    ...
]
```

4. Publish config file, Following command to terminal.
```
php artisan vendor:publish
```

## Code Example
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use SSO_WSO2;

class TestController extends Controller
{    
    public function callback(){
        try {

            $request = \Request::all(); 
            if (!array_key_exists('code', $request)) {
                //redirect to index
                return redirect('/');
            }

            $processCode = $request['code'];

            $response = SSO_WSO2::callback($processCode);

            return $response;
       } catch (Exception $e) {
           return response()->json(
                array(                      
                        'Exception : '=>$e->getMessage()
                    )
            );
       }          
    }

    public function makeurl(){
    	try {

            $response = SSO_WSO2::make_authorization_url(url('/sso/callback'));
    		
    		return $response['result'];
    	} catch (Exception $e) {
    		return response()->json(
                array(                		
                 		'Exception : '=>$e->getMessage()
                 	)
            );
    	}
    }    
}

```