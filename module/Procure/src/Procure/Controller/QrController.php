<?php
namespace Procure\Controller;

use Application\Domain\Util\Pagination\Paginator;
use Procure\Application\Reporting\QR\QrReporter;
use Procure\Controller\Contracts\ProcureCRUDController;
use Procure\Infrastructure\Persistence\Filter\QrReportSqlFilter;
use Zend\Http\Headers;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class QrController extends ProcureCRUDController
{

    protected $qrReporter;

    public function __construct()
    {
        $this->setBaseUrl();
        $this->setAjaxLayout();
        $this->setDefaultLayout();
        $this->setListTemplate();
        $this->setViewTemplate();
    }

    protected function setAjaxLayout()
    {
        $this->ajaxLayout = 'layout/user/ajax';
    }

    protected function setBaseUrl()
    {
        $this->baseUrl = '/procure/qr';
    }

    protected function setDefaultLayout()
    {
        $this->defaultLayout = "Procure/layout-fullscreen";
    }

    protected function setViewTemplate()
    {
        $this->viewTemplate = "procure/qr/review";
    }

    protected function setListTemplate()
    {
        $this->listTemplate = $this->getBaseUrl() . '/procure/qr/dto_list';
    }

    public function listAction()
    {
        $is_active = (int) $this->params()->fromQuery('is_active');
        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');
        $currentState = $this->params()->fromQuery('currentState');
        $docStatus = $this->params()->fromQuery('docStatus');
        $file_type = $this->params()->fromQuery('file_type');

        if (is_null($this->params()->fromQuery('perPage'))) {
            $resultsPerPage = 15;
        } else {
            $resultsPerPage = $this->params()->fromQuery('perPage');
        }

        if (is_null($this->params()->fromQuery('page'))) {
            $page = 1;
        } else {
            $page = $this->params()->fromQuery('page');
        }

        $isActive = (int) $this->params()->fromQuery('is_active');

        if ($isActive == null) {
            $isActive = 1;
        }

        if ($docStatus == null) {
            $docStatus = "posted";
        }

        if ($sort_by == null) {
            $sort_by = "sysNumber";
        }

        if ($sort == null) {
            $sort = "DESC";
        }

        $paginator = null;
        $limit = null;
        $offset = null;

        $filter = new QrReportSqlFilter();
        $filter->setIsActive($isActive);
        $filter->setDocStatus($docStatus);

        /**
         *
         * @todo: CACHE
         */
        $total_records = $this->getQrReporter()->getListTotal($filter);

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);

            $limit = $paginator->getLimit();
            $offset = $paginator->getOffset();
        }

        $list = $this->qrReporter->getList($filter, $sort_by, $sort, $limit, $offset, $file_type);

        $viewModel = new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator,
            'is_active' => $is_active,
            'sort_by' => $sort_by,
            'sort' => $sort,
            'per_pape' => $resultsPerPage,
            'currentState' => $currentState,
            'docStatus' => $docStatus
        ));

        $viewModel->setTemplate("procure/qr/dto_list");
        return $viewModel;
    }

    public function getTemplateAction()
    {
        $f = ROOT . '/public/templates/QrInput.xlsx';
        $output = file_get_contents($f);
        $response = $this->getResponse();
        $headers = new Headers();
        $headers->addHeaderLine('Content-Type: xlsx');
        $headers->addHeaderLine('Content-Disposition: attachment; filename="QrInput.xlsx"');
        $headers->addHeaderLine('Content-Description: File Transfer');
        $headers->addHeaderLine('Content-Transfer-Encoding: binary');
        $headers->addHeaderLine('Content-Encoding: UTF-8');
        $response->setHeaders($headers);
        $response->setContent($output);
        return $response;
    }

    /**
     *
     * @return \Procure\Application\Reporting\QR\QrReporter
     */
    public function getQrReporter()
    {
        return $this->qrReporter;
    }

    /**
     *
     * @param QrReporter $qrReporter
     */
    public function setQrReporter(QrReporter $qrReporter)
    {
        $this->qrReporter = $qrReporter;
    }
}
