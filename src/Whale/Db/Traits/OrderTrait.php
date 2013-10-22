<?php

namespace Whale\Db\Traits;

trait OrderTrait {

    /** @var int */
    protected $_order;

    /**
     * @return int
     */
    public function getOrder()
    {
        return $this->_order;
    }

    /**
     * @param int $order
     */
    public function setOrder($order)
    {
        $this->_order = $order;
    }

}