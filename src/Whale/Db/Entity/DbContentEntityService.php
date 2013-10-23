<?php
/**
 * @author Dmitry Groza <boxfrommars@gmail.com>
 */

namespace Whale\Db\Entity;


use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\ResolvedFormTypeInterface;
use Whale\Db\DbEntity;
use Whale\Db\DbEntityService;

class DbContentEntityService extends DbEntityService {

    /**
     * @return FormTypeInterface|ResolvedFormTypeInterface|string
     */
    public function getForm()
    {
        return new DbContentForm();
    }

    /**
     * @param array $data
     * @return DbEntity
     */
    public function createEntity($data = array())
    {
        return new DbContentEntity($data);
    }
} 