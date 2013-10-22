<?php
/**
 * @author Dmitry Groza <boxfrommars@gmail.com>
 */

namespace Whale\Db;

use Whale\System;

class Entity {

    /** @var int */
    protected $_id;

    protected $_dbFields = array();

    public function __construct($data = array()){
        foreach ($data as $name => $value) {
            $method = 'set' . System::toCamelCase($name);
            $this->$method($value);
        }
    }

    public function raw(){
        $raw = array();
        foreach ($this->getDbFields() as $field) {
            if (!is_array($field)) {
                $field = array(
                    'name' => $field,
                    'type' => \PDO::PARAM_STR,
                );
            }

            $method = 'get' . System::toCamelCase($field['name']);

            $raw[$field['name']] = array(
                'value' => $this->$method(),
                'type' => $field['type']
            );
        }
        return $raw;
    }


    /**
     * @return int
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->_id = $id;
    }

    /**
     * @param array $dbFields
     */
    public function setDbFields($dbFields)
    {
        $this->_dbFields = $dbFields;
    }

    /**
     * @return array
     */
    public function getDbFields()
    {
        return $this->_dbFields;
    }
} 