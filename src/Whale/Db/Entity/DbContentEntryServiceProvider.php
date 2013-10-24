<?php
/**
 * @author Dmitry Groza <boxfrommars@gmail.com>
 */

namespace Whale\Db\Entity;


use Silex\Application;
use Silex\ServiceProviderInterface;

class DbContentEntryServiceProvider implements ServiceProviderInterface {
    /**
     * Registers services on the given app.
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Application $app An Application instance
     */
    public function register(Application $app)
    {
        $app['content.entity'] = $app->protect(function($data = array()){
            return new DbContentEntity($data);
        });
        $app['content.form'] = $app->protect(function(){
            return new DbContentForm();
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