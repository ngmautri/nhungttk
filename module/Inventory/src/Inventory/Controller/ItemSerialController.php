<?php
namespace Inventory\Controller;

use Application\Controller\Contracts\AbstractGenericController;
use Application\Domain\Contracts\FormActions;
use Application\Domain\Util\Collection\Contracts\SupportedRenderType;
use Application\Domain\Util\Pagination\Paginator;
use Application\Entity\NmtInventorySerial;
use Inventory\Application\Reporting\ItemSerial\ItemSerialReporter;
use Inventory\Form\ItemSerial\ItemSerialFilterForm;
use Inventory\Infrastructure\Persistence\SQL\Filter\ItemSerialSqlFilter;
use Inventory\Service\ItemSearchService;
use Zend\Hydrator\Reflection;
use Zend\Math\Rand;
use Zend\Validator\Date;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemSerialController extends AbstractGenericController
{

    protected $itemSearchService;

    protected $itemSerialService;

    protected $itemSerialReporter;

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function list1Action()
    {
        $this->layout("layout/user/ajax");
        $form_action = "/inventory/item-serial/list1";
        $form_title = "Item Serial Map";
        $action = FormActions::SHOW;

        $viewTemplete = "/inventory/item-serial/list1";

        $request = $this->getRequest();

        // var_dump($this->params()->fromQuery());

        // / var_dump($_GET);

        $itemId = $this->getGETparam('itemId');
        $docMonth = $this->getGETparam('docMonth');
        $docMonth = $this->getGETparam('docMonth');
        $perPage = $this->getGETparam('resultPerPage', 20);
        $render_type = $this->getGETparam('render_type', SupportedRenderType::HMTL_TABLE);
        $page = $this->getGETparam('page', 1);

        if ($page <= 0 or $page == null) {
            $page = 1;
        }

        $filter = new ItemSerialSqlFilter();
        $filter->setItemId($itemId);
        $filter->setResultPerPage($perPage);
        $filter->setDocMonth($docMonth);

        // var_dump($filter);

        $form = new ItemSerialFilterForm("filter_form");
        $form->setAction($form_action . "?render_type=" . $render_type);
        $form->setHydrator(new Reflection());
        $form->setRedirectUrl('http://mla-app.com/inventory/item/list2');
        $form->setFormAction($action);
        $form->refresh();
        $form->bind($filter);

        $collectionRender = $this->getItemSerialReporter()->getItemSerialCollectionRender($filter, $page, $perPage, $render_type);

        $viewModel = new ViewModel(array(
            'action' => $action,
            'form_action' => $form_action,
            'form_title' => $form_title,
            'redirectUrl' => null,
            'collectionRender' => $collectionRender,
            'companyVO' => $this->getCompanyVO(),
            'form' => $form
        ));

        $viewModel->setTemplate($viewTemplete);
        return $viewModel;
    }

    public function girdAction()
    {
        $itemId = $this->getGETparam('itemId');
        $docMonth = $this->getGETparam('docMonth');
        $page = $this->getGETparam('pq_curpage', 1);
        $perPage = $this->getGETparam('pq_rpp', 50);

        $filter = new ItemSerialSqlFilter();
        $filter->setItemId($itemId);
        $filter->setResultPerPage($perPage);
        $filter->setDocMonth($docMonth);

        $collectionRender = $this->getItemSerialReporter()->getItemSerialCollectionRender($filter, $page, $perPage, SupportedRenderType::AS_ARRAY);

        if ($collectionRender == null) {
            return $this->redirect()->toRoute('not_found');
        }

        $a_json_final['data'] = $collectionRender->execute();
        $a_json_final['totalRecords'] = $collectionRender->getPaginator()->getTotalResults();
        $a_json_final['curPage'] = $page;

        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $response->setContent(json_encode($a_json_final));
        return $response;
    }

    public function removeAction()
    {
        $ids = $this->getPOSTparam('ids');

        $a_json_final['data'] = $ids;
        $this->logInfo(json_encode($a_json_final));

        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');

        $response->setContent(json_encode($a_json_final));
        return $response;
    }

    /*
     * |=============================
     * |Mapping Serial
     * |
     * |=============================
     */

    /**
     *
     * @deprecated
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function addAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {

            $errors = array();
            $redirectUrl = $request->getPost('redirectUrl');

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

            $entity = new NmtInventorySerial();

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

            if (count($errors) > 0) {
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity
                ));
            }

            // NO ERROR
            // +++++++++++++++++++++++++

            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                "email" => $this->identity()
            ));
            $createdOn = new \DateTime();

            $entity->setCreatedBy($u);
            $entity->setCreatedOn($createdOn);
            $entity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));

            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();

            $m = sprintf('S/N %s - #%s created. OK!', $entity->getSerialNumber(), $entity->getId());

            // Trigger: procure.activity.log. AbtractController is EventManagerAware.
            $this->getEventManager()->trigger('inventory.activity.log', __METHOD__, array(
                'priority' => \Zend\Log\Logger::INFO,
                'message' => $m,
                'createdBy' => $u,
                'createdOn' => $createdOn
            ));

            $this->flashMessenger()->addMessage($m);
            return $this->redirect()->toUrl($redirectUrl);
        }

        // NO POST
        // ==========================

        $redirectUrl = null;
        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();

        $entity = null;
        return new ViewModel(array(
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity
        ));
    }

    /**
     *
     * @deprecated
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
     * @deprecated
     * @return \Zend\View\Model\ViewModel
     */
    public function editAction()
    {

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $country_list = $nmtPlugin->countryList();

        $request = $this->getRequest();

        // Is Posing
        // =============================

        // Is Posing
        // =============================
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

            /** @var \Application\Entity\NmtInventoryItemSerial $entity ; */
            $entity = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemSerial')->findOneBy($criteria);

            if ($entity == null) {
                $errors[] = 'Entity not found or emty!';
                $this->flashMessenger()->addMessage('Something wrong!');
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => null,
                    'country_list' => $country_list,
                    'n' => $nTry
                ));

                // might need redirect
            } else {

                $oldEntity = clone ($entity);

                $serialNumber = $request->getPost('serialNumber');
                $location = $request->getPost('location');
                $category = $request->getPost('category');

                $origin_country_id = $request->getPost('origin_country_id');

                $mfgName = $request->getPost('mfgName');
                $mfgDate = $request->getPost('mfgDate');
                $mfgSerialNumber = $request->getPost('mfgSerialNumber');
                $mfgModel = $request->getPost('mfgModel');
                $capacity = $request->getPost('capacity');

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
                $entity->setCapacity($capacity);

                $entity->setMfgName($mfgName);
                $entity->setMfgModel($mfgModel);

                $entity->setMfgSerialNumber($mfgSerialNumber);
                $entity->setLotNumber($lotNumber);
                $entity->setRemarks($remarks);

                if ($serialNumber == "") {
                    // $errors[] = 'Pls give serial number!';
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

                $criteria = array(
                    'id' => $origin_country_id
                );
                $country = $this->doctrineEM->getRepository('\Application\Entity\NmtApplicationCountry')->findOneBy($criteria);

                if ($country !== null) {
                    $entity->setOriginCountry($country);
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
                        'country_list' => $country_list,

                        'n' => $nTry
                    ));
                }

                // NO ERROR
                // Saving into Database..........
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

                $redirectUrl = sprintf("/inventory/item/view?tab_idx=6&entity_id=%s&entity_token=%s", $entity->getItem()->getId(), $entity->getItem()->getToken());

                return $this->redirect()->toUrl($redirectUrl);
            }
        }

        // NO POST
        // Initiate ......................
        // ================================
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
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemSerial')->findOneBy($criteria);
        return new ViewModel(array(
            'errors' => null,
            'entity' => $entity,
            'redirectUrl' => $redirectUrl,
            'country_list' => $country_list,
            'n' => 0
        ));
    }

    /**
     *
     * @deprecated
     * @return \Zend\View\Model\ViewModel
     */
    public function listAction()
    {
        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');
        $currentState = $this->params()->fromQuery('currentState');

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
        ;

        $is_active = (int) $this->params()->fromQuery('is_active');

        if ($is_active == null) {
            $is_active = 1;
        }

        if ($sort_by == null) :
            $sort_by = "createdOn";
        endif;

        if ($sort == null) :
            $sort = "DESC";
        endif;

        $criteria = array();
        $sort_criteria = array(
            "sysNumber" => "DESC"
        );

        $list = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemSerial')->findBy($criteria, $sort_criteria);

        $total_records = count($list);
        $paginator = null;

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            $list = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemSerial')->findBy($criteria, $sort_criteria, ($paginator->getMaxInPage() - $paginator->getMinInPage()) + 1, $paginator->getMinInPage() - 1);
        }

        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator,
            'is_active' => $is_active,
            'sort_by' => $sort_by,
            'sort' => $sort,
            'per_pape' => $resultsPerPage,
            'currentState' => $currentState
        ));
    }

    /**
     *
     * @deprecated
     * @return \Zend\View\Model\ViewModel
     */
    public function list2Action()
    {
        $request = $this->getRequest();

        // accepted only ajax request

        if (! $request->isXmlHttpRequest()) {
            // return $this->redirect()->toRoute('access_denied');
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
            'item' => $target
        );

        $sort_criteria = array();

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

        $list = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemSerial')->findBy($criteria, $sort_criteria);
        $total_records = count($list);
        $paginator = null;

        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator,
            'target_id' => $target_id,
            'token' => $token
        ));
    }

    /*
     * |=============================
     * |Mapping Serial
     * |
     * |=============================
     */

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
     * @return \Inventory\Controller\ItemSerialController
     */
    public function setItemSearchService(ItemSearchService $itemSearchService)
    {
        $this->itemSearchService = $itemSearchService;
        return $this;
    }

    /**
     *
     * @return \Inventory\Service\ItemSerialService
     */
    public function getItemSerialService()
    {
        return $this->itemSerialService;
    }

    /**
     *
     * @param \Inventory\Service\ItemSerialService $itemSerialService
     */
    public function setItemSerialService(\Inventory\Service\ItemSerialService $itemSerialService)
    {
        $this->itemSerialService = $itemSerialService;
    }

    /**
     *
     * @return ItemSerialReporter
     */
    public function getItemSerialReporter()
    {
        return $this->itemSerialReporter;
    }

    /**
     *
     * @param ItemSerialReporter $itemSerialReporter
     */
    public function setItemSerialReporter(ItemSerialReporter $itemSerialReporter)
    {
        $this->itemSerialReporter = $itemSerialReporter;
    }
}
