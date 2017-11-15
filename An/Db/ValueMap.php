<?php
namespace An\Db;

use Tk\Db\Tool;
use Tk\Db\Map\ArrayObject;
use Tk\DataMap\Db;
use Tk\DataMap\Form;


/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class ValueMap extends \App\Db\Mapper
{
    /**
     * Mapper constructor.
     *
     * @param \Tk\Db\Pdo|null $db
     */
    public function __construct($db = null)
    {
        parent::__construct($db);
        $this->setMarkDeleted('');
    }

    /**
     * @return \Tk\DataMap\DataMap
     */
    public function getDbMap()
    {
        if (!$this->dbMap) {
            $this->setTable('animal_value');
            $this->dbMap = new \Tk\DataMap\DataMap();
            $this->dbMap->addPropertyMap(new Db\Integer('id'), 'key');
            $this->dbMap->addPropertyMap(new Db\Integer('typeId', 'type_id'));
            $this->dbMap->addPropertyMap(new Db\Integer('placementId', 'placement_id'));
            $this->dbMap->addPropertyMap(new Db\Text('name'));
            $this->dbMap->addPropertyMap(new Db\Text('value'));
            $this->dbMap->addPropertyMap(new Db\Text('notes'));
            $this->dbMap->addPropertyMap(new Db\Date('modified'));
            $this->dbMap->addPropertyMap(new Db\Date('created'));
        }
        return $this->dbMap;
    }

    /**
     * @return \Tk\DataMap\DataMap
     */
    public function getFormMap()
    {
        if (!$this->formMap) {
            $this->formMap = new \Tk\DataMap\DataMap();
            $this->formMap->addPropertyMap(new Form\Integer('id'), 'key');
            $this->formMap->addPropertyMap(new Form\Integer('typeId'));
            $this->formMap->addPropertyMap(new Form\Integer('placementId'));
            $this->formMap->addPropertyMap(new Form\Text('name'));
            $this->formMap->addPropertyMap(new Form\Text('value'));
            $this->formMap->addPropertyMap(new Form\Text('notes'));
        }
        return $this->formMap;
    }

//    /**
//     * @param $placementId
//     */
//    public function removeAllByPlacementId($placementId)
//    {
//        $list = $this->findFiltered(array('placementId' => $placementId));
//        foreach ($list as $v) {
//            $v->delete();
//        }
//    }
//
//    /**
//     * Find filtered records
//     *
//     * @param array $filter
//     * @param Tool $tool
//     * @return ArrayObject
//     */
//    public function findFiltered($filter = array(), $tool = null)
//    {
//        //if (!$tool) $tool = \Tk\Db\Tool::create('orderBy');
//        $from = sprintf('%s a ', $this->getDb()->quoteParameter($this->getTable()));
//        $where = '';
//
//        if (!empty($filter['keywords'])) {
//            $kw = '%' . $this->getDb()->escapeString($filter['keywords']) . '%';
//            $w = '';
//            $w .= sprintf('a.name LIKE %s OR ', $this->getDb()->quote($kw));
//            $w .= sprintf('a.notes LIKE %s OR ', $this->getDb()->quote($kw));
//            if (is_numeric($filter['keywords'])) {
//                $id = (int)$filter['keywords'];
//                $w .= sprintf('a.id = %d OR ', $id);
//            }
//            if ($w) {
//                $where .= '(' . substr($w, 0, -3) . ') AND ';
//            }
//        }
//
//        if (!empty($filter['typeId'])) {
//            $where .= sprintf('a.type_id = %s AND ', (int)$filter['typeId']);
//        }
//
//        if (!empty($filter['placementId'])) {
//            $where .= sprintf('a.placement_id = %s AND ', (int)$filter['placementId']);
//        }
//
//        if (!empty($filter['name'])) {
//            $where .= sprintf('a.name = %s AND ', $this->quote($filter['name']));
//        }
//
//        if (!empty($filter['value'])) {
//            $where .= sprintf('a.value = %s AND ', $this->quote($filter['value']));
//        }
//
//        if (!empty($filter['dateFrom'])) {
//            /** @var \DateTime $dtef */
//            $dtef = \Tk\Date::floor($filter['dateFrom']);
//            $where .= sprintf('a.created >= %s AND ', $this->quote($dtef->format(\Tk\Date::FORMAT_ISO_DATETIME)) );
//        }
//        if (!empty($filter['dateTo'])) {
//            /** @var \DateTime $dtet */
//            $dtet = \Tk\Date::ceil($filter['dateTo']);
//            $where .= sprintf('a.created <= %s AND ', $this->quote($dtet->format(\Tk\Date::FORMAT_ISO_DATETIME)) );
//        }
//
//        if (!empty($filter['exclude'])) {
//            $w = $this->makeMultiQuery($filter['exclude'], 'a.id', 'AND', '!=');
//            if ($w) {
//                $where .= '('. $w . ') AND ';
//            }
//        }
//
//        if ($where) {
//            $where = substr($where, 0, -4);
//        }
//
//        $res = $this->selectFrom($from, $where, $tool);
//        return $res;
//    }


    /**
     * Find filtered records
     *
     * @param array $filter
     * @param Tool $tool
     * @return ArrayObject
     */
    public function findFiltered($filter = array(), $tool = null)
    {
        if (!$tool) $tool = \Tk\Db\Tool::create('orderBy');

        list($from, $where) = $this->processFilter($filter);
        $r = $this->selectFrom($from, $where, $tool);
        return $r;
    }

    /**
     * @param $filter
     * @return array
     */
    protected function processFilter($filter)
    {
        $from = sprintf('%s a ', $this->quoteTable($this->getTable()));
        $where = '';

        if (!empty($filter['keywords'])) {
            $kw = '%' . $this->getDb()->escapeString($filter['keywords']) . '%';
            $w = '';
            $w .= sprintf('a.name LIKE %s OR ', $this->getDb()->quote($kw));
            $w .= sprintf('a.notes LIKE %s OR ', $this->getDb()->quote($kw));
            if (is_numeric($filter['keywords'])) {
                $id = (int)$filter['keywords'];
                $w .= sprintf('a.id = %d OR ', $id);
            }
            if ($w) {
                $where .= '(' . substr($w, 0, -3) . ') AND ';
            }
        }

        if (!empty($filter['typeId'])) {
            $where .= sprintf('a.type_id = %s AND ', (int)$filter['typeId']);
        }

        if (!empty($filter['placementId'])) {
            $where .= sprintf('a.placement_id = %s AND ', (int)$filter['placementId']);
        }

        if (!empty($filter['profileId']) || !empty($filter['courseId'])) {
            $from .= sprintf(', %s b', $this->quoteTable('animal_type'));
            $where .= sprintf('a.type_id = b.id AND ');
            if (!empty($filter['profileId'])) {
                $where .= sprintf('b.profile_id = %s AND ', (int)$filter['profileId']);
            }
            if (!empty($filter['courseId'])) {
                $from .= sprintf(', %s c', $this->quoteTable('placement'));
                $where .= sprintf('a.placement_id = c.id AND c.course_id = %s AND ', (int)$filter['courseId']);
            }
        }

        if (!empty($filter['name'])) {
            $where .= sprintf('a.name = %s AND ', $this->quote($filter['name']));
        }

        if (!empty($filter['value'])) {
            $where .= sprintf('a.value = %s AND ', $this->quote($filter['value']));
        }

        if (!empty($filter['dateFrom'])) {
            /** @var \DateTime $dtef */
            $dtef = \Tk\Date::floor($filter['dateFrom']);
            $where .= sprintf('a.created >= %s AND ', $this->quote($dtef->format(\Tk\Date::FORMAT_ISO_DATETIME)) );
        }
        if (!empty($filter['dateTo'])) {
            /** @var \DateTime $dtet */
            $dtet = \Tk\Date::ceil($filter['dateTo']);
            $where .= sprintf('a.created <= %s AND ', $this->quote($dtet->format(\Tk\Date::FORMAT_ISO_DATETIME)) );
        }

        if (!empty($filter['exclude'])) {
            $w = $this->makeMultiQuery($filter['exclude'], 'a.id', 'AND', '!=');
            if ($w) {
                $where .= '('. $w . ') AND ';
            }
        }

        if ($where) {
            $where = substr($where, 0, -4);
        }

        $r = array($from, $where);
        return $r;

    }

    /**
     * @param array $filter
     * @param Tool $tool
     * @return array
     */
    public function findTotals($filter, $tool = null)
    {
        if (!$tool) $tool = \Tk\Db\Tool::create('orderBy');
        list($from, $where) = $this->processFilter($filter);
        if (!$where) $where = '1';

        $sql = sprintf('SELECT a.name, a.type_id, SUM(a.value) AS total, COUNT(a.id) as \'count\'
FROM %s
WHERE %s
GROUP BY a.type_id
ORDER BY a.name', $from, $where);
        $stm = $this->getDb()->prepare($sql);
        $stm->execute();
        $r = $stm->fetchAll();
        return $r;
    }


}