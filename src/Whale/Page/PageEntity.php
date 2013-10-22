<?php
/**
 * @author Dmitry Groza <boxfrommars@gmail.com>
 */

namespace Whale\Page;

use Whale\Db\Entity;
use Whale\Db\Traits\ContentTrait;
use Whale\Db\Traits\OrderTrait;
use Whale\Db\Traits\ParentTrait;
use Whale\Db\Traits\PublishedTrait;
use Whale\Db\Traits\TimestampableTrait;

class PageEntity extends Entity
{
    use ParentTrait;
    use TimestampableTrait;
    use ContentTrait;
    use PublishedTrait;
    use OrderTrait;

    /** @var string */
    protected $_name;
    /** @var string */
    protected $_path;
    /** @var string */
    protected $_entity;
    /** @var string */
    protected $_url;

    /** @var string */
    protected $_pageUrl;
    /** @var string */
    protected $_pageTitle;
    /** @var string */
    protected $_pageDescription;
    /** @var string */
    protected $_pageKeywords;

    protected $_dbFields = array(
        array(
            'name' => '"order"', // sic!
            'type' => \PDO::PARAM_INT
        ),

        'title',
        'content',
        array(
            'name' => 'is_published',
            'type' => \PDO::PARAM_BOOL
        ),

        'id_parent',
        'name',
        'path',
        'entity',

        'page_url',
        'page_title',
        'page_description',
        'page_keywords',

        'created_at',
        'updated_at'
    );
    /**
     * @return string
     */
    public function getEntity()
    {
        return $this->_entity;
    }

    /**
     * @param string $entity
     */
    public function setEntity($entity)
    {
        $this->_entity = $entity;
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
     * @return string
     */
    public function getPageDescription()
    {
        return $this->_pageDescription;
    }

    /**
     * @param string $pageDescription
     */
    public function setPageDescription($pageDescription)
    {
        $this->_pageDescription = $pageDescription;
    }

    /**
     * @return string
     */
    public function getPageKeywords()
    {
        return $this->_pageKeywords;
    }

    /**
     * @param string $pageKeywords
     */
    public function setPageKeywords($pageKeywords)
    {
        $this->_pageKeywords = $pageKeywords;
    }

    /**
     * @return string
     */
    public function getPageTitle()
    {
        return $this->_pageTitle;
    }

    /**
     * @param string $pageTitle
     */
    public function setPageTitle($pageTitle)
    {
        $this->_pageTitle = $pageTitle;
    }

    /**
     * @return string
     */
    public function getPageUrl()
    {
        return $this->_pageUrl;
    }

    /**
     * @param string $pageUrl
     */
    public function setPageUrl($pageUrl)
    {
        $this->_pageUrl = $pageUrl;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->_path;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->_path = $path;
    }


    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->_url = $url;
    }
} 