<?php
namespace Procure\Controller;

use Doctrine\ORM\EntityManager;
use Monolog\Logger;
use Zend\Mvc\Controller\AbstractActionController;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ReportController extends AbstractActionController
{

    protected $doctrineEM;

    protected $prReporter;

    protected $qrReporter;

    protected $poReporter;

    protected $grReporter;

    protected $apReporter;

    protected $procureReporter;

    protected $logger;

    public function priceOfItemAction()
    {}

    public function prOfItemAction()
    {}

    public function qrOfItemAction()
    {}

    public function poOfItemAction()
    {}

    public function apOfItemAction()
    {}

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
    }

    /**
     *
     * @return \Monolog\Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     *
     * @param Logger $logger
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }
}
