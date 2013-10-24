<?php
/**
 * @author Dmitry Groza <boxfrommars@gmail.com>
 */

namespace Whale\Db;

use Doctrine\DBAL\Connection;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\ResolvedFormTypeInterface;

class DbEntityService
{
    /** @var  Connection */
    protected $_db;
    /** @var string table name */
    protected $_name;
    /** @var string */
    protected $_serviceName;
    /** @var string table sequence */
    protected $_seq;

    /** @var Callable */
    protected $_entityCreator;

    /** @var Callable */
    protected $_formCreator;

    /**
     * @param Connection $db
     * @param array $options
     */
    public function __construct($db, $options = array())
    {
        $this->setDb($db);
        if (array_key_exists('name', $options)) $this->setName($options['name']);
        if (array_key_exists('seq', $options)) $this->setSeq($options['seq']);
        if (array_key_exists('service_name', $options)) $this->setServiceName($options['service_name']);
        if (array_key_exists('entity_creator', $options)) $this->setEntityCreator($options['entity_creator']);
        if (array_key_exists('form_creator', $options)) $this->setFormCreator($options['form_creator']);
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
            $fieldName = '"' . $fieldName . '"';
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
    public function getForm() {
        $creator = $this->getFormCreator();
        return $creator();
    }


    public function createEntity($data = array()) {
        $creator = $this->getEntityCreator();
        return $creator($data);
    }

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

    /**
     * @param Callable $formCreator
     */
    public function setFormCreator($formCreator)
    {
        $this->_formCreator = $formCreator;
    }

    /**
     * @return Callable
     */
    public function getFormCreator()
    {
        return $this->_formCreator;
    }

    /**
     * @param mixed $entityCreator
     */
    public function setEntityCreator($entityCreator)
    {
        $this->_entityCreator = $entityCreator;
    }

    /**
     * @return Callable
     */
    public function getEntityCreator()
    {
        return $this->_entityCreator;
    }
} 