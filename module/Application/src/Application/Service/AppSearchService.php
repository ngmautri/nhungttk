<?php

namespace Application\Service;

use ZendSearch\Lucene\Lucene;
use ZendSearch\Lucene\Analysis\Analyzer\Analyzer;
use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\Document\Field;
use Doctrine\ORM\EntityManager;
use ZendSearch\Lucene\Analysis\Analyzer\Common\Text\CaseInsensitive;
use ZendSearch\Lucene\Index\Term;
use ZendSearch\Lucene\Search\Query\MultiTerm;
use ZendSearch\Lucene\Search\Query\Wildcard;

/**
 *
 * @author nmt
 *        
 */
class AppSearchService {
	
	const RESOURCES_INDEX = "/data/application/indexes/resources";
	
	protected $doctrineEM;
	
	/**
	 */
	function test() {
	}
	
	/**
	 *
	 * @return string
	 */
	public function createResourcesIndexes() {
		$index = Lucene::create (getcwd (). self::RESOURCES_INDEX );
		Analyzer::setDefault ( new CaseInsensitive () );
		
		$records = $this->doctrineEM->getRepository ( 'Application\Entity\NmtApplicationAclResource' )->findAll ();
		
		if (count ( $records ) > 0) {
			foreach ( $records as $row ) {
				$doc = new Document ();
				$doc->addField ( Field::UnIndexed ( 'resource_id', $row->getId () ) );
				$doc->addField ( Field::Keyword ( 'module', $row->getModule () ) );
				$doc->addField ( Field::Keyword ( 'controller', $row->getController () ) );
				$doc->addField ( Field::Keyword ( 'action', $row->getAction () ) );
				$doc->addField ( Field::Keyword ( 'resource', $row->getResource () ) );
				$doc->addField ( Field::Text ( 'module_1', mb_strtolower ( $row->getModule () ) ) );
				$doc->addField ( Field::Text ( 'controller_1', mb_strtolower ( $row->getController () ) ) );
				$doc->addField ( Field::Text ( 'action_1', mb_strtolower ( $row->getAction () ) ) );
				$doc->addField ( Field::Keyword ( 'resource_1', mb_strtolower ( $row->getResource () ) ) );
				$index->addDocument ( $doc );
			}
			return 'application resource indexes is created successfully!';
		} else {
			return 'Nothing for indexing!';
		}
	}
	
	/**
	 *
	 * @param unknown $q        	
	 * @param unknown $department_id        	
	 */
	public function searchResource($q) {
		$index = Lucene::open ( getcwd ().self::RESOURCES_INDEX );
		
		// Lucene::setDefaultSearchField('module_1');
		/*
		 * if ($department_id > 0) :
		 * $query = new Boolean ();
		 * if ($q instanceof Wildcard) {
		 * $query->addSubquery ( $q, true );
		 * } else {
		 * $subquery = new MultiTerm ();
		 * $subquery->addTerm ( new Term ( $q ) );
		 * $query->addSubquery ( $subquery, true );
		 * }
		 *
		 * $subquery1 = new MultiTerm ();
		 * $subquery1->addTerm ( new Term ( $department_id, 'department_id' ) );
		 *
		 * $query->addSubquery ( $subquery1, true );
		 *
		 * // var_dump($query);
		 * $hits = $index->find ( $query );
		 * return $hits;
		 * endif;
		 */
		try {
			if (strpos ( $q, '*' ) != false) {
				$pattern = new Term ( $q );
				$query = new Wildcard ( $pattern );
				$hits = $index->find ( $query );
			} else {
				$hits = $index->find ( $q );
			}
			
			$hits = $index->find ( $q );
			
			$result = [ 
					"message" => count ( $hits ) . " Result(s) found for Query: <b>\"" . $q . "\"</b>",
					"hits" => $hits 
			];
			
			return $result;
		} catch ( \Exception $e ) {
			$result = [ 
					"message"=>'Query: <b>"'. $q . '"</b> sent , but exception catched: <b>'.  $e->getMessage(). "</b>\n",
					"hits"=>null,
			];
			return $result;
			
		}
	}
	
	
	public function getDoctrineEM() {
		return $this->doctrineEM;
	}
	public function setDoctrineEM(EntityManager $doctrineEM) {
		$this->doctrineEM = $doctrineEM;
		return $this;
	}
}