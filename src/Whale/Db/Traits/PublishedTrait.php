<?php

namespace Whale\Db\Traits;

trait PublishedTrait {

    /** @var bool */
    protected $_isPublished;

    /**
     * @return boolean
     */
    public function getIsPublished()
    {
        return $this->_isPublished;
    }

    /**
     * @param boolean $isPublished
     */
    public function setIsPublished($isPublished)
    {
        $this->_isPublished = (bool) $isPublished;
    }

}