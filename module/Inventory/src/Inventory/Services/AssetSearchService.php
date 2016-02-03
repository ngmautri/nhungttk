<?php

namespace Inventory\Services;


use Zend\Permissions\Acl\Acl;
use MLA\Service\AbstractService;
use ZendSearch\Lucene\Lucene;
use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\document\Field;
 /* 
 * @author nmt
 *
 */
class AssetSearchService extends AbstractService
{
	
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \MLA\Service\AbtractService::initAcl()
	 */
	public function initAcl(Acl $acl){
		// TODO
	}
	
	public function createIndex(){
		
		//$index_path = ROOT . DIRECTORY_SEPARATOR . "/module/Inventory/data" . DIRECTORY_SEPARATOR . "index" . DIRECTORY_SEPARATOR . "asset";
		
		//Test
		$index_path = ROOT . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "index" . DIRECTORY_SEPARATOR . "asset";
		
		
		$index = Lucene::create($index_path);
		
		$doc = new Document();
		
		$docUrl = "NMT";
		
		// Store document URL to identify it in the search results
		$doc->addField(Field::Text('url', $docUrl));
		
		
		$docContent = "JUKI";
		
		// Index document contents
		$doc->addField(Field::UnStored('contents', $docContent));
		
		// Add document to the index
		$index->addDocument($doc);		
	}
	
	public function search($q){
		
		
	
		//$index_path = ROOT . DIRECTORY_SEPARATOR . "/module/Inventory/data" . DIRECTORY_SEPARATOR . "index" . DIRECTORY_SEPARATOR . "asset";
	
		//Test
		$index_path = ROOT . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "index" . DIRECTORY_SEPARATOR . "asset";
		
		$index = Lucene::open($index_path);
		
		$hits = $index->find($q);
		
		foreach ($hits as $hit) {
			echo $hit->score;
			var_dump($document = $hit->getDocument());
			
		}
		
	}

}
