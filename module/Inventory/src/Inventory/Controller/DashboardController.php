<?php
namespace Inventory\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;
use Zend\View\Model\ViewModel;
use Inventory\Infrastructure\Persistence\DoctrineItemListRepository;
use Inventory\Infrastructure\Persistence\DoctrineItemReportingRepository;
use MLA\Paginator;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DashboardController extends AbstractActionController
{

    const CHAR_LIST = "__0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";

    protected $doctrineEM;
    protected $itemListRepository;
    protected $itemReportingRepository;
    

    /**
     *
     * {@inheritdoc}
     *
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function indexAction()
    {
        $request = $this->getRequest();
        
        if ($request->getHeader('Referer') == null) {
           return $this->redirect ()->toRoute ( 'access_denied' );
        } 
        
        return new ViewModel ( array (
            
        ) );
        
    }
    
    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     *
     */
    public function lastReceivedItemsAction()
    {
        $request = $this->getRequest();
        
        // accepted only ajax request
        
        if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        $this->layout("layout/user/ajax");
        
        if (is_null($this->params()->fromQuery('perPage'))) {
            $resultsPerPage = 5;
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
        
        $res = $this->itemReportingRepository;
        $list = $res->getLastAPRows(45);
        
        $total_records = count($list);
        
        $paginator = null;
        
        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            // $list = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryLog')->findBy($criteria, $sort_criteria, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
            $list = $res->getLastAPRows(($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
        }
        
        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator
        ));
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     *
     */
    public function lastOrderedItemsAction()
    {
        $request = $this->getRequest();
        
        // accepted only ajax request
        
        if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        $this->layout("layout/user/ajax");
        
        if (is_null($this->params()->fromQuery('perPage'))) {
            $resultsPerPage = 5;
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
        
        $res = $this->itemReportingRepository;
        $list = $res->getLastCreatedPrRow(45);
        
        $total_records = count($list);
        
        $paginator = null;
        
        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            // $list = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryLog')->findBy($criteria, $sort_criteria, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
            $list = $res->getLastCreatedPrRow(($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
        }
        
        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator
        ));
    }
    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     *
     */
    public function mostOrderItemsAction()
    {
        $request = $this->getRequest();
        
        // accepted only ajax request
        
        if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        $this->layout("layout/user/ajax");
        
        if (is_null($this->params()->fromQuery('perPage'))) {
            $resultsPerPage = 12;
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
        
        $res = $this->itemReportingRepository;
        $mostOrderItems = $res->getMostOrderItems(108);
        
        $total_records = count($mostOrderItems);
        
        $paginator = null;
        
        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            // $list = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryLog')->findBy($criteria, $sort_criteria, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
            $mostOrderItems = $res->getMostOrderItems(($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
        }
        
        return new ViewModel(array(
            'mostOrderItems' => $mostOrderItems,
            'total_records' => $total_records,
            'paginator' => $paginator
        ));
    }
    
    /**
     * 
     *  @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     *
     */
    public function lastCreatedItemsAction()
    {
        $request = $this->getRequest();
        
        // accepted only ajax request
        
        if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        $this->layout("layout/user/ajax");
        
        if (is_null($this->params()->fromQuery('perPage'))) {
            $resultsPerPage = 5;
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
        
        $res = $this->itemReportingRepository;
        $list = $res->getLastCreatedItems(45);
        
        $total_records = count($list);
        
        $paginator = null;
        
        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            // $list = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryLog')->findBy($criteria, $sort_criteria, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
            $list = $res->getLastCreatedItems(($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
        }
        
        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator
        ));
    }
    
    /**
     *
     *  @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     *
     */
    public function mostValueItemsAction()
    {
        $request = $this->getRequest();
        
        // accepted only ajax request
        
       /*  if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        } */
        
        $this->layout("layout/user/ajax");
        
        if (is_null($this->params()->fromQuery('perPage'))) {
            $resultsPerPage = 15;
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
        
        $res = $this->itemReportingRepository;
        $list = $res->getMostValueItems(8200,135,0);
        
        $total_records = count($list);
        
        $paginator = null;
        
        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            // $list = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryLog')->findBy($criteria, $sort_criteria, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
            $list = $res->getMostValueItems(8200,($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
        }
        
        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator
        ));
    }
    
    /**
     *
     *  @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     *
     */
    public function randomItemAction()
    {
        $request = $this->getRequest();
        
        // accepted only ajax request
        
       /*  if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        } */
        
        $this->layout("layout/user/ajax");
        
    
        $res = $this->itemReportingRepository;
        $entity = $res->getRandomItem();
         
        return new ViewModel(array(
            'entity' => $entity,
         ));
    }

    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
        return $this;
    }
    
    /**
     *
     * @return \Inventory\Infrastructure\Persistence\DoctrineItemListRepository
     */
    public function getItemListRepository()
    {
        return $this->itemListRepository;
    }
    
    /**
     *
     * @param DoctrineItemListRepository $itemListRepository
     */
    public function setItemListRepository(DoctrineItemListRepository $itemListRepository)
    {
        $this->itemListRepository = $itemListRepository;
    }
    
    /**
     *
     * @return \Inventory\Infrastructure\Persistence\DoctrineItemReportingRepository
     */
    public function getItemReportingRepository()
    {
        return $this->itemReportingRepository;
    }
    
    /**
     *
     * @param DoctrineItemReportingRepository $itemReportingRepository
     */
    public function setItemReportingRepository(DoctrineItemReportingRepository $itemReportingRepository)
    {
        $this->itemReportingRepository = $itemReportingRepository;
    }
    
}
