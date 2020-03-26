<?php
namespace Inventory\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Zend\Db\ResultSet;
use Inventory\Model\SparepartCategory;

class SparepartCategoryTable
{

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function get($id)
    {
        $id = (int) $id;

        $rowset = $this->tableGateway->select(array(
            'id' => $id
        ));
        $row = $rowset->current();
        if (! $row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function add(SparepartCategory $input)
    {
        $data = array(
            'name' => $input->name,
            'description' => $input->description,
            'parent_id' => $input->parent_id,
            'created_on' => date('Y-m-d H:i:s')
        );

        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }

    public function update(SparepartCategory $input, $id)
    {
        $data = array(
            'name' => $input->name,
            'description' => $input->description,
            'parent_id' => $input->parent_id,
            'created_on' => date('Y-m-d H:i:s')
        );

        $where = 'id = ' . $id;
        $this->tableGateway->update($data, $where);
    }

    public function delete($id)
    {
        $where = 'id = ' . $id;
        $this->tableGateway->delete($where);
    }

    /*
     * SELECT t2.id, t2.name as l1, t3.id as child_id, t3.name as l2
     * FROM mla_sparepart_cats as t1
     * LEFT JOIN mla_sparepart_cats AS t2
     * ON t2.parent_id = t1.id
     * LEFT JOIN mla_sparepart_cats AS t3 ON t3.parent_id = t2.id
     * WHERE t1.name = '_ROOT_'
     *
     *
     * SELECT * From mla_sparepart_cats_members as t1
     * inner JOIN mla_sparepart_cats as t2 on t2.id = t1.sparepart_cat_id
     *
     * SELECT count(t1.sparepart_cat_id), t2.id as cat_id From mla_sparepart_cats_members as t1
     * Inner JOIN mla_sparepart_cats as t2 on t2.id = t1.sparepart_cat_id group by t2.id
     *
     */

    /*
     *
     * SELECT t1.id, t2.id, t2.name as l1, t3.id as child_id, t3.name as l2, t6.members
     * FROM mla_sparepart_cats as t1
     * LEFT JOIN mla_sparepart_cats AS t2
     * ON t2.parent_id = t1.id
     * LEFT JOIN mla_sparepart_cats AS t3 ON t3.parent_id = t2.id
     * LEFT JOIN
     * (SELECT count(t4.sparepart_cat_id) as members, t4.sparepart_cat_id as cat_id
     * From mla_sparepart_cats_members as t4 Inner JOIN mla_sparepart_cats as t5 on t5.id = t4.sparepart_cat_id group by t4.sparepart_cat_id) as t6 ON t6.cat_id = t2.id
     * WHERE t1.name = '_ROOT_'
     */
    public function getCategories()
    {
        $adapter = $this->tableGateway->adapter;

        $sql = new Sql($adapter);
        $select = $sql->select();

        $select->from(array(
            't1' => 'mla_sparepart_cats'
        ));
        $select->columns(array(
            'id1' => 'id',
            'level1' => 'name'
        ));
        $select->join(array(
            't2' => 'mla_sparepart_cats'
        ), 't2.parent_id = t1.id', array(
            'id2' => 'id',
            'level2' => 'name'
        ), \Zend\Db\Sql\Select::JOIN_LEFT);
        $select->join(array(
            't3' => 'mla_sparepart_cats'
        ), 't3.parent_id = t2.id', array(
            'id3' => 'id',
            'level3' => 'name'
        ), \Zend\Db\Sql\Select::JOIN_LEFT);
        $select->where('t1.name="_ROOT_"');

        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();

        return $results;
    }

    public function getCategories1()
    {
        $adapter = $this->tableGateway->adapter;

        $sql = "SELECT t1.id, t2.id, t2.name as l1, t3.id as child_id, t3.name as l2, t6.members
		FROM mla_sparepart_cats as t1
		LEFT JOIN mla_sparepart_cats AS t2
		ON t2.parent_id = t1.id
		LEFT JOIN mla_sparepart_cats AS t3 ON t3.parent_id = t2.id
		LEFT JOIN
		(SELECT count(t4.sparepart_cat_id) as members, t4.sparepart_cat_id as cat_id
		From mla_sparepart_cats_members as t4 Inner JOIN mla_sparepart_cats as t5 on t5.id = t4.sparepart_cat_id group by t4.sparepart_cat_id) as t6 ON t6.cat_id = t2.id
		WHERE t1.name = '_ROOT_'";

        $statement = $adapter->query($sql);
        $result = $statement->execute();

        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($result);
        return $resultSet;
    }

    public function getCategoriesOf($id)
    {
        $adapter = $this->tableGateway->adapter;

        $sql = "SELECT t2.id as category_id, t2.name as category_name FROM `mla_sparepart_cats_members` as t1 
		inner join mla_sparepart_cats as t2 On t2.id = t1.sparepart_cat_id
		where t1.sparepart_id=?";

        $statement = $adapter->query($sql);
        $result = $statement->execute(array(
            $id
        ));

        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($result);
        return $resultSet;
    }
}