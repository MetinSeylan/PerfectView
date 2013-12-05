<?php namespace MetinSeylan\PerfectView;

use Illuminate\Support\ServiceProvider;

class PerfectViewServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['PerfectView'] = $this->app->share(function($app)
                {
                    $app['PerfectView.loaded'] = true;
                    return new PerfectView;
                });
                
	}
        
        
        public function boot()
        {
            
            
            
            $this->package('MetinSeylan/PerfectView');
            
        }

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('PerfectView');
	}

}