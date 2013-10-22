<?php

namespace Whale\Db\Traits;

trait TitledTrait {

    /** @var string */
    protected $_title;

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->_title = $title;
    }
}