<?php

namespace Whale\Db\Traits;

trait ContentTrait {

    use TitledTrait;

    /** @var string */
    protected $_content;

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->_content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->_content = $content;
    }
}