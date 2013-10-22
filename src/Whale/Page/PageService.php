<?php
/**
 * @author Dmitry Groza <boxfrommars@gmail.com>
 */

namespace Whale\Page;

use Whale\Db\DbEntityService;

class PageService extends DbEntityService {

    /** @var string table name */
    protected $_name = 'page';

    protected $_serviceName = 'page';

    /** @var string table sequence */
    protected $_seq = 'page_id_seq';

    /**
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function getQuery() {
        $qb = $this->getDb()->createQueryBuilder();
        $qb->select("array_to_string(array_agg(a.page_url ORDER BY a.path), '/') AS url, e.*")
            ->from($this->getName(), 'e')
            ->innerJoin('e', 'page', 'a', 'a.path @> e.path')
            ->groupBy(array('e.id', 'e.path', 'e.page_url'))
            ->orderBy('"order"');
        return $qb;
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