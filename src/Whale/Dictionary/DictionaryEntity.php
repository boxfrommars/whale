<?php
/**
 * @author Dmitry Groza <boxfrommars@gmail.com>
 */

namespace Whale\Dictionary;


use Whale\Db\Entity\DbContentEntity;

class DictionaryEntity extends DbContentEntity {

    protected $_idDictionary;

    protected $_dbFields = array(
        'title',
        'content',
        'id_dictionary'
    );

    /**
     * @param mixed $idDictionary
     */
    public function setIdDictionary($idDictionary)
    {
        $this->_idDictionary = $idDictionary;
    }

    /**
     * @return mixed
     */
    public function getIdDictionary()
    {
        return $this->_idDictionary;
    }
} 