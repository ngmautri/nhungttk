<?php
namespace Procure\Controller;

use Application\Application\Service\Search\Contracts\SearchResult;
use Application\Controller\Contracts\AbstractGenericController;
use Procure\Application\Service\Search\ZendSearch\PR\Filter\PrQueryFilter;
use Procure\Application\Service\Search\ZendSearch\QR\QrSearchIndexImpl;
use Procure\Application\Service\Search\ZendSearch\QR\Filter\QrQueryFilter;
use Procure\Domain\Contracts\ProcureDocStatus;
use Procure\Domain\QuotationRequest\QRRowSnapshotAssembler;
use Procure\Domain\Service\Search\QrSearchQueryInterface;
use Procure\Infrastructure\Persistence\Doctrine\QrReportRepositoryImpl;
use Procure\Infrastructure\Persistence\Filter\QrReportSqlFilter;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class QrSearchController extends AbstractGenericController
{

    protected $queryService;

    /**
     *
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function autocompleteAction()
    {
        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        /* retrieve the search term that autocomplete sends */
        $q = trim(strip_tags($_GET['term']));

        $vendorId = 0;

        if (isset($_REQUEST['vendor_id'])) {
            $vendorId = (int) $_REQUEST['vendor_id'];
        }

        $a_json = array();
        $a_json_row = array();
        $n = 0;

        $results = null;

        if ($q !== "") {
            $queryFilter = new QrQueryFilter();

            $queryFilter->setDocStatus(ProcureDocStatus::POSTED);
            if ($vendorId > 0) {
                $queryFilter->setVendorId($vendorId);
            }

            $results = $this->queryService->search($q, $queryFilter);
        }

        if ($results instanceof SearchResult) {}

        $results_array = [];
        $hits_array = [];
        foreach ($results->getHits() as $hit) {
            $n ++;
            if ($n > 10) {
                break;
            }

            $hits_array["n"] = \sprintf('%s/%s found.', $n, $results->getTotalHits());

            if ($n == 10) {
                $hits_array["n"] = \sprintf('%s/%s found. There are %s hits more...', $n, $results->getTotalHits(), $results->getTotalHits() - $n);
            }

            $hits_array["value"] = \sprintf('%s | %s', $hit->docNumber, $hit->itemName);

            $item_thumbnail = '/images/no-pic1.jpg';
            if ($hit->itemId != null) {
                $item_thumbnail = $nmtPlugin->getItemPic($hit->itemId);
            }
            $hits_array["item_thumbnail"] = $item_thumbnail;
            $hits_array["hit"] = QRRowSnapshotAssembler::createFromQueryHit($hit);

            $results_array[] = $hits_array;
        }

        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $response->setContent(json_encode($results_array));
        return $response;
    }

    /**
     *
     * @return \Procure\Domain\Service\Search\QrSearchQueryInterface
     */
    public function getQueryService()
    {
        return $this->queryService;
    }

    /**
     *
     * @param QrSearchQueryInterface $queryService
     */
    public function setQueryService(QrSearchQueryInterface $queryService)
    {
        $this->queryService = $queryService;
    }

    /*
     * Defaul Action
     */
    public function indexAction()
    {
        return new ViewModel(array());
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function doAction()
    {
        $q = $this->params()->fromQuery('q');
        $q = trim(strip_tags($q));

        $queryFilter = new PrQueryFilter();
        $results = $this->getQueryService()->search($q, $queryFilter);

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        $viewTemplete = "procure/qr-search/search-result";
        $viewModel = new ViewModel(array(
            'results' => $results,
            'nmtPlugin' => $nmtPlugin
        ));
        $viewModel->setTemplate($viewTemplete);
        return $viewModel;
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function do1Action()
    {
        $request = $this->getRequest();

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        // accepted only ajax request

        if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }

        $this->layout("layout/user/ajax");

        $q = $this->params()->fromQuery('q');
        $context = $this->params()->fromQuery('context');
        $target_id = $this->params()->fromQuery('target_id');
        $target_name = $this->params()->fromQuery('target_name');

        $results = [
            "message" => "",
            "hits" => null,
            'context' => $context,
            'target_id' => $target_id,
            'target_name' => $target_name,
            'nmtPlugin' => $nmtPlugin
        ];

        if ($q !== null) {
            if ($q !== "") {
                $results = $this->prSearchService->search($q);
            }
        }

        // var_dump($results);
        return new ViewModel(array(
            'message' => $results["message"],
            'hits' => $results["hits"],
            'context' => $context,
            'target_id' => $target_id,
            'target_name' => $target_name,
            'nmtPlugin' => $nmtPlugin
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function createIndexAction()
    {
        ini_set('memory_limit', '2048M');

        $rep = new QrReportRepositoryImpl($this->getDoctrineEM());
        $sort_by = Null;
        $sort = null;
        $limit = null;
        $offset = null;
        $filter = new QrReportSqlFilter();
        $filter->setIsActive(1);
        $results = $rep->getAllRow($filter, $sort_by, $sort, $limit, $offset);
        $indexer = new QrSearchIndexImpl();

        $r = $indexer->createIndex($results);
        $r = $indexer->optimizeIndex();

        return new ViewModel(array(
            'result' => $r
        ));
    }

    // =====================
    // @Deprecated.
    // ======================

    /**
     *
     * @deprecated
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function autocompleteAction1()
    {
        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        /* retrieve the search term that autocomplete sends */
        $q = trim(strip_tags($_GET['term']));
        // $q = $this->params ()->fromQuery ( 'q' );

        $a_json = array();
        $a_json_row = array();
        $n = 0;
        if ($q !== "") {
            $results = $this->prSearchService->search($q);

            if (count($results) > 0) {
                foreach ($results['hits'] as $a) {

                    $n ++;
                    $a_json_row["n"] = $n;

                    $a_json_row["value"] = $a->pr_number . ' | ' . $a->item_name;

                    $a_json_row["pr_id"] = $a->pr_id;
                    $a_json_row["pr_token"] = $a->pr_token;
                    $a_json_row["pr_checksum"] = $a->pr_checksum;
                    $a_json_row["pr_number"] = $a->pr_number;
                    $a_json_row["pr_row_id"] = $a->pr_row_id;
                    $a_json_row["token"] = $a->token;
                    $a_json_row["checksum"] = $a->checksum;

                    $a_json_row["item_id"] = $a->item_id;
                    $a_json_row["item_token"] = $a->item_token;
                    $a_json_row["item_checksum"] = $a->item_checksum;
                    $a_json_row["item_name"] = $a->item_name;
                    $a_json_row["inventory_account_id"] = $a->inventory_account_id;
                    $a_json_row["cogs_account_id"] = $a->cogs_account_id;
                    $a_json_row["cost_center_id"] = $a->cost_center_id;

                    $a_json_row["item_sku_key"] = $a->item_sku_key;
                    $a_json_row["manufacturer_code"] = $a->manufacturer_code;
                    $a_json_row["row_quantity"] = $a->row_quantity;
                    $a_json_row["row_unit"] = $a->row_unit;
                    $a_json_row["row_name"] = $a->row_name;
                    $a_json_row["row_conversion_factor"] = $a->row_conversion_factor;

                    $item_thumbnail = '/images/no-pic1.jpg';
                    if ($a->item_id != null) {
                        $item_thumbnail = $nmtPlugin->getItemPic($a->item_id);
                    }

                    $a_json_row["item_thumbnail"] = $item_thumbnail;
                    $a_json_row["total_hit"] = count($results['hits']);

                    $a_json[] = $a_json_row;
                }
            }
        }
        // var_dump($a_json);
        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $response->setContent(json_encode($a_json));
        return $response;
    }

    /**
     *
     * @deprecated
     * @return \Zend\View\Model\ViewModel
     */
    public function do2Action()
    {
        $q = $this->params()->fromQuery('q');
        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        if ($q !== "") {
            $results = $this->prSearchService->search($q);
        } else {
            $results = [
                "message" => "",
                "hits" => null,
                'nmtPlugin' => $nmtPlugin
            ];
        }

        $viewTemplete = "procure/pr-search/do";

        // var_dump($results);
        $viewModel = new ViewModel(array(
            'message' => $results["message"],
            'hits' => $results["hits"],
            'nmtPlugin' => $nmtPlugin
        ));

        $viewModel->setTemplate($viewTemplete);
        return $viewModel;
    }

    // Setter and Getter
    // ======================
}
