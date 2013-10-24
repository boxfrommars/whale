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

    /** @var string  */
    protected $_entityLayout = 'admin/entity.twig';

    /** @var string  */
    protected $_listLayout = 'admin/list.twig';

    /**
     * @param DbEntityService $service
     * @param array $options
     */
    public function __construct($service, $options = array()) {
        $this->setService($service);
        if (array_key_exists('entity_layout', $options)) $this->setEntityLayout($options['entity_layout']);
        if (array_key_exists('list_layout', $options)) $this->setEntityLayout($options['list_layout']);
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

        $service = $this->getService();

        $controllers->get('/', function () use ($app, $service) {

            $entities = $service->fetchAll();

            return $app['twig']->render('admin/layout.twig', array(
                'content' => $app['twig']->render($this->getListLayout(), array(
                    'entities' => $entities,
                )),
            ));
        })->bind('admin_' . $service->getServiceName() . '_list');

        $processor = $this->_getProcessor($app);

        $controllers->match('/edit/{id}', $processor)->bind('admin_' . $service->getServiceName() . '_edit');
        $controllers->match('/create', $processor)->bind('admin_' . $service->getServiceName() . '_create');
        $controllers->match('/create/{paramName}/{paramValue}', function(Request $request, $paramName, $paramValue) use ($app, $processor) {
            return $processor($request, null, array($paramName => $paramValue));
        })->bind('admin_' . $service->getServiceName() . '_create_parented');


        $controllers->before(function () use ($app, $service) {
            $app['twig']->addGlobal('_service_name', $service->getServiceName());
        });

        return $controllers;
    }

    /**
     * @var \Whale\WhaleApplication|\Symfony\Component\Form\FormFactory[]|\Twig_Environment[]|\Symfony\Component\HttpFoundation\Session\Flash\FlashBag[] $app
     * @return callable
     */
    protected function _getProcessor($app){

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
                'content' => $app['twig']->render($this->getEntityLayout(), array(
                        'entity' => $page,
                        'form' => $form->createView(),
                    )),
            ));
        };
        return $processPage;
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
     * @param string $entityLayout
     */
    public function setEntityLayout($entityLayout)
    {
        $this->_entityLayout = $entityLayout;
    }

    /**
     * @return string
     */
    public function getEntityLayout()
    {
        return $this->_entityLayout;
    }

    /**
     * @param string $listLayout
     */
    public function setListLayout($listLayout)
    {
        $this->_listLayout = $listLayout;
    }

    /**
     * @return string
     */
    public function getListLayout()
    {
        return $this->_listLayout;
    }

}