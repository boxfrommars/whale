<?php
/**
 * @author Dmitry Groza <boxfrommars@gmail.com>
 */

namespace Whale\Dict;


use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\ResolvedFormTypeInterface;
use Whale\Db\DbEntity;
use Whale\Db\DbEntityService;

class DictService extends DbEntityService {
    /**
     * @return FormTypeInterface|ResolvedFormTypeInterface|string
     */
    public function getForm()
    {
        // TODO: Implement getForm() method.
    }

    /**
     * @param array $data
     * @return DbEntity
     */
    public function createEntity($data = array())
    {
        // TODO: Implement createEntity() method.
    }
}