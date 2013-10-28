<?php
/**
 * Created by PhpStorm.
 * User: xu
 * Date: 23.10.13
 * Time: 17:59
 */

namespace Whale\Dictionary;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Validator\Constraints as Assert;
use Whale\Db\DbEntityService;
use Whale\Db\Entity\DbContentEntity;

class DictionaryEntryForm extends AbstractType {

    /**
     * @var DbEntityService $dictService
     */
    protected $_service;

    public function __construct($dictService){
        $this->_service = $dictService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $defaultInputAttrs = array(
            'class' => 'input-block-level'
        );

        $builder
            ->add('id_dictionary', 'choice', array(
                'label' => 'Словарь',
                'attr' => $defaultInputAttrs,
                'choices' => $this->getDictionaries(),
                'required'  => true,
            ))
            ->add('title', 'text', array(
                'label' => 'Заголовок',
                'attr' => $defaultInputAttrs,
                'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 1)))
            ))
            ->add('content', 'textarea', array(
                'label' => 'Контент',
                'attr' => $defaultInputAttrs,
            ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'dictionary_form';
    }

    public function getDictionaries(){
        $entities = $this->_service->fetchAll();
        $choices = array();
        foreach ($entities as $entity) {
            /** @var DbContentEntity $entity */
            $choices[$entity->getId()] = $entity->getTitle();
        }

        return $choices;
    }
} 