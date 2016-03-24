<?php

namespace Inventory\Services;


use Zend\Permissions\Acl\Acl;
use Zend\EventManager\EventManagerInterface;

use ZendSearch\Lucene\Lucene;
use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\Document\Field;
use ZendSearch\Lucene\Analysis\Analyzer\Common\TextNum\CaseInsensitive;
use ZendSearch\Lucene\Analysis\Analyzer\Analyzer;

use Inventory\Model\MLAAssetTable;

use MLA\Service\AbstractService;


 /* 
 * @author nmt
 *
 */
class AssetSearchService extends AbstractService
{
	protected $assetTable;
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
		
		$index_path = ROOT . DIRECTORY_SEPARATOR . "/module/Inventory/data" . DIRECTORY_SEPARATOR . "index" . DIRECTORY_SEPARATOR . "asset";
		
		//Test
		//$index_path = ROOT . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "index" . DIRECTORY_SEPARATOR . "asset";
		
		
		$index = Lucene::create($index_path);
		Analyzer::setDefault(new CaseInsensitive());
		
		$assetTable = $this->getAssetTable();
		
		$rows = $assetTable->fetchAll();
		
		if(count($rows) > 0 )
		{
			foreach($rows as $row)
			{
				$doc = new Document();
				
				$doc->addField(Field::Text('name', mb_strtolower($row->name)));
				$doc->addField(Field::Text('description', mb_strtolower($row->description)));
				
				$doc->addField(Field::Text('tag', $row->tag));
				$doc->addField(Field::Keyword('tag1', $row->tag));
				
				$doc->addField(Field::Text('model', mb_strtolower($row->model)));
				$doc->addField(Field::Keyword('model1', $row->model));
				
				$doc->addField(Field::Text('brand', mb_strtolower($row->brand)));
				$doc->addField(Field::Keyword('brand1', $row->brand));
				
				$doc->addField(Field::Text('serial', $row->serial));
				$doc->addField(Field::Keyword('serial1', $row->serial));
				
				$doc->addField(Field::Text('origin', mb_strtolower($row->origin)));
				$doc->addField(Field::Keyword('origin1', $row->origin));
				
				$doc->addField(Field::Keyword('catetory_id', $row->category_id));
				
												
				$doc->addField(Field::Text('location', mb_strtolower($row->location)));
				$doc->addField(Field::Text('comment', mb_strtolower($row->comment)));			
				
				$doc->addField(Field::UnIndexed ('asset_id',$row->id));
				
				$index->addDocument($doc);
			}
			return 'Asset index is created successfully!';
		}else{
			return 'Nothing for indexing!';
		}
		
	}
	
	public function search($q){
		
		
	
		$index_path = ROOT . DIRECTORY_SEPARATOR . "/module/Inventory/data" . DIRECTORY_SEPARATOR . "index" . DIRECTORY_SEPARATOR . "asset";
	
		//Test
		//$index_path = ROOT . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "index" . DIRECTORY_SEPARATOR . "asset";
		
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
	
	public function setAssetTable(MLAAssetTable $assetTable)
	{
		$this->assetTable =  $assetTable;
	}
	
	public function getAssetTable()
	{
		return $this->assetTable;
	}

}
