<?php
/**
 * @author Dmitry Groza <boxfrommars@gmail.com>
 */

namespace Whale\Page;

use Whale\Db\EntityService;

class PageService extends EntityService {

    /** @var string table name */
    protected $_name = 'page';

    protected $_serviceName = 'page';

    /** @var string table sequence */
    protected $_seq = 'page_id_seq';

    /**
     * @return PageEntity[]
     */
    public function fetchAll() {
        $qb = $this->getQuery();
        return $this->fetchQuery($qb);
    }

    /**
     * @param \Doctrine\DBAL\Query\QueryBuilder $qb
     * @return array
     */
    public function fetchQuery($qb) {
        $pagesData = $this->getDb()->executeQuery($qb)->fetchAll();
        $pages = array();
        foreach ($pagesData as $pageData) {
            $pages[] = $this->createEntity($pageData);
        }
        return $pages;
    }

    /**
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function getQuery() {
        $qb = $this->getDb()->createQueryBuilder();
        $qb->select("array_to_string(array_agg(a.page_url ORDER BY a.path), '/') AS url, p.*")
            ->from($this->getName(), 'p')
            ->innerJoin('p', 'page', 'a', 'a.path @> p.path')
            ->groupBy(array('p.id', 'p.path', 'p.page_url'))
            ->orderBy('"order"');
        return $qb;
    }

    /**
     * @param int $id
     * @return bool|PageEntity
     */
    public function fetch($id) {
        $qb = $this->getQuery();
        $qb->add('where', 'p.id = :id');
        $pageData = $this->getDb()->executeQuery($qb, array('id' => $id))->fetch();;
        return ($pageData === false) ? false : $this->createEntity($pageData);
    }

    /**
     * @param array $data
     * @return PageEntity
     */
    public function createEntity($data = array()) {
        return new PageEntity($data);
    }

    /**
     * @return PageForm
     */
    public function getForm()
    {
        return new PageForm($this);
    }
}