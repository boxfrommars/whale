<?php
/**
 * @author Dmitry Groza <boxfrommars@gmail.com>
 */

namespace Whale\Dictionary;


use Silex\Application;
use Silex\ServiceProviderInterface;
use Whale\Db\DbEntityService;
use Whale\WhaleApplication;

class DictionaryServiceProvider implements ServiceProviderInterface {
    /**
     * Registers services on the given app.
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Application $app An Application instance
     */
    public function register(Application $app)
    {
        /** @var WhaleApplication $app */
        $app['dict.entry_entity'] = $app->protect(function($data = array()){
            return new DictionaryEntryEntity($data);
        });
        $app['dict.dict_entity'] = $app->protect(function($data = array()){
            return new DictionaryEntity($data);
        });
        $app['dict.dict_service'] = $app->share(function ($app) {
            return  new DbEntityService($app['db'], array(
                'name' => 'dictionary',
                'service_name' => 'dictionary',
                'seq' => 'dictionary_id_seq',
                'entity_creator' => $app['dict.dict_entity'],
                'form_creator' => $app['content.form'],
            ));
        });
        $app['dict.entry_form'] = $app->protect(function() use ($app) {
            return new DictionaryEntryForm($app['dict.dict_service']);
        });
        $app['dict.entry_service'] = $app->share(function ($app) {
            return  new DbEntityService($app['db'], array(
                'name' => 'dictionary_entry',
                'service_name' => 'dictionary_entry',
                'seq' => 'dictionary_entry_id_seq',
                'entity_creator' => $app['dict.entry_entity'],
                'form_creator' => $app['dict.entry_form'],
            ));
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