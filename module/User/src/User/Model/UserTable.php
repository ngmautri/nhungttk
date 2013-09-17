<?php

namespace User\Model;
use Zend\Db\TableGateway\TableGateway;

class UserTable
{
	protected $tableGateway;

	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

	/*
	 *
	 */
	public function fetchAll()
	{
		$resultSet = $this->tableGateway->select();
		return $resultSet;
	}


	/*
	 *
	 */
	public function getUserByID($id)
	{
		$id  = (int) $id;
		$rowset = $this->tableGateway->select(array('id' => $id));
		$row = $rowset->current();

		if (!$row) {
			throw new \Exception("Could not find row $id");
		}
		return  $row;
	}


	/*
	 *
	 */
	public function saveUser(User $user){

		$data = array(
				'artist' => $user->artist,
				'title'  => $user->title,
		);

		$id = (int)$user->id;

		if ($id == 0) {
			$this->tableGateway->insert($data);

		} else {
			if ($this->getUserByID($id)) {
				$this->tableGateway->update($data, array('id' => $id));
			} else {
				throw new \Exception('user id does not exist');
			}
		}

	}


	/*
	 *
	 *
	 */

	public function deteleUser($id){



	}
}