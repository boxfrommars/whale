<?php
/**
 * @author Dmitry Groza <boxfrommars@gmail.com>
 */

namespace Whale\Db;


use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Whale\WhaleApplication;

class DbActionService {

    /** @var WhaleApplication|\Twig_Environment[]|FlashBag[]|FormFactory[] */
    protected $_app;

    public function __construct($app){
        $this->_app = $app;
    }

    /**
     * array(
     *      'view' => array(
     *          'type' => 'edit'|'view',
     *          'name' => 'admin/entity.twig',
     *      ),
     *      'service' => DbEntityService,
     * )
     *
     * @param string $actionType
     * @param array  $options
     * @throws \Exception
     * @return callable
     */
    public function create($actionType, $options){
        switch ($actionType) {
            case 'entity':
                return $this->createEntityAction($options);
                break;
            case 'list':
                return $this->createListAction($options);
                break;
            default:
                throw new \Exception("no <b>{$actionType}</b> action defined");
        }
    }

    /**
     * @param $options
     * @return callable
     */
    public function createEntityAction($options){
        return function(Request $request, $id = null, $params = array()) use ($options) {

            /** @var DbEntityService $service */
            $service = $options['service'];
            $layoutName = !empty($options['view']['layout']) ? $options['view']['layout'] : $this->_app['layout.action'];

            $entity = ($id === null) ? $service->createEntity($params) : $service->fetch($id);

            if ($entity === false) $this->_app->abort('404', "the entity (id={$id}) you are looking for could not be found");

            /** @var \Symfony\Component\Form\FormBuilder $formBuilder */
            $formBuilder = $this->_app['form.factory']->createBuilder($service->getForm(), $entity);
            $form = $formBuilder->getForm();
            $form->handleRequest($request);

            if ($form->isValid()) {
                $service->save($entity);
                $this->_app['flashbag']->add('success', 'запись сохранена');
                return $this->_app->redirect($this->_app->url('admin_' . $service->getServiceName() . '_edit', array_merge(array('id' => $entity->getId()), $params)));
            }

            return $this->_app['twig']->render($layoutName, array(
                'content' => $this->_app['twig']->render($options['view']['contentLayout'], array(
                    'entity' => $entity,
                    'form' => $form->createView(),
                    'entityName' => $service->getServiceName(),
                    'viewType' => empty($options['view']['type']) ? 'edit' : 'view',
                ))
            ));
        };
    }

    /**
     * @param $options
     * @return callable
     */
    public function createListAction($options){
        return function () use ($options) {
            /** @var DbEntityService $service */
            $service = $options['service'];

            $qb = $service->getQuery();
            if (!empty($options['query']['where'])) {
                foreach ($options['query']['where'] as $where) {
                    $qb->add('where', $where);
                }
            }

            $this->_app->log($qb->getSQL());
            $this->_app->log(print_r($options['query'], true));

            $queryParams = empty($options['query']['params']) ? array() : $options['query']['params'];

            $layoutName = !empty($options['view']['layout']) ? $options['view']['layout'] : $this->_app['layout.action'];

            return $this->_app['twig']->render($layoutName, array(
                'content' => $this->_app['twig']->render($options['view']['contentLayout'], array(
                    'entities' => $service->fetchQuery($qb, $queryParams),
                    'entityName' => $service->getServiceName(),
                    'viewType' => empty($options['view']['type']) ? 'edit' : $options['view']['type'],
                )),
            ));
        };
    }
} 