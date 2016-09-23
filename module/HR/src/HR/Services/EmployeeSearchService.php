<?php

namespace HR\Services;

use Zend\Permissions\Acl\Acl;
use Zend\EventManager\EventManagerInterface;
use ZendSearch\Lucene\Lucene;
use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\Document\Field;
use ZendSearch\Lucene\Analysis\Analyzer\Common\TextNum\CaseInsensitive;
use ZendSearch\Lucene\Analysis\Analyzer\Analyzer;
use ZendSearch\Lucene\Search\Query\Boolean;
use ZendSearch\Lucene\Search\Query\MultiTerm;
use ZendSearch\Lucene\Search\Query\Wildcard;

use MLA\Service\AbstractService;

use ZendSearch\Lucene\Index\Term;
use HR\Model\VendorTable;
use \Exception;
use HR\Model\Employee;
use HR\Model\EmployeeTable;
use HR\Model\EmployeePicture;
use HR\Model\EmployeePictureTable;
/*
 * @author nmt
 *
 */
class EmployeeSearchService extends AbstractService {
	
	protected $employeeTable;
	protected $employeePictureTable;
	protected $employeeService;
	
	protected $eventManager = null;
	
	//Production
	private $index_path = "module/HR/data/index/employee";
	
	//Test
	//private $index_path = "data/index/employee";
	
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \MLA\Service\AbtractService::initAcl()
	 */
	public function initAcl(Acl $acl) {
		// TODO
	}
	/**
	 */
	public function createIndex() {
		
		$index = Lucene::create ( ROOT . DIRECTORY_SEPARATOR . $this->index_path );
		Analyzer::setDefault ( new CaseInsensitive () );
		
		$rows = $this->employeeTable->fetchAll();
		echo count ( $rows );
		
		if (count ( $rows ) > 0) {
			foreach ( $rows as $row ) {
				$doc = new Document ();
				$doc->addField ( Field::UnIndexed ( 'employee_id', $row->id ) );
				
				$doc->addField(Field::Text('employee_code', $row->employee_code));
				$doc->addField(Field::Keyword('employee_code', $row->employee_code));
				
				$doc->addField(Field::Text('employee_code1', $row->employee_code*1));
				
				
				$doc->addField ( Field::Text ( 'employee_name', mb_strtolower ( $row->employee_name ) ) );
				$doc->addField ( Field::Text ( 'employee_name_local', mb_strtolower ( $row->employee_name_local ) ) );
				$doc->addField ( Field::Text ( 'employee_dob', mb_strtolower ( $row->employee_dob ) ) );
				
				$index->addDocument ( $doc );
			}
			return 'Employee index is created successfully!';
		} else {
			return 'Nothing for indexing!';
		}
	}
	
	/**
	 * 
	 * @param unknown $row
	 * @return string
	 */
	public function updateIndex($row) {
		
		$index = Lucene::open ( ROOT . DIRECTORY_SEPARATOR . $this->index_path);
		Analyzer::setDefault ( new CaseInsensitive () );
		
		$doc = new Document ();
		$doc->addField ( Field::UnIndexed ( 'article_id', $row->id ) );
		$doc->addField ( Field::Keyword ( 'department_id', $row->department_id ) );
		$doc->addField ( Field::UnIndexed ( 'unit', $row->unit ) );
		
		
		$doc->addField ( Field::Text ( 'name', mb_strtolower ( $row->name ) ) );
		$doc->addField ( Field::Text ( 'description', mb_strtolower ( $row->description ) ) );
		$doc->addField ( Field::Text ( 'keywords', mb_strtolower ( $row->keywords ) ) );
		$doc->addField ( Field::Keyword ( 'type', mb_strtolower ( $row->type ) ) );
		$doc->addField ( Field::Keyword ( 'code', mb_strtolower ( $row->code ) ) );
		$doc->addField ( Field::Keyword ( 'barcode', mb_strtolower ( $row->barcode ) ) );
		$doc->addField ( Field::Keyword ( 'created_by', mb_strtolower ( $row->created_by ) ) );
		$doc->addField ( Field::Text ( 'remarks', mb_strtolower ( $row->remarks ) ) );
		$index->addDocument ( $doc );
		return 'Article index is created successfully!';
	}
	
	/**
	 * need to you 
	 * use \Exception;
	 * 
	 * @param unknown $q
	 * @return \ZendSearch\Lucene\Search\QueryHit|NULL
	 */
	public function search($q) {
			$index = Lucene::open (  ROOT . DIRECTORY_SEPARATOR . $this->index_path );
			//Lucene::setDefaultSearchField('name');
			$hits = $index->find ($q);
			return $hits;
	}
	
	
	
	public function setEventManager(EventManagerInterface $eventManager) {
		$eventManager->setIdentifiers ( array (
				__CLASS__ 
		) );
		$this->eventManager = $eventManager;
	}
	public function getEventManager() {
		return $this->eventManager;
	}
	public function getVendorTable() {
		return $this->vendorTable;
	}
	public function setVendorTable(VendorTable $vendorTable) {
		$this->vendorTable = $vendorTable;
		return $this;
	}
	public function getEmployeeTable() {
		return $this->employeeTable;
	}
	public function setEmployeeTable(EmployeeTable $employeeTable) {
		$this->employeeTable = $employeeTable;
		return $this;
	}
	public function getEmployeePictureTable() {
		return $this->employeePictureTable;
	}
	public function setEmployeePictureTable($employeePictureTable) {
		$this->employeePictureTable = $employeePictureTable;
		return $this;
	}
	public function getEmployeeService() {
		return $this->employeeService;
	}
	public function setEmployeeService($employeeService) {
		$this->employeeService = $employeeService;
		return $this;
	}
	
	

	
	
}
