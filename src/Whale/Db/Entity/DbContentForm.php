<?php
/**
 * Created by PhpStorm.
 * User: xu
 * Date: 23.10.13
 * Time: 17:59
 */

namespace Whale\Db\Entity;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Validator\Constraints as Assert;

class DbContentForm extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $defaultInputAttrs = array(
            'class' => 'input-block-level'
        );

        $builder
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
        return 'content_form';
    }
} 