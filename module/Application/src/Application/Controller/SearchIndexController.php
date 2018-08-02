<?php


namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Doctrine\ORM\EntityManager;
use MLA\Paginator;
use Inventory\Service\ItemSearchService;
use Inventory\Service\ItemSerialSearchService;
use Procure\Service\PrSearchService;
use PM\Service\ProjectSearchService;
/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class SearchIndexController extends AbstractActionController
{

    protected $doctrineEM;
    protected $itemSearchService;
    protected $itemSerialSearchService;
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
       // $result[] = "ITEM ". $this->itemSearchService->createItemIndex();
        $result[] = "ITEM Serial". $this->itemSearchService->createItemIndex();
        
        $result[] = "PR ". $this->prSearchService->createIndex();
        $result[] = "PROJECT " . $this->projectSearchService->createIndex();
     
        
        // trigger uploadPicture. AbtractController is EventManagerAware.
        $this->getEventManager()->trigger('system.log', __CLASS__, array(
            'priority' => 7,
            'message' => 'Search Indexes updated!'
        ));
        
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
     * 
     *  @return \Doctrine\ORM\EntityManager
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

   /**
    * 
    *  @param EntityManager $doctrineEM
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
    /**
     * @return mixed
     */
    public function getItemSerialSearchService()
    {
        return $this->itemSerialSearchService;
    }

    /**
     * @param mixed $itemSerialSearchService
     */
    public function setItemSerialSearchService(ItemSerialSearchService $itemSerialSearchService)
    {
        $this->itemSerialSearchService = $itemSerialSearchService;
    }





	

	
}
