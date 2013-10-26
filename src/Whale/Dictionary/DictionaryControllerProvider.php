<?php
/**
 * @author Dmitry Groza <boxfrommars@gmail.com>
 */

namespace Whale\Dictionary;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\Route;
use Whale\Db\DbControllerProvider;
use Whale\Db\DbEntityService;
use Whale\Db\Entity\DbContentEntryServiceProvider;

class DictionaryControllerProvider extends DbControllerProvider {

    /**
     * Returns routes to connect to the given application.
     *
     * @param Application $app An Application instance
     * @return ControllerCollection A ControllerCollection instance
     */
    public function connect(Application $app)
    {
        /** @var \Twig_Environment[]|DbEntityService[] $app */
        /** @var ControllerCollection|Route $controllers */
        $controllers = $app['controllers_factory'];
        $processor = $this->_getEntityProcessor($app, $app['dict.dict_service']);
        $entryProcessor = $this->_getEntityProcessor($app, $this->getService());

        $controllers->get('/', $this->_getListProcessor($app, $app['dict.dict_service']))->bind('admin_' . $app['dict.dict_service']->getServiceName() . '_list');
        $controllers->match('/edit/{id}', $processor)->bind('admin_' . $app['dict.dict_service']->getServiceName() . '_edit');

        /*
        $controllers->get('/', $this->_getListProcessor($app))->bind('admin_' . $this->_serviceName . '_list');
        $controllers->match('/edit/{id}', $processor)->bind('admin_' . $this->_serviceName . '_edit');
        $controllers->match('/create', $processor)->bind('admin_' . $this->_serviceName . '_create');
        $controllers->match('/create/{paramName}/{paramValue}', function(Request $request, $paramName, $paramValue) use ($app, $processor) {
            return $processor($request, null, array($paramName => $paramValue));
        })->bind('admin_' . $this->_serviceName . '_create_parented');
*/
        $controllers->before(function () use ($app) {
            $app['twig']->addGlobal('_service_name', $this->_serviceName);
        });

        return $controllers;
    }
}