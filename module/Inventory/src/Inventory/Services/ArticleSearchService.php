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
use Inventory\Model\ArticleTable;

/*
 * @author nmt
 *
 */
class ArticleSearchService extends AbstractService {
	protected $articleTable;
	protected $eventManager = null;
	
	//Production
	private $index_path = "module/Inventory/data/index/articles";
	
	//Test
	//private $index_path = "data/index/articles";
	
	
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
		
		$rows = $this->articleTable->getArticles ( 0, 0, 0 );
		
		if (count ( $rows ) > 0) {
			foreach ( $rows as $row ) {
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
			}
			return 'Article index is created successfully!';
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
	 * 
	 * @param unknown $q
	 * @param unknown $department_id
	 */
	public function search($q,$department_id) {
			
		$index = Lucene::open (  ROOT . DIRECTORY_SEPARATOR . $this->index_path );
		
		if($department_id >0):
			$q = $q. ' AND department_id:'.$department_id;
		endif;
		
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
	public function getArticleTable() {
		return $this->articleTable;
	}
	public function setArticleTable(ArticleTable $articleTable) {
		$this->articleTable = $articleTable;
		return $this;
	}
	
	
	
}
