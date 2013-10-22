<?php
/**
 * @author Dmitry Groza <boxfrommars@gmail.com>
 */

namespace Whale\Page;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints as Assert;

class PageForm extends AbstractType {


    /** @var  PageService */
    protected $_service;


    public function __construct($service)
    {
        $this->_service = $service;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $parentIdChoices = $this->_getParentIdChoices();
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
//            $form = $event->getForm();
//            $data = $event->getData();
//            print_r($data);
        });

        $defaultInputAttrs = array(
            'class' => 'input-block-level'
        );

        $builder
            ->add('id_parent', 'choice', array(
                'label' => 'Родитель',
                'attr' => $defaultInputAttrs,
                'choices' => $parentIdChoices,
                'required'  => false,
            ))
            ->add('title', 'text', array(
                'label' => 'Заголовок',
                'attr' => $defaultInputAttrs,
                'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 5)))
            ))
            ->add('content', 'textarea', array(
                'label' => 'Контент',
                'attr' => $defaultInputAttrs,
            ))
            ->add('is_published', 'checkbox', array(
                'label' => 'Опубликована',
                'required' => false,
            ))
            ->add('order', 'integer', array(
                'attr' => $defaultInputAttrs,
                'label' => 'Порядковый номер',
//                'constraints' => array(new Assert\)
            ))
            ->add('page_url', 'text', array(
                'label' => 'url (относительный)',
                'attr' => $defaultInputAttrs,
                'constraints' => array()
            ))
            ->add('page_title', 'text', array(
                'label' => 'СЕО title',
                'attr' => $defaultInputAttrs,
                'constraints' => array()
            ))
            ->add('page_description', 'text', array(
                'label' => 'СЕО description',
                'attr' => $defaultInputAttrs,
                'constraints' => array()
            ))
            ->add('page_keywords', 'text', array(
                'label' => 'СЕО keywords',
                'attr' => $defaultInputAttrs,
                'constraints' => array()
            ))
            ->add('name', 'text');
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'page';
    }


    protected function _getParentIdChoices() {
        $pages = $this->_service->fetchAll();
        $choices = array();
        foreach ($pages as $page) {
            $choices[$page->getId()] = $page->getTitle();
        }

        return $choices;
    }
}