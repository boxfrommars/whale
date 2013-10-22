<?php
/**
 * @author Dmitry Groza <boxfrommars@gmail.com>
 */

namespace Whale\Db;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Silex\Route;
use Symfony\Component\HttpFoundation\Request;

class DbControllerProvider implements ControllerProviderInterface {

    /** @var DbEntityService */
    protected $_service;

    /**
     * @param DbEntityService $service
     */
    public function __construct($service) {
        $this->setService($service);
    }

    /**
     * Returns routes to connect to the given application.
     *
     * @param Application $app An Application instance
     * @return ControllerCollection A ControllerCollection instance
     */
    public function connect(Application $app)
    {
        /** @var \Twig_Environment[] $app */
        /** @var ControllerCollection|Route $controllers */
        $controllers = $app['controllers_factory'];

        $controllers->get('/', function () use ($app) {
            return $app['twig']->render('admin/layout.twig', array(
                'content' => 'Hello!',
            ));
        });

        $service = $this->getService();
        $processor = $this->getProcessor($app);

        $controllers->match('/edit/{id}', $processor)->bind('admin_' . $service->getServiceName() . '_edit');
        $controllers->match('/create', $processor)->bind('admin_' . $service->getServiceName() . '_create');
        $controllers->match('/create/idParent/{idParent}', function(Request $request, $idParent) use ($app, $processor) {
            return $processor($request, null, array('idParent' => $idParent));
        })->bind('admin_' . $service->getServiceName() . '_create_parented');

        return $controllers;
    }

    /**
     * @param DbEntityService $service
     */
    public function setService($service)
    {
        $this->_service = $service;
    }

    /**
     * @return DbEntityService
     */
    public function getService()
    {
        return $this->_service;
    }

    /**
     * @var \Whale\WhaleApplication|\Symfony\Component\Form\FormFactory[]|\Twig_Environment[]|\Symfony\Component\HttpFoundation\Session\Flash\FlashBag[] $app
     * @return callable
     */
    protected function getProcessor($app){

        $service = $this->getService();

        $processPage = function(Request $request, $id = null, $params = array()) use ($app, $service) {
            $page = ($id === null) ? $service->createEntity($params) : $service->fetch($id);

            if ($page === false) $app->abort('404', "the entity (id={$id}) you are looking for could not be found");

            /** @var \Symfony\Component\Form\FormBuilder $formBuilder */
            $formBuilder = $app['form.factory']->createBuilder($service->getForm(), $page);
            $form = $formBuilder->getForm();
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->getService()->save($page);
                $app['flashbag']->add('success', 'запись сохранена');
                return $app->redirect($app->url('admin_' . $service->getServiceName() . '_edit', array('id' => $page->getId())));
            }

            return $app['twig']->render('admin/layout.twig', array(
                'content' => $app['twig']->render('admin/page.twig', array(
                        'page' => $page,
                        'form' => $form->createView(),
                    )),
            ));
        };
        return $processPage;
    }

}