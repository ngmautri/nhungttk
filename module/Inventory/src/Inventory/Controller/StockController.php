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

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class StockController extends AbstractActionController
{

    protected $doctrineEM;

    protected $itemSearchService;

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
        $currency_list = $nmtPlugin->currencyList();
        $gl_list = $nmtPlugin->glAccountList();

        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));

        $default_wh = null;
        $default_wh = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->findOneBy(array(
            'isDefault' => 1
        ));

        $default_cur = null;
        if ($u->getCompany() instanceof \Application\Entity\NmtApplicationCompany) {
            $default_cur = $u->getCompany()->getDefaultCurrency();
        }

        // Is Posing
        // =============================
        if ($request->isPost()) {

            $errors = array();
            $redirectUrl = $request->getPost('redirectUrl');
            $postingDate = $request->getPost('postingDate');
            $warehouse_id = (int) $request->getPost('target_wh_id');
            $gl_account_id = (int) $request->getPost('gl_account_id');
            $currency_id = (int) $request->getPost('currency_id');

            $isActive = (int) $request->getPost('isActive');
            $remarks = $request->getPost('remarks');

            if ($isActive != 1) {
                $isActive = 0;
            }

            $entity = new NmtInventoryOpeningBalance();

            $entity->setIsActive($isActive);
            $entity->setCurrency($default_cur);
            $validator = new Date();

            if ($postingDate !== null) {
                if (! $validator->isValid($postingDate)) {
                    $errors[] = $nmtPlugin->translate('Posting Date is not correct or empty!');
                } else {
                    $entity->setPostingDate(new \DateTime($postingDate));
                }
            }

            $warehouse = null;
            if ($warehouse_id > 0) {
                $warehouse = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->find($warehouse_id);
            }

            if ($warehouse !== null) {
                $entity->setWarehouse($warehouse);
            } else {
                $errors[] = $nmtPlugin->translate('Warehouse can\'t be empty. Please select a Wahrhouse!');
            }

            $gl_account = null;
            if ($gl_account_id > 0) {
                $gl_account = $this->doctrineEM->getRepository('Application\Entity\FinAccount')->find($gl_account_id);
            }

            if ($gl_account !== null) {
                $entity->setGlAccount($gl_account);
            } else {
                $errors[] = $nmtPlugin->translate('Warehouse can\'t be empty. Please select a Wahrhouse!');
            }

            $currency = null;
            if ($currency_id > 0) {
                $currency = $this->doctrineEM->getRepository('\Application\Entity\NmtApplicationCurrency')->find($currency_id);
            }

            if ($currency != null) {
                $entity->setCurrency($currency);
            } else {
                $errors[] = $nmtPlugin->translate('Currency can\'t be empty. Please select a Currency!');
            }

            $entity->setRemarks($remarks);

            if (count($errors) > 0) {
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'gl_list' => $gl_list,
                    'currency_list' => $currency_list
                ));
            }

            // NO ERROR
            // Saving into Database..........
            // ++++++++++++++++++++++++++++++

            $entity->setSysNumber(\Application\Model\Constants::SYS_NUMBER_UNASSIGNED);
            $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_DRAFT);
            $entity->setIsPosted(0);

            $createdOn = new \DateTime();
            $entity->setCreatedBy($u);

            $entity->setCreatedOn($createdOn);
            $entity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));

            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();
            $m = sprintf("[OK] Open Balance: %s created!", $entity->getId());
            $this->flashMessenger()->addMessage($m);

            $redirectUrl = "/inventory/opening-balance-row/add?token=" . $entity->getToken() . "&target_id=" . $entity->getId();
            return $this->redirect()->toUrl($redirectUrl);
        }

        // NO POST
        // Initiate ......................
        // ================================

        $redirectUrl = null;
        /*
         * if ($request->getHeader('Referer') !== null) {
         * $redirectUrl = $this->getRequest()
         * ->getHeader('Referer')
         * ->getUri();
         * }
         */

        $entity = new NmtInventoryOpeningBalance();
        $entity->setIsActive(1);
        $entity->setCurrency($default_cur);

        if ($default_wh !== null) {
            $entity->setWarehouse($default_wh);
        }

        return new ViewModel(array(
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'gl_list' => $gl_list,
            'currency_list' => $currency_list
        ));
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
    public function summaryAction()
    {
        $request = $this->getRequest();

        // accepted only ajax request

        if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }

        $this->layout("layout/user/ajax");

        $target_id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');

        $criteria = array(
            'id' => $target_id,
            'token' => $token
        );

        $target = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->findOneBy($criteria);

        if ($target == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $criteria = array(
            'item' => $target,
            'isClosed' => 0
        );

        $sort_criteria = array(
            'postingDate' => "ASC"
        );

        $list = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryFifoLayer')->findBy($criteria, $sort_criteria);
        $total_records = count($list);
        $paginator = null;

        $viewTemplete = "inventory/stock/summary1";

        $viewModel = new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator,
            'target' => $target
        ));

        $viewModel->setTemplate($viewTemplete);
        return $viewModel;
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

    /**
     *
     * @return \Inventory\Service\ItemSearchService
     */
    public function getItemSearchService()
    {
        return $this->itemSearchService;
    }

    /**
     *
     * @param ItemSearchService $itemSearchService
     * @return \Inventory\Controller\OpeningBalanceController
     */
    public function setItemSearchService(ItemSearchService $itemSearchService)
    {
        $this->itemSearchService = $itemSearchService;
        return $this;
    }
}
