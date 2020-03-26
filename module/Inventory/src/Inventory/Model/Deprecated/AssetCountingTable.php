<?php
namespace Inventory\Model;

use Zend\Db\TableGateway\TableGateway;
use Inventory\Model\AssetCounting;

class AssetCountingTable
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

    public function add(AssetCounting $input)
    {
        $data = array(
            'name' => $input->name,
            'description' => $input->description,
            'start_date' => $input->start_date,
            'end_date' => $input->end_date,

            'asset_cat_id' => $input->asset_cat_id,
            'status' => $input->status,
            'created_by' => $input->created_by,
            'created_on' => date('Y-m-d H:i:s')
        );

        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }

    public function update(AssetCounting $input, $id)
    {
        $data = array(
            'category' => $input->name,
            'description' => $input->description,
            'start_date' => $input->start_date,
            'end_date' => $input->end_date,

            'asset_cat_id' => $input->asset_cat_id,
            'status' => $input->status,
            'created_by' => $input->created_by,
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

    public function getCountings()
    {
        $adapter = $this->tableGateway->adapter;

        $sql = "SELECT t1.*, t2.category as asset_category
		FROM mla_asset_counting as t1
		LEFT JOIN mla_asset_categories AS t2
		ON t2.id = t1.asset_cat_id
		";

        $statement = $adapter->query($sql);
        $result = $statement->execute();

        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($result);
        return $resultSet;
    }
}