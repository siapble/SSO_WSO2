<?php 
namespace siapble\sso_wso2\Facades;
use Illuminate\Support\Facades\Facade;
class SSO_WSO2Facade extends Facade {
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() {
        return 'SSO_WSO2';
    }
}