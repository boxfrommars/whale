<?php
/**
 * @author Dmitry Groza <boxfrommars@gmail.com>
 */

namespace Whale\Dictionary;

use Doctrine\DBAL\Query\QueryBuilder;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\Route;
use Symfony\Component\HttpFoundation\Request;
use Whale\Db\DbActionService;
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
        /** @var \Twig_Environment[]|DbEntityService[]|DbActionService[] $app */
        /** @var ControllerCollection|Route $controllers */
        $controllers = $app['controllers_factory'];

        $dictName = $app['dict.dict_service']->getServiceName();
        $entryName = $app['dict.entry_service']->getServiceName();

        $controllers->get('/', $app['action.db']->create(
            'list',
            array(
                'view' => array('linkType' => 'view', 'contentLayout' => 'admin/list.twig'),
                'service' => $app['dict.dict_service'],
            )
        ))->bind($this->getRoute($dictName, 'list'));

        $controllers->match('/edit/{id}', $app['action.db']->create(
            'entity',
            array(
                'view' => array('contentLayout' => 'admin/entity.twig'),
                'service' => $app['dict.dict_service'],
            )
        ))->bind($this->getRoute($dictName, 'edit'));

        $controllers->match('/view/{id}', function(Request $request, $id) use ($app) {
            $action = $app['action.db']->create(
                'list',
                array(
                    'view' => array('linkType' => 'edit', 'contentLayout' => 'admin/listParented.twig'),
                    'service' => $app['dict.entry_service'],
                    'query' => array(
                        'where' => array('e.id_dictionary = :id_dict'),
                        'params' => array('id_dict' => $id),
                    ),
                )
            );
            return $action($request, $id);

        })->bind($this->getRoute($dictName, 'view'));

        $controllers->match('/edit/{idParent}/{id}', function(Request $request, $idParent, $id) use ($app) {
            $action = $app['action.db']->create(
                'entity',
                array(
                    'view' => array('contentLayout' => 'admin/entity.twig'),
                    'service' => $app['dict.entry_service'],
                )
            );
            return $action($request, $id, array('idParent' => $idParent));

        })->bind($this->getRoute($entryName, 'edit'));

        $controllers->before(function () use ($app) {
            $app['twig']->addGlobal('sidebar', $app['twig']->render('admin/list.twig', array(
                'entities' => $app['dict.dict_service']->fetchAll(),
                'entityName' => $app['dict.dict_service']->getServiceName(),
                'linkType' => 'view',
            )));
        });


        return $controllers;
    }

    /**
     * @param string $name
     * @param string $action
     * @return string
     */
    protected function getRoute($name, $action){
        return "admin_{$name}_{$action}";
    }
}