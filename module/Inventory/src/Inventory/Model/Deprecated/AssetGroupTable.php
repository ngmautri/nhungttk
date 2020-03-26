<?php
namespace Inventory\Model;

use Zend\Db\TableGateway\TableGateway;
use Inventory\Model\AssetGroup;

class AssetGroupTable
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

    public function add(AssetGroup $input)
    {
        $data = array(
            'category_id' => $input->category_id,
            'name' => $input->description,
            'description' => $input->description,
            'created_on' => date('Y-m-d H:i:s')
        );
        $resultSet = $this->tableGateway->insert($data);
    }

    public function update(AssetGroup $input, $id)
    {
        $data = array(
            'category_id' => $input->category_id,
            'name' => $input->description,
            'description' => $input->description,
            'created_on' => date('Y-m-d H:i:s')
        );
        $resultSet = $this->tableGateway->update($data, (int) $id);
    }

    public function delete($id)
    {
        $this->tableGateway->delete(array(
            'id' => (int) $id
        ));
    }
}