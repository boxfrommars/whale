<?php
/**
 * @author Dmitry Groza <boxfrommars@gmail.com>
 */

namespace Whale\Dictionary;


use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\ResolvedFormTypeInterface;
use Whale\Db\DbEntity;
use Whale\Db\DbEntityService;
use Whale\Db\Entity\DbContentEntity;
use Whale\Db\Entity\DbContentEntityService;
use Whale\Db\Entity\DbContentForm;

class DictionaryEntryService extends DbContentEntityService {

    /** @var string table name */
    protected $_name = 'dictionary_entry';

    protected $_serviceName = 'dictionary_entry';

    /** @var string table sequence */
    protected $_seq = 'dictionary_entry_id_seq';
}