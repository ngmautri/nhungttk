<?php
namespace Procure\Controller;

use Application\Controller\Contracts\AbstractGenericController;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ReportController extends AbstractGenericController
{

    protected $prReporter;

    protected $qrReporter;

    protected $poReporter;

    protected $grReporter;

    protected $apReporter;

    protected $procureReporter;

    public function indexAction()
    {
        return parent::indexAction();
    }

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
}
