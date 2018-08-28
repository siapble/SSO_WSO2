<?php

namespace siapble\sso_wso2;

use Illuminate\Support\ServiceProvider;

class SingleSignOnServiceProvider extends ServiceProvider {
	
	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register() {
		//		
		$this->app->singleton('SSO_WSO2', function() {
			return new SSO_WSO2;
		});
	}

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot() {			

    	$this->publishConfig();
	}

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {
        return array("SSO_WSO2");
    }

    private function publishConfig()
    {
        $configPath = $this->packagePath('config/wso2spc.php');
        $this->publishes([
            $configPath => config_path('wso2spc.php'),
        ], 'config');        
    }

    private function packagePath($path)
    {
        return __DIR__."/../$path";
    }
}
