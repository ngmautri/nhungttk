<?php

namespace Inventory\Services;


use Zend\Permissions\Acl\Acl;
use Zend\EventManager\EventManagerInterface;

use ZendSearch\Lucene\Lucene;
use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\Document\Field;
use ZendSearch\Lucene\Analysis\Analyzer\Common\TextNum\CaseInsensitive;
use ZendSearch\Lucene\Analysis\Analyzer\Analyzer;


use MLA\Service\AbstractService;
use Inventory\Model\MLASparepartTable;


 /* 
 * @author nmt
 *
 */
class SparePartsSearchService extends AbstractService
{
	protected $sparePartTable;
	protected $eventManager = null;
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \MLA\Service\AbtractService::initAcl()
	 */
	public function initAcl(Acl $acl){
		// TODO
	}
	
	public function createIndex(){
		
		$index_path = ROOT . DIRECTORY_SEPARATOR . "/module/Inventory/data" . DIRECTORY_SEPARATOR . "index" . DIRECTORY_SEPARATOR . "spareparts";
		
		//Test
		//$index_path = ROOT . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "index" . DIRECTORY_SEPARATOR . "spareparts";
		
		
		$index = Lucene::create($index_path);
		Analyzer::setDefault(new CaseInsensitive());
		
		$assetTable = $this->getSparepartTable();
		
		$rows = $assetTable->fetchAll();
		
		if(count($rows) > 0 )
		{
			foreach($rows as $row)
			{
				$doc = new Document();
				$doc->addField(Field::Text('name', mb_strtolower($row['name'])));
				$doc->addField(Field::Text('description', mb_strtolower($row['description'])));
				
				$doc->addField(Field::Text('tag', $row['tag']));
				$doc->addField(Field::Keyword('tag1', $row['tag']));
				
				$doc->addField(Field::Text('code', $row['code']));
				$doc->addField(Field::Keyword('code1', $row['code']));
						
				
				$doc->addField(Field::Text('comment', mb_strtolower($row['comment'])));			
				
				$doc->addField(Field::UnIndexed ('sparepart_id',$row['id']));
				
				$index->addDocument($doc);
			}
			return 'Sparepart index is created successfully!';
		}else{
			return 'Nothing for indexing!';
		}
		
	}
	
	public function search($q){
		
		
	
		$index_path = ROOT . DIRECTORY_SEPARATOR . "/module/Inventory/data" . DIRECTORY_SEPARATOR . "index" . DIRECTORY_SEPARATOR . "spareparts";
	
		//Test
		//$index_path = ROOT . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "index" . DIRECTORY_SEPARATOR . "spareparts";
		
		$index = Lucene::open($index_path);
		
		$hits = $index->find($q);
		
		/*
		foreach ($hits as $hit) {
			echo $hit->score;
			echo $hit->tag;
			var_dump($document = $hit->getDocument());
			
		}
		*/
		
		return $hits;
		
	}
	
	
	public function setEventManager(EventManagerInterface $eventManager)
	{
		$eventManager->setIdentifiers(array(__CLASS__));
		$this->eventManager = $eventManager;
	}
	
	public function getEventManager()
	{
		return $this->eventManager;
	}
	
	public function setSparepartTable(MLASparepartTable $sparePartTable)
	{
		$this->sparePartTable =  $sparePartTable;
	}
	
	public function getSparepartTable()
	{
		return $this->sparePartTable;
	}

}
