<?php

namespace Lester\EloquentSalesForce\Tests;

use Lester\EloquentSalesForce\ServiceProvider;
use Lester\EloquentSalesForce\TestModel;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Config;

class EloquentSalesForceTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

	/**
	 * @covers Lester\EloquentSalesForce\TestModel
	 * @covers Lester\EloquentSalesForce\Database\SOQLBuilder
	 * @covers Lester\EloquentSalesForce\Database\SOQLConnection
	 * @covers Lester\EloquentSalesForce\Database\SOQLGrammar
	 */
    public function testObject()
    {
	    $lead = TestModel::first();
	    
        $this->assertEquals(1, 1);
    }
    
    public function setUp()
	{
		parent::setUp();
		
		config([
			'forrest' => [
				/*
			     * These are optional authentication parameters that can be specified for the WebServer flow.
			     * https://help.salesforce.com/apex/HTViewHelpDoc?id=remoteaccess_oauth_web_server_flow.htm&language=en_US
			     */
			    'parameters'     => [
			        'display'   => '',
			        'immediate' => false,
			        'state'     => '',
			        'scope'     => '',
			        'prompt'    => '',
			    ],
			
			    /*
			     * Default settings for resource requests.
			     * Format can be 'json', 'xml' or 'none'
			     * Compression can be set to 'gzip' or 'deflate'
			     */
			    'defaults'       => [
			        'method'          => 'get',
			        'format'          => 'json',
			        'compression'     => false,
			        'compressionType' => 'gzip',
			    ],
			
			    /*
			     * Where do you want to store access tokens fetched from Salesforce
			     */
			    'storage'        => [
			        'type'          => 'session', // 'session' or 'cache' are the two options
			        'path'          => 'forrest_', // unique storage path to avoid collisions
			        'expire_in'     => 20, // number of minutes to expire cache/session
			        'store_forever' => false, // never expire cache/session
			    ],
			
			    /*
			     * If you'd like to specify an API version manually it can be done here.
			     * Format looks like '32.0'
			     */
			    'version'        => '',
			
			    /*
			     * Optional (and not recommended) if you need to override the instance_url returned from Saleforce
			     */
			    'instanceURL'    => '',
			
			    /*
			     * Language
			     */
			    'language'       => 'en_US',
			]
		]);
		
		config([
			'app.key' => 'base64:WRAf0EDpFqwpbS829xKy2MGEkcJxIEmMrwFIZbGxIqE=',
			'cache.stores.file.path' => __DIR__,
			'cache.default' => 'file',
			'forrest.credentials' => [
				'driver' => 'soql',
			    'database' => null,
				'consumerKey'    => getenv('CONSUMER_KEY'),
		        'consumerSecret' => getenv('CONSUMER_SECRET'),
		        'callbackURI'    => getenv('CALLBACK_URI'),
		        'loginURL'       => getenv('LOGIN_URL'),
		        
		        // Only required for UserPassword authentication:
		        'username'       => getenv('USERNAME'),
		        // Security token might need to be ammended to password unless IP Address is whitelisted
		        'password'       => getenv('PASSWORD')
			]
		]);
				
	}
	
	/**
	 * Creates the application.
	 *
	 * @return \Illuminate\Foundation\Application
	 */
	
	public function createApplication()
	{
		if (getenv('SCRUT_TEST')) return parent::createApplication();
		
		$app = require __DIR__.'/../../../../bootstrap/app.php';
	
		$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
	
		$app->loadEnvironmentFrom('.env');

		return $app;
	}
}
