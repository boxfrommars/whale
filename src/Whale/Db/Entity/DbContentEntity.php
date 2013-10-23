<?php
/**
 * Created by PhpStorm.
 * User: xu
 * Date: 23.10.13
 * Time: 17:54
 */

namespace Whale\Db\Entity;


use Whale\Db\DbEntity;
use Whale\Db\Traits\ContentTrait;

class DbContentEntity extends DbEntity {
    use ContentTrait;

    protected $_dbFields = array(
        'title',
        'content',
    );
} 