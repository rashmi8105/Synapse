<?php

use Symfony\Component\ClassLoader\ApcClassLoader;
use Symfony\Component\HttpFoundation\Request;

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';

// Use APC for autoloading to improve performance.
// Change 'sf2' to a unique prefix in order to prevent cache key conflicts
// with other applications also using APC.
/*
$apcLoader = new ApcClassLoader('sf2', $loader);
$loader->unregister();
$apcLoader->register(true);
*/

ini_set("memory_limit",-1);
set_time_limit(0);
require_once __DIR__.'/../app/AppKernel.php';
//require_once __DIR__.'/../app/AppCache.php';
/*
switch (getenv('SYNAPSE_ENV')) {
	case 'prod':
		$kernel = new AppKernel('prod', false);
		break;

	case 'qa';
		$kernel = new AppKernel('qa', false);
		break;

    case 'uat';
        $kernel = new AppKernel('uat', false);
        break;

    case 'staging';
        $kernel = new AppKernel('staging', false);
        break;

	default:
		$kernel = new AppKernel('dev', false);
		break;
}*/

$kernel = new AppKernel('integration', false);
//$kernel = new AppKernel('qa', false);
$kernel->loadClassCache();
//$kernel = new AppCache($kernel);

// When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter
//Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();
Request::setTrustedProxies(array('127.0.0.1', $request->server->get('REMOTE_ADDR')));
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
