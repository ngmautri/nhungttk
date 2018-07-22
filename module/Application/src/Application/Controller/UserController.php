<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Doctrine\ORM\EntityManager;
use MLA\Paginator;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class UserController extends AbstractActionController
{

    protected $doctrineEM;

    /*
     * Defaul Action
     */
    public function indexAction()
    {}

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

	

	
}
