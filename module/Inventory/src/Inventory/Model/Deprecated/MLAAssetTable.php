<?php
namespace Inventory\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Inventory\Model\MLAAsset;

/**
 *
 * @author nmt
 *        
 */
class MLAAssetTable
{

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     *
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    /**
     *
     * @param unknown $id
     * @throws \Exception
     */
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

    public function getAssetsByCategoryID($id)
    {
        $adapter = $this->tableGateway->adapter;

        $sql = new Sql($adapter);
        $select = $sql->select();

        $where = 'category_id =' . $id;

        $select->from('mla_asset');
        $select->where($where);

        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();

        return $results;
    }

    public function getLimitAssetsByCategoryID($id, $limit, $offset)
    {
        $adapter = $this->tableGateway->adapter;

        $sql = new Sql($adapter);
        $select = $sql->select();

        $where = 'category_id =' . $id;

        $select->from('mla_asset');
        $select->where($where)
            ->limit($limit)
            ->offset($offset);

        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();

        return $results;
    }

    /**
     *
     * @param MLAAsset $input
     */
    public function add(MLAAsset $input)
    {
        $data = array(
            'name' => $input->name,
            'description' => $input->description,
            'category_id' => $input->category_id,
            'group_id' => $input->group_id,
            'tag' => $input->tag,
            'brand' => $input->brand,
            'model' => $input->model,
            'serial' => $input->serial,
            'origin' => $input->origin,
            'location' => $input->location,
            'status' => $input->status,
            'comment' => $input->comment,
            'created_on' => date('Y-m-d H:i:s')
        );
        $this->tableGateway->insert($data);

        return $this->tableGateway->lastInsertValue;
    }

    /**
     *
     * @param MLAAsset $input
     * @param unknown $id
     */
    public function update(MLAAsset $input, $id)
    {
        $data = array(
            'name' => $input->name,
            'description' => $input->description,
            'category_id' => $input->category_id,
            'group_id' => $input->group_id,
            'tag' => $input->tag,
            'brand' => $input->brand,
            'model' => $input->model,
            'serial' => $input->serial,
            'origin' => $input->origin,
            'location' => $input->location,
            'status' => $input->status,
            'comment' => $input->comment,
            'created_on' => date('Y-m-d H:i:s')
        );

        $where = 'id = ' . $id;
        $resultSet = $this->tableGateway->update($data, $where);
    }

    /**
     *
     * @param unknown $id
     */
    public function delete($id)
    {
        $where = 'id = ' . $id;
        $this->tableGateway->delete($where);
    }
}