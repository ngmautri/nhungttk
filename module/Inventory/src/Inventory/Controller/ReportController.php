<?php
namespace Inventory\Controller;

use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;
use Inventory\Service\ItemSearchService;

/**
 * Goods Issue
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ReportController extends AbstractActionController
{

    protected $doctrineEM;

    protected $itemReportService;
 
    protected $itemSearchService;
    
    

    /**
     * {@inheritDoc}
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function indexAction()
    {
         return parent::indexAction();
    }

    /**
     * 
     * @return \Inventory\Service\Report\ItemReportService
     */
    public function getItemReportService()
    {
        return $this->itemReportService;
    }

    /**
     * 
     * @param \Inventory\Service\Report\ItemReportService $itemReportService
     */
    public function setItemReportService(\Inventory\Service\Report\ItemReportService $itemReportService)
    {
        $this->itemReportService = $itemReportService;
    }

    /**
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }
    
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
        return $this;
    }
    
    public function getItemSearchService()
    {
        return $this->itemSearchService;
    }
    
    public function setItemSearchService(ItemSearchService $itemSearchService)
    {
        $this->itemSearchService = $itemSearchService;
        return $this;
    }
}
