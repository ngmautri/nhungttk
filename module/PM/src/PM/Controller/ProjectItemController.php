<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace PM\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;
use Zend\View\Model\ViewModel;
use MLA\Paginator;
use Application\Entity\NmtApplicationAttachment;
use Zend\Http\Headers;
use Zend\Validator\Date;
use Zend\Math\Rand;
use Zend\Escaper\Escaper;

/**
 *
 * @author nmt
 *        
 */
class ProjectItemController extends AbstractActionController
{

    const CHAR_LIST = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";

    protected $doctrineEM;

    /**
     *
     * {@inheritdoc}
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function indexAction()
    {}

    public function girdAction()
    {
        $request = $this->getRequest();
        
        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');
        $balance = $this->params()->fromQuery('balance');
        $is_active = $this->params()->fromQuery('is_active');
        
        if ($sort_by == null) :
            $sort_by = "createdOn";
	        endif;
        
        if ($balance == null) :
            $balance = 2;
	        endif;
        
        if ($sort == null) :
            $sort = "ASC";
	        endif;
        
        $target_id = (int) $this->params()->fromQuery('target_id');
        $checksum = $this->params()->fromQuery('checksum');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $target_id,
            'checksum' => $checksum,
            'token' => $token
        );
        
        /**
         *
         * @todo : Change Target
         */
        $target = $this->doctrineEM->getRepository('Application\Entity\NmtPmProject')->findOneBy($criteria);
        
        $a_json_final = array();
        $a_json = array();
        $a_json_row = array();
        $escaper = new Escaper();
        
        if ($target !== null) {
            
            $criteria = array(
                'pm' => $target_id
                // 'isActive' => 1,
            );
            
            /** @var \Application\Repository\NmtProcurePrRowRepository  $rep */            
            $rep = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow');
            
            $list = $rep->getProjectItem($target_id);
            foreach ($list as $a) {
                
                $a_json_row ["pr_number"] = $a ['pr_number'];
                
                $a_json_row ["row_id"] = $a ['pr_row_id'];
                $a_json_row ["row_token"] = $a ['pr_row_token'];
                $a_json_row ["row_checksum"] = $a ['pr_row_checksum'];
                
                $item_detail = "/inventory/item/show1?token=" . $a['item_token'] . "&checksum=" . $a['item_checksum'] . "&entity_id=" . $a['item_id'];
                
                if ($a['item_name'] !== null) {
                    $onclick = "showJqueryDialog('Detail of Item: " . $escaper->escapeJs($a['item_name']) . "','1200',$(window).height()-100,'" . $item_detail . "','j_loaded_data', true);";
                } else {
                    $onclick = "showJqueryDialog('Detail of Item: " . ($a['item_name']) . "','1200',$(window).height()-100,'" . $item_detail . "','j_loaded_data', true);";
                }
                
                 $a_json_row["item_sku"] = '<span title="SKU: ' . $a['item_sku'] . '">' . substr($a['item_sku'], 0, 5) . '</span>';
                
                if (strlen($a['item_name']) < 40) {
                    $a_json_row["item_name"] = '<a style="cursor:pointer;"  item-pic="" id="' . $a['item_id'] . '" item_name="' . $a['item_name'] . '" title="' . $a['item_name'] . '" href="javascript:;" onclick="' . $onclick . '" >' . $a['item_name'] . '</a>';
                } else {
                    $a_json_row["item_name"] = '<a style="" title="' . $a['item_name'] . '" href="javascript:;" onclick="' . $onclick . '" >' . substr($a['item_name'], 0, 36) . ' ...</a>';
                    $a_json_row["item_name"] = '<a style="cursor:pointer;"  item-pic="" id="' . $a['item_id'] . '" item_name="' . $a['item_name'] . '" title="' . $a['item_name'] . '" href="javascript:;" onclick="' . $onclick . '" >' . substr($a['item_name'], 0, 36) . '</a>';
                }
                
                $a_json_row["quantity"] = $a['quantity'];
                $a_json_row["vendor_unit_price"] = $a['vendor_unit_price'];
                
                if (strlen($a['vendor_name']) < 10) {
                    $a_json_row["vendor_name"] = $a['vendor_name'];
                } else {
                    $a_json_row["vendor_name"] = '<span title="' . $a['vendor_name'] . '">' . substr($a['vendor_name'], 0, 8) . '...</span>';
                }
                
                if ($a['vendor_unit_price'] !== null) {
                    $a_json_row["vendor_unit_price"] = number_format($a['vendor_unit_price'], 2);
                } else {
                    $a_json_row["vendor_unit_price"] = 0;
                }
                
                if ($a['total_price'] !== null) {
                    $a_json_row["total_price"] = number_format($a['total_price'], 2);
                } else {
                    $a_json_row["total_price"] = 0;
                }
                
                $a_json_row["currency"] = $a['currency'];
                
                 if (strlen($a['remarks']) < 20) {
                    $a_json_row["remarks"] = $a['remarks'];
                } else {
                    $a_json_row["remarks"] = '<span title="' . $a['remarks'] . '">' . substr($a['remarks'], 0, 15) . '...</span>';
                }
                $a_json_row["fa_remarks"] = $a['fa_remarks'];                
                $a_json[] = $a_json_row;
            }
            
            $a_json_final['data'] = $a_json;
            $a_json_final['totalRecords'] = 10;
            $a_json_final['curPage'] = 1;
        }
        
        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $response->setContent(json_encode($a_json_final));
        return $response;
    }

    /**
     *
     * @return \Zend\Stdlib\ResponseInterface
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
}
