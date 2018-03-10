<?php

namespace WorkflowTest;

use Application\Entity\NmtProcurePr;
use PHPUnit_Framework_TestCase;


class WorkflowTest extends PHPUnit_Framework_TestCase {
 	 protected $tree;
	
 	 /*
 	 public function testArticleCatergoryTest() {
 	 	$list = Bootstrap::getServiceManager()->get('User\Service\ArticleCategory');
 	 	//$list = $this->articleCategoryService;
 	 	$list = $list->initCategory ();
 	 	$list = $list->updateCategory ( 42, 0 );
 	 	//var_dump($list);
 	 	$list = $list->generateJSTreeWithTotalMember ( 42 );
 	 	var_dump($list->getCategories ()[43]);
 	 
 	 }
 	 */
	
 	public function testWorflowTable() {
		//$tbl = Bootstrap::getServiceManager ()->get ( 'Workflow\Model\WorkflowNodeTable' );
		$service = Bootstrap::getServiceManager ()->get ( 'Workflow\Service\WorkflowService' );
		//$service->init();
		//$service->updateCategory(1,0);
		
		$p = new NmtProcurePr();
		$p->setPrNumber("Purchase Request");
		$p->setCurrentState("bought");
		$wf= $service->purchaseRequestWF()->get($p);
		//var_dump($wf->getEnabledTransitions($p));
		$wf->apply($p, 'delivery');
		//echo "Current State: " . $p->getCurrentState();
		
 	}	
	
}

