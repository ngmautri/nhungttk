<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Inventory\Controller;

// use Zend\I18n\Validator\Int;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Date;
use Zend\Validator\EmailAddress;
use Zend\Mail\Message;
use Zend\View\Model\ViewModel;
use Zend\Http\Headers;
use Application\Domain\Util\Pagination\Paginator;
use MLA\Files;
use Inventory\Model\SparepartPicture;
use Inventory\Model\SparepartPictureTable;
use Inventory\Model\MLASparepart;
use Inventory\Model\MLASparepartTable;
use Inventory\Model\SparepartCategoryMember;
use Inventory\Model\SparepartCategoryMemberTable;
use Inventory\Model\SparepartCategoryTable;
use Inventory\Model\SparepartMovement;
use Inventory\Model\SparepartMovementsTable;
use Inventory\Services\SparepartService;

class ReportController extends AbstractActionController
{

    protected $authService;

    protected $SmtpTransportService;

    protected $sparePartService;

    protected $userTable;

    protected $sparePartTable;

    protected $sparePartPictureTable;

    protected $sparepartMovementsTable;

    protected $sparePartCategoryTable;

    protected $sparePartCategoryMemberTable;

    protected $massage = 'NULL';

    /*
     * Defaul Action
     */
    public function indexAction()
    {

        // $redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
        $fromDate = $this->params()->fromQuery('start_date');
        $toDate = $this->params()->fromQuery('end_date');
        $flow = $this->params()->fromQuery('flow');
        $output = $this->params()->fromQuery('output');

        $movements = $this->sparepartMovementsTable->getSparePartMovements($fromDate, $toDate, $flow, 0, 0);

        if ($output === 'csv') {

            $fh = fopen('php://memory', 'w');
            // $myfile = fopen('ouptut.csv', 'a+');

            $h = array();
            $h[] = "DATE";
            $h[] = "TAG";
            $h[] = "NAME";
            $h[] = "QUANTITY";
            $h[] = "FLOW";

            $delimiter = ";";

            fputcsv($fh, $h, $delimiter, '"');
            // fputs($fh, implode($h, ',')."\n");

            foreach ($movements as $m) {
                $l = array();
                $l[] = (string) $m->movement_date;
                $l[] = (string) '"' . $m->tag . '"';

                $name = (string) $m->sparepart_name;

                $name === '' ? $name = "-" : $name;

                $name = str_replace(',', '', $name);
                $name = str_replace(';', '', $name);

                $l[] = $name;
                $l[] = (string) $m->quantity;
                $l[] = (string) $m->flow;

                fputcsv($fh, $l, $delimiter, '"');
                // fputs($fh, implode($l, ',')."\n");
            }

            $fileName = 'report-' . date("m-d-Y") . '-' . date("h:i:sa") . '.csv';
            fseek($fh, 0);
            $output = stream_get_contents($fh);
            // file_put_contents($fileName, $output);

            $response = $this->getResponse();
            $headers = new Headers();

            $headers->addHeaderLine('Content-Type: text/csv');
            // $headers->addHeaderLine ( 'Content-Type: application/vnd.ms-excel; charset=UTF-8' );

            $headers->addHeaderLine('Content-Disposition: attachment; filename="' . $fileName . '"');
            $headers->addHeaderLine('Content-Description: File Transfer');
            $headers->addHeaderLine('Content-Transfer-Encoding: binary');
            $headers->addHeaderLine('Content-Encoding: UTF-8');

            // $response->setHeaders(Headers::fromString("Content-Type: application/octet-stream\r\nContent-Length: 9\r\nContent-Disposition: attachment; filename=\"blamoo.txt\""));
            $response->setHeaders($headers);
            $response->setContent($output);

            fclose($fh);
            // unlink($fileName);

            return $response;
        } else {

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

            $totalResults = count($movements);

            $paginator = null;
            if ($totalResults > $resultsPerPage) {
                $paginator = new Paginator($totalResults, $page, $resultsPerPage);
                $movements = $this->sparepartMovementsTable->getSparePartMovements($fromDate, $toDate, $flow, ($paginator->getMaxInPage() - $paginator->getMinInPage()) + 1, $paginator->getMinInPage() - 1);
            }

            return new ViewModel(array(
                'movements' => $movements,
                // 'redirectUrl' => $redirectUrl,
                'fromDate' => $fromDate,
                'toDate' => $toDate,
                'flow' => $flow,
                'paginator' => $paginator,
                'total_items' => $totalResults
            ));
        }
    }

    // SETTER AND GETTER
    public function getAuthService()
    {
        return $this->authService;
    }

    public function setAuthService($authService)
    {
        $this->authService = $authService;
        return $this;
    }

    public function setSmtpTransportService($SmtpTransportService)
    {
        $this->SmtpTransportService = $SmtpTransportService;
        return $this;
    }

    public function setSparePartService(SparepartService $sparePartService)
    {
        $this->sparePartService = $sparePartService;
        return $this;
    }

    public function getUserTable()
    {
        return $this->userTable;
    }

    public function setUserTable($userTable)
    {
        $this->userTable = $userTable;
        return $this;
    }

    public function getSparePartTable()
    {
        return $this->sparePartTable;
    }

    public function setSparePartTable(MLASparepartTable $sparePartTable)
    {
        $this->sparePartTable = $sparePartTable;
        return $this;
    }

    public function setSparepartMovementsTable(SparepartMovementsTable $sparepartMovementsTable)
    {
        $this->sparepartMovementsTable = $sparepartMovementsTable;
        return $this;
    }

    public function setSparePartCategoryTable(SparepartCategoryTable $sparePartCategoryTable)
    {
        $this->sparePartCategoryTable = $sparePartCategoryTable;
        return $this;
    }

    public function setSparePartPictureTable(SparepartPictureTable $sparePartPictureTable)
    {
        $this->sparePartPictureTable = $sparePartPictureTable;
        return $this;
    }

    public function setSparePartCategoryMemberTable(SparepartCategoryMemberTable $sparePartCategoryMemberTable)
    {
        $this->sparePartCategoryMemberTable = $sparePartCategoryMemberTable;
        return $this;
    }

    public function getSparePartService()
    {
        return $this->sparePartService;
    }

    public function getSmtpTransportService()
    {
        return $this->SmtpTransportService;
    }

    public function getSparePartPictureTable()
    {
        return $this->sparePartPictureTable;
    }

    public function getSparepartMovementsTable()
    {
        return $this->sparepartMovementsTable;
    }

    public function getSparePartCategoryTable()
    {
        return $this->sparePartCategoryTable;
    }

    public function getSparePartCategoryMemberTable()
    {
        return $this->sparePartCategoryMemberTable;
    }
}
