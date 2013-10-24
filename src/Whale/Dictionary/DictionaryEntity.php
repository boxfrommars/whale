<?php
/**
 * @author Dmitry Groza <boxfrommars@gmail.com>
 */

namespace Whale\Dictionary;


use Whale\Db\Entity\DbContentEntity;

class DictionaryEntity extends DbContentEntity {

    protected $_name;

    protected $_dbFields = array(
        'title',
        'content',
        'name'
    );

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->_name;
    }
} 