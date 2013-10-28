<?php
/**
 * @author Dmitry Groza <boxfrommars@gmail.com>
 */

namespace Whale\Db;


use Silex\Application;
use Silex\ServiceProviderInterface;
use Whale\WhaleApplication;

class DbActionServiceProvider implements ServiceProviderInterface {
    /**
     * Registers services on the given app.
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Application $app An Application instance
     */
    public function register(Application $app)
    {
        $app['layout.action'] = 'admin/layout.twig';

        /** @var WhaleApplication $app */
        $app['action.db'] = $app->share(function ($app) {
            $actionService = new DbActionService($app);
            return $actionService;
        });

    }

    /**
     * Bootstraps the application.
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     */
    public function boot(Application $app)
    {
        // TODO: Implement boot() method.
    }
}