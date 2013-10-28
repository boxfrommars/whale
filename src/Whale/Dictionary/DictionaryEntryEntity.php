<?php
/**
 * @author Dmitry Groza <boxfrommars@gmail.com>
 */

namespace Whale\Dictionary;


use Whale\Db\Entity\DbContentEntity;

class DictionaryEntryEntity extends DbContentEntity {

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

    public function getIdParent()
    {
        return $this->_idDictionary;
    }

    public function setIdParent($idParent)
    {
        $this->_idDictionary = $idParent;
    }
} 