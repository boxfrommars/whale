<?php
/**
 * @author Dmitry Groza <boxfrommars@gmail.com>
 */

namespace Whale\Dict;


use Doctrine\DBAL\Connection;

class DictService {

    /** @var  Connection */
    protected $_db;

    /**
     * @param Connection $db
     * @param array $options
     */
    public function __construct($db, $options = array()) {
        $this->setDb($db);
    }

    /**
     * @param string $tableName
     * @return array
     */
    public function fetchAll($tableName) {
        $qb = $this->getQuery($tableName);
        return $this->getDb()->executeQuery($qb)->fetchAll();
    }

    /**
     * @param Connection $db
     */
    public function setDb($db)
    {
        $this->_db = $db;
    }

    /**
     * @return Connection
     */
    public function getDb()
    {
        return $this->_db;
    }

    /**
     * @param string $tableName
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function getQuery($tableName) {
        $qb = $this->getDb()->createQueryBuilder();
        $qb->select("*")
            ->from($tableName, 't');
        return $qb;
    }
} 