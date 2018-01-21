<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Doctrine\ORM\EntityManager;
use MLA\Paginator;
use Inventory\Service\ItemSearchService;
use Procure\Service\PrSearchService;
use PM\Service\ProjectSearchService;
/**
 * 
 * @author nmt
 *
 */
class SearchIndexController extends AbstractActionController
{

    protected $doctrineEM;
    protected $itemSearchService;
    protected $prSearchService;
    protected $projectSearchService;

    /*
     * Defaul Action
     */
    public function indexAction()
    {}
    
    /**
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function updateAllAction()
    {
        $result = array();
        $result[] = "ITEM ". $this->itemSearchService->optimizeIndex();
        $result[] = "PR ". $this->prSearchService->optimizeIndex();
        $result[] = "PROJECT " . $this->projectSearchService->optimizeIndex();
        
        return new ViewModel(array(
            "result"=>$result
        ));
        
    }

    /**
     * 
     */
    public function listAction()
    {
        if (is_null($this->params()->fromQuery('perPage'))) {
            $resultsPerPage = 18;
        } else {
            $resultsPerPage = $this->params()->fromQuery('perPage');
        }
        ;
        
        if (is_null($this->params()->fromQuery('page'))) {
            $page = 1;
        } else {
            $page = $this->params()->fromQuery('page');
        }
        ;
        
        $criteria = array(
         );
        $sort = array(
            'firstname' => 'ASC',
         );
        
        $resources = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findBy($criteria, $sort);
        $totalResults = count($resources);
        $paginator = null;
        
        if ($totalResults > $resultsPerPage) {
            $paginator = new Paginator($totalResults, $page, $resultsPerPage);
            $resources = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findBy($criteria, $sort, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
        }
        
        return new ViewModel(array(
            'total_resources' => $totalResults,
	        'resources' => $resources,
	        'paginator' => $paginator
	    ));
	}
	
    /**
     * @return the $doctrineEM
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     * @param field_type $doctrineEM
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
    }
    /**
     * @return mixed
     */
    public function getItemSearchService()
    {
        return $this->itemSearchService;
    }

    /**
     * @param mixed $itemSearchService
     */
    public function setItemSearchService(ItemSearchService $itemSearchService)
    {
        $this->itemSearchService = $itemSearchService;
    }
    /**
     * @return mixed
     */
    public function getPrSearchService()
    {
        return $this->prSearchService;
    }

    /**
     * @param mixed $prSearchService
     */
    public function setPrSearchService(PrSearchService $prSearchService)
    {
        $this->prSearchService = $prSearchService;
    }
    /**
     * @return mixed
     */
    public function getProjectSearchService()
    {
        return $this->projectSearchService;
    }

    /**
     * @param mixed $projectSearchService
     */
    public function setProjectSearchService(ProjectSearchService $projectSearchService)
    {
        $this->projectSearchService = $projectSearchService;
    }




	

	
}