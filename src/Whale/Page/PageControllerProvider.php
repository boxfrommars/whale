<?php
/**
 * @author Dmitry Groza <boxfrommars@gmail.com>
 */

namespace Whale\Page;

use Doctrine\DBAL\Query\QueryBuilder;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\Route;
use Whale\Db\DbControllerProvider;

class PageControllerProvider extends DbControllerProvider {

    /**
     * Returns routes to connect to the given application.
     *
     * @param Application $app An Application instance
     * @return ControllerCollection A ControllerCollection instance
     */
    public function connect(Application $app)
    {
        /** @var \Whale\WhaleApplication|\Twig_Environment[] $app */

        /** @var ControllerCollection|Route $controllers */
        $controllers = parent::connect($app);

        $controllers->before(function () use ($app) {
            /** @var QueryBuilder $pagesQuery */
            $pagesQuery = $this->getService()->getQuery()->resetQueryPart('order')->addOrderBy('e.path', 'ASC');
            $app['twig']->addGlobal('_pages', $this->getService()->fetchQuery($pagesQuery));
            $app->log('admin before');
        });

        return $controllers;
    }
}