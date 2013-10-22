<?php
/**
 * @author Dmitry Groza <boxfrommars@gmail.com>
 */

namespace Whale\Db;

use Doctrine\DBAL\Connection;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\ResolvedFormTypeInterface;

abstract class DbEntityService
{
    /** @var  Connection */
    protected $_db;
    /** @var string table name */
    protected $_name;
    /** @var string */
    protected $_serviceName;
    /** @var string table sequence */
    protected $_seq;

    /**
     * @param Connection $db
     * @param array $options
     */
    public function __construct($db, $options = array())
    {
        $this->setDb($db);
        if (array_key_exists('name', $options)) $this->setName($options['name']);
        if (array_key_exists('seq', $options)) $this->setSeq($options['seq']);
    }

    /**
     * @param DbEntity $entity
     * @return void
     */
    public function save($entity)
    {
        null === $entity->getId() ? $this->insert($entity) : $this->update($entity);
    }

    /**
     * @param DbEntity $entity
     */
    public function insert($entity)
    {
        $data = array();
        $types = array();

        foreach ($entity->raw() as $fieldName => $field) {
            $data[$fieldName] = $field['value'];
            $types[$fieldName] = $field['type'];
        }

        $this->getDb()->insert($this->getName(), $data, $types);
        $entity->setId($this->_db->lastInsertId($this->getSeq()));
    }

    /**
     * @param DbEntity $entity
     */
    public function update($entity)
    {
        $data = array();
        $types = array();

        foreach ($entity->raw() as $fieldName => $field) {
            $data[$fieldName] = $field['value'];
            $types[$fieldName] = $field['type'];
        }
        $this->getDb()->update($this->getName(), $data, array('id' => $entity->getId()), $types);
    }

    /**
     * @param int $id
     * @return bool|DbEntity
     */
    public function fetch($id) {
        $qb = $this->getQuery();
        $qb->add('where', 'e.id = :id');
        $entityData = $this->getDb()->executeQuery($qb, array('id' => $id))->fetch();;
        return ($entityData === false) ? false : $this->createEntity($entityData);
    }

    /**
     * @return DbEntity[]
     */
    public function fetchAll() {
        $qb = $this->getQuery();
        return $this->fetchQuery($qb);
    }

    /**
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function getQuery() {
        $qb = $this->getDb()->createQueryBuilder();
        $qb->select("e.*")->from($this->getName(), 'e');
        return $qb;
    }

    /**
     * @param \Doctrine\DBAL\Query\QueryBuilder $qb
     * @return array
     */
    public function fetchQuery($qb) {
        $entitiesData = $this->getDb()->executeQuery($qb)->fetchAll();
        $entities = array();
        foreach ($entitiesData as $entityData) {
            $entities[] = $this->createEntity($entityData);
        }
        return $entities;
    }

    /**
     * @return FormTypeInterface|ResolvedFormTypeInterface|string
     */
    public abstract function getForm();

    /**
     * @param array $data
     * @return DbEntity
     */
    public abstract function createEntity($data = array());

    /**
     * @return Connection
     */
    public function getDb()
    {
        return $this->_db;
    }

    /**
     * @param Connection $db
     */
    public function setDb($db)
    {
        $this->_db = $db;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

    /**
     * @return string table sequence
     */
    public function getSeq()
    {
        return $this->_seq;
    }

    /**
     * @param string $seq table sequence
     */
    public function setSeq($seq)
    {
        $this->_seq = $seq;
    }

    /**
     * @return string
     */
    public function getServiceName()
    {
        return $this->_serviceName;
    }

    /**
     * @param string $serviceName
     */
    public function setServiceName($serviceName)
    {
        $this->_serviceName = $serviceName;
    }
} 