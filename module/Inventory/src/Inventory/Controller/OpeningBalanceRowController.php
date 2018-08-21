<?php
namespace Inventory\Controller;

use Application\Entity\FinJe;
use Application\Entity\FinJeRow;
use Application\Entity\NmtHrFingerscan;
use Doctrine\ORM\EntityManager;
use Inventory\Service\ItemSearchService;
use MLA\Paginator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Zend\Math\Rand;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Date;
use Zend\View\Model\ViewModel;
use Application\Entity\NmtInventoryOpeningBalanceRow;
use Application\Entity\NmtInventoryOpeningBalance;
use Zend\Escaper\Escaper;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class OpeningBalanceRowController extends AbstractActionController
{

    protected $doctrineEM;

    protected $itemSearchService;

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function girdAction()
    {
        $request = $this->getRequest();

        if ($request->getHeader('Referer') == null) {
            // return $this->redirect()->toRoute('access_denied');
        }

        // $pq_curPage = $_GET ["pq_curpage"];
        // $pq_rPP = $_GET ["pq_rpp"];

        $target_id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $target_id,
            'token' => $token
        );

        /** @var \Application\Entity\NmtInventoryOpeningBalance $a ;*/
        $target = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryOpeningBalance')->findOneBy($criteria);

        $a_json_final = array();
        $a_json = array();
        $a_json_row = array();
        $escaper = new Escaper();

        if ($target instanceof \Application\Entity\NmtInventoryOpeningBalance) {

            $query = 'SELECT e FROM Application\Entity\NmtInventoryOpeningBalanceRow e
            WHERE e.openingBalance=?1';

            $list = $this->doctrineEM->createQuery($query)
                ->setParameters(array(
                "1" => $target
            ))
                ->getResult();

            $total_records = 0;
            if (count($list) > 0) {
                $escaper = new Escaper();

                $total_records = count($list);
                foreach ($list as $a) {

                    /** @var \Application\Entity\NmtInventoryOpeningBalanceRow $a ;*/

                    $a_json_row["row_id"] = $a->getId();
                    $a_json_row["row_token"] = $a->getToken();
                    $a_json_row["row_quantity"] = $a->getQuantity();
                    $a_json_row["row_unit_price"] = $a->getUnitPrice();
                    $a_json_row["row_total_price"] = $a->getNetAmount();

                    if ($a->getGlAccount() != null) {
                        $a_json_row["row_gl_account"] = $a->getGlAccount()->getAccountNumber();
                    } else {
                        $a_json_row["row_gl_account"] = "";
                    }

                    $item_detail = "/inventory/item/show1?token=" . $a->getItem()->getToken() . "&checksum=" . $a->getItem()->getChecksum() . "&entity_id=" . $a->getItem()->getId();
                    if ($a->getItem()->getItemName() !== null) {
                        $onclick = "showJqueryDialog('Detail of Item: " . $escaper->escapeJs($a->getItem()
                            ->getItemName()) . "','1310',$(window).height()-50,'" . $item_detail . "','j_loaded_data', true);";
                    } else {
                        $onclick = "showJqueryDialog('Detail of Item: " . ($a->getItem()->getItemName()) . "','1310',$(window).height()-50,'" . $item_detail . "','j_loaded_data', true);";
                    }

                    if (strlen($a->getItem()->getItemName()) < 35) {
                        $a_json_row["item_name"] = $a->getItem()->getItemName() . '<a style="cursor:pointer;color:blue"  item-pic="" id="' . $a->getItem()->getId() . '" item_name="' . $a->getItem()->getItemName() . '" title="' . $a->getItem()->getItemName() . '" href="javascript:;" onclick="' . $onclick . '" >&nbsp;&nbsp;....&nbsp;&nbsp;</a>';
                    } else {
                        $a_json_row["item_name"] = substr($a->getItem()->getItemName(), 0, 30) . '<a style="cursor:pointer;;color:blue"  item-pic="" id="' . $a->getItem()->getId() . '" item_name="' . $a->getItem()->getItemName() . '" title="' . $a->getItem()->getItemName() . '" href="javascript:;" onclick="' . $onclick . '" >&nbsp;&nbsp;...&nbsp;&nbsp;</a>';
                    }

                    // $a_json_row["item_name"] = $a->getItem()->getItemName();

                    $a_json_row["item_sku"] = $a->getItem()->getItemSku();
                    $a_json_row["item_token"] = $a->getItem()->getToken();
                    $a_json_row["item_checksum"] = $a->getItem()->getChecksum();

                    $a_json[] = $a_json_row;
                }
            }

            $a_json_final['data'] = $a_json;
            $a_json_final['totalRecords'] = $total_records;
            // $a_json_final ['curPage'] = $pq_curPage;
        }

        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $response->setContent(json_encode($a_json_final));
        return $response;
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function addAction()
    {
        $request = $this->getRequest();
        $this->layout("Inventory/layout-fullscreen");

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $gl_list = $nmtPlugin->glAccountList();

        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));

        // Is Posting .................
        // ============================
        if ($request->isPost()) {
            $errors = array();
            $redirectUrl = $request->getPost('redirectUrl');

            $target_id = (int) $request->getPost('target_id');
            $target_token = $request->getPost('token');

            /**@var \Application\Entity\NmtInventoryOpeningBalance $target ;*/
            $target = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryOpeningBalance')->findOneBy(array(
                "id" => $target_id,
                "token" => $target_token
            ));

            if ($target == null) {

                $errors[] = 'Opening Balance object can\'t be empty. Or token key is not valid!';

                $viewModel = new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'target' => null,
                    'entity' => null,
                    'gl_list' => $gl_list
                ));

                // $viewModel->setTemplate("inventory/gi-row/add" . $target->getMovementType());
                return $viewModel;
            }

            $item_id = (int) $request->getPost('item_id');
            $isActive = (int) $request->getPost('isActive');
            $quantity = $request->getPost('quantity');
            $unitPrice = $request->getPost('unitPrice');

            $remarks = $request->getPost('remarks');

            if ($isActive != 1) {
                $isActive = 0;
            }

            // Inventory Transaction:
            $entity = new NmtInventoryOpeningBalanceRow();
            $entity->setOpeningBalance($target);
            $entity->setIsActive($isActive);

            /**@var \Application\Entity\NmtInventoryItem $item ;*/
            $item = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->find($item_id);

            if ($item == null) {
                $errors[] = $nmtPlugin->translate('No Item is selected');
            } else {
                if ($item->getIsStocked() != 1) {
                    $errors[] = $nmtPlugin->translate('Item is not kept in stock');
                } else {
                    $entity->setItem($item);
                }
            }

            $n_validated = 0;
            if ($quantity == null) {
                $errors[] = 'Please  enter quantity!';
            } else {

                if (! is_numeric($quantity)) {
                    $errors[] = $nmtPlugin->translate('Quantity must be a number!');
                } else {
                    if ($quantity <= 0) {
                        $errors[] = $nmtPlugin->translate('Quantity must be greater than 0!');
                    } else {
                        $n_validated ++;
                        $entity->setQuantity($quantity);
                    }
                }
            }

            if ($unitPrice == null) {
                $errors[] = 'Please  enter unit!';
            } else {

                if (! is_numeric($unitPrice)) {
                    $errors[] = $nmtPlugin->translate('Unit Price must be a number!');
                } else {
                    if ($unitPrice <= 0) {
                        $errors[] = $nmtPlugin->translate('Unit Price  must be greater than 0!');
                    } else {
                        $n_validated ++;
                        $entity->setUnitPrice($unitPrice);
                    }
                }
            }

            if ($n_validated == 2) {
                $entity->setNetAmount($entity->getQuantity() * $entity->getUnitPrice());
            }

            $entity->setGlAccount($target->getGlAccount());
            $entity->setCurrency($target->getCurrency());
            $entity->setExchangeRate(1);
            $entity->setRemarks($remarks);

            if (count($errors) > 0) {

                $viewModel = new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'target' => $target,
                    'gl_list' => $gl_list
                ));

                // $viewModel->setTemplate("inventory/gi-row/add" . $target->getMovementType());
                return $viewModel;
            }

            // NO ERROR
            // Saving into Database..........
            // ++++++++++++++++++++++++++++++

            $entity->setDocStatus($target->getDocStatus());
            $entity->setCreatedBy($u);
            $entity->setCreatedOn(new \DateTime());
            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();

            $redirectUrl = "/inventory/opening-balance-row/add?token=" . $target->getToken() . "&target_id=" . $target->getId();
            $m = sprintf("[OK] Opening Balance Line: %s created!", $entity->getId());
            $this->flashMessenger()->addMessage($m);

            return $this->redirect()->toUrl($redirectUrl);
        }

        // NO POST
        // Initiate.....................
        // ==============================

        $redirectUrl = Null;

        /*
         * if ($request->getHeader('Referer') == null) {
         * return $this->redirect()->toRoute('access_denied');
         * }
         *
         * $redirectUrl = $this->getRequest()
         * ->getHeader('Referer')
         * ->getUri();
         */

        $id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');

        $target = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryOpeningBalance')->findOneBy(array(
            "id" => $id,
            "token" => $token
        ));

        if ($target == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $entity = new NmtInventoryOpeningBalanceRow();
        $entity->setIsActive(1);

        $viewModel = new ViewModel(array(
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'target' => $target,
            'gl_list' => $gl_list
        ));

        // $viewModel->setTemplate("inventory/gi-row/add" . $target->getMovementType());
        return $viewModel;
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function showAction()
    {
        $request = $this->getRequest();

        // NO POST
        $redirectUrl = Null;
        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();
        $entity_id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');
        $checksum = $this->params()->fromQuery('checksum');

        $criteria = array(
            'id' => $entity_id,
            // 'checksum' => $checksum,
            'token' => $token
        );

        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryTrx')->findOneBy($criteria);
        if ($entity !== null) {
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'errors' => null,
                'entity' => $entity,
                'target' => $entity->getItem()
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function editAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {

            $errors = array();
            $redirectUrl = $request->getPost('redirectUrl');
            $entity_id = (int) $request->getPost('entity_id');
            $token = $request->getPost('token');
            $nTry = $request->getPost('n');

            $criteria = array(
                'id' => $entity_id,
                'token' => $token
            );

            /** @var \Application\Entity\NmtInventorySerial $entity ; */
            $entity = $this->doctrineEM->getRepository('Application\Entity\NmtInventorySerial')->findOneBy($criteria);

            if (! $entity instanceof \Application\Entity\NmtInventorySerial) {
                $errors[] = 'Entity not found or emty!';
                $this->flashMessenger()->addMessage('Something wrong!');
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => null,
                    'n' => $nTry
                ));

                // might need redirect
            } else {

                $oldEntity = clone ($entity);

                $serialNumber = $request->getPost('serialNumber');
                $location = $request->getPost('location');
                $category = $request->getPost('category');
                $mfgName = $request->getPost('mfgName');
                $mfgDate = $request->getPost('mfgDate');
                $mfgSerialNumber = $request->getPost('mfgSerialNumber');
                $lotNumber = $request->getPost('lotNumber');
                $mfgWarrantyStart = $request->getPost('mfgWarrantyStart');
                $mfgWarrantyEnd = $request->getPost('mfgWarrantyEnd');
                $isActive = (int) $request->getPost('isActive');
                $remarks = $request->getPost('remarks');

                if ($isActive !== 1) {
                    $isActive = 0;
                }

                $entity->setIsActive($isActive);

                $entity->setSerialNumber($serialNumber);
                $entity->setLocation($location);
                $entity->setCategory($category);

                $entity->setMfgName($mfgName);
                $entity->setMfgSerialNumber($mfgSerialNumber);
                $entity->setLotNumber($lotNumber);
                $entity->setRemarks($remarks);

                if ($serialNumber == "") {
                    $errors[] = 'Pls give serial number!';
                } else {

                    if ($serialNumber !== $oldEntity->getSerialNumber()) {
                        $criteria = array(
                            'serialNumber' => $serialNumber
                        );

                        /** @var \Application\Entity\NmtInventorySerial $entity_ck ; */
                        $entity_ck = $this->doctrineEM->getRepository('Application\Entity\NmtInventorySerial')->findOneBy($criteria);
                        if ($entity_ck == null) {
                            $entity->setSerialNumber($serialNumber);
                        } else {
                            $errors[] = $serialNumber . ' exists already!';
                        }
                    }
                }

                $validator = new Date();

                if (! $mfgDate == null) {
                    if (! $validator->isValid($mfgDate)) {
                        $errors[] = 'Manufacturing Date is not correct!';
                    } else {
                        $entity->setMfgWarrantyStart(new \DateTime($mfgDate));
                    }
                }

                if (! $mfgWarrantyStart == null) {
                    if (! $validator->isValid($mfgWarrantyStart)) {
                        $errors[] = 'Warranty Start Date is not correct!';
                    } else {
                        $entity->setMfgDate(new \DateTime($mfgWarrantyStart));
                    }
                }

                $n_validated = 0;
                if (! $mfgWarrantyStart == null) {
                    if (! $validator->isValid($mfgWarrantyStart)) {
                        $errors[] = 'Warranty Start Date is not correct!';
                    } else {
                        $n_validated ++;
                        $entity->setMfgDate(new \DateTime($mfgWarrantyStart));
                    }
                }

                if (! $mfgWarrantyEnd == null) {
                    if (! $validator->isValid($mfgWarrantyEnd)) {
                        $errors[] = 'Warranty End Date is not correct!';
                    } else {
                        $n_validated ++;
                        $entity->setMfgWarrantyEnd(new \DateTime($mfgWarrantyEnd));
                    }
                }

                if ($n_validated == 2) {
                    if ($mfgWarrantyEnd <= $mfgWarrantyStart) {
                        $errors[] = 'Warranty End Date is not correct!';
                    }
                }

                /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
                $nmtPlugin = $this->Nmtplugin();
                $changeArray = $nmtPlugin->objectsAreIdentical($oldEntity, $entity);

                if (count($changeArray) == 0) {
                    $nTry ++;
                    $errors[] = sprintf('Nothing changed! n = %s', $nTry);
                }

                if ($nTry >= 3) {
                    $errors[] = sprintf('Do you really want to edit (%s)?', $entity->getSerialNumber());
                }

                if ($nTry == 5) {
                    $m = sprintf('You might be not ready to edit (%s). Please try later!', $entity->getSerialNumber());
                    $this->flashMessenger()->addMessage($m);
                    return $this->redirect()->toUrl($redirectUrl);
                }

                if (count($errors) > 0) {
                    return new ViewModel(array(
                        'redirectUrl' => $redirectUrl,
                        'errors' => $errors,
                        'entity' => $entity,
                        'n' => $nTry
                    ));
                }

                // NO ERROR
                // ++++++++++++++++++++++++++++++

                $changeOn = new \DateTime();

                $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                    "email" => $this->identity()
                ));

                $entity->setLastchangeBy($u);
                $entity->setLastchangeOn($changeOn);

                $this->doctrineEM->persist($entity);
                $this->doctrineEM->flush();

                $m = sprintf('S/N %s - #%s updated. Change No %s. OK!', $entity->getSerialNumber(), $entity->getId(), count($changeArray));

                // Trigger Change Log. AbtractController is EventManagerAware.
                $this->getEventManager()->trigger('inventory.change.log', __METHOD__, array(
                    'priority' => 7,
                    'message' => $m,
                    'objectId' => $entity->getId(),
                    'objectToken' => $entity->getToken(),
                    'changeArray' => $changeArray,
                    'changeBy' => $u,
                    'changeOn' => $changeOn,
                    'revisionNumber' => 1,
                    'changeDate' => $changeOn,
                    'changeValidFrom' => $changeOn
                ));

                // Trigger Activity Log . AbtractController is EventManagerAware.
                $this->getEventManager()->trigger('inventory.activity.log', __METHOD__, array(
                    'priority' => \Zend\Log\Logger::INFO,
                    'message' => $m,
                    'createdBy' => $u,
                    'createdOn' => $changeOn
                ));

                $this->flashMessenger()->addMessage($m);
                return $this->redirect()->toUrl($redirectUrl);
            }
        }

        // NO POST
        // =======================

        $redirectUrl = null;
        if ($this->getRequest()->getHeader('Referer') !== null) {
            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $id,
            'token' => $token
        );

        /** @var \Application\Entity\NmtInventorySerial $entity ; */
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtInventorySerial')->findOneBy($criteria);
        return new ViewModel(array(
            'errors' => null,
            'entity' => $entity,
            'redirectUrl' => $redirectUrl,
            'n' => 0
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function listAction()
    {
        $is_active = (int) $this->params()->fromQuery('is_active');
        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');

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
        $criteria = array();
        $sort_criteria = array();

        $list = $this->doctrineEM->getRepository('\Application\Entity\NmtInventoryOpeningBalance')->findBy($criteria, $sort_criteria);
        $total_records = count($list);
        $paginator = null;

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            $list = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryOpeningBalance')->findBy($criteria, $sort_criteria, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
        }

        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator,
            'sort_by' => $sort_by,
            'sort' => $sort,
            'is_active' => $is_active,
            'per_pape' => $resultsPerPage
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function list1Action()
    {
        $request = $this->getRequest();

        // accepted only ajax request

        if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }

        $this->layout("layout/user/ajax");

        $target_id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');
        $checksum = $this->params()->fromQuery('checksum');

        $criteria = array(
            'id' => $target_id,
            'checksum' => $checksum,
            'token' => $token
        );

        $target = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->findOneBy($criteria);

        if ($target == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $criteria = array(
            'item' => $target
        );

        $sort_criteria = array(
            'trxDate' => "DESC"
        );

        $list = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryTrx')->findBy($criteria, $sort_criteria);
        $total_records = count($list);
        $paginator = null;

        /*
         * $this->getResponse()->getHeaders ()->addHeaderLine('Expires', '3800', true);
         * $this->getResponse()->getHeaders ()->addHeaderLine('Cache-Control', 'public', true);
         * $this->getResponse()->getHeaders ()->addHeaderLine('Cache-Control', 'max-age=3800');
         * $this->getResponse()->getHeaders ()->addHeaderLine('Pragma', '', true);
         */
        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator,
            'target' => $target
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function updateTokenAction()
    {
        $criteria = array();

        // var_dump($criteria);
        $sort_criteria = array();

        $list = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->findBy($criteria, $sort_criteria);

        if (count($list) > 0) {
            foreach ($list as $entity) {
                $entity->setChecksum(md5(uniqid("item_" . $entity->getId()) . microtime()));
                $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
            }
        }

        $this->doctrineEM->flush();

        // update search index()
        $this->itemSearchService->createItemIndex();

        $total_records = count($list);

        return new ViewModel(array(
            'total_records' => $total_records
        ));
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
