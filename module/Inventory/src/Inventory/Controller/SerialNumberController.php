<?php
namespace Inventory\Controller;

use Application\Entity\NmtInventorySerial;
use Doctrine\ORM\EntityManager;
use Inventory\Service\ItemSearchService;
use Application\Domain\Util\Pagination\Paginator;
use Zend\Math\Rand;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Date;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class SerialNumberController extends AbstractActionController
{

    const CHAR_LIST = "_0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";

    protected $doctrineEM;

    protected $itemSearchService;

    /**
     *
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
            $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));

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
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function assignAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {

            $target_id = (int) $request->getPost('target_id');
            $token = $request->getPost('token');
            $incomes = $request->getPost('incomes');
            $redirectUrl = $request->getPost('redirectUrl');

            $criteria = array(
                'id' => $target_id,
                'token' => $token
            );

            /**@var \Application\Entity\NmtHrContract $target ; */
            $target = $this->doctrineEM->getRepository('Application\Entity\NmtHrContract')->findOneBy($criteria);
            $errors = array();

            if (! $target instanceof \Application\Entity\NmtHrContract) {

                $errors[] = 'Entity object can\'t be empty!';
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors
                ));

                // might need redirect
            } else {

                return $this->redirect()->toUrl($redirectUrl);
            }
        }

        // NO POST
        // ==============================

        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        } else {
            $redirectUrl = $request->getHeader('Referer')->getUri();
        }

        $n = (int) $this->params()->fromQuery('n');
        $id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $id,
            'token' => $token
        );

        /**@var \Application\Repository\NmtInventoryItemRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem');
        $list = $res->getVacantSerialNumbers();

        /**@var \Application\Entity\NmtInventoryItem $target ; */
        $target = $res->findOneBy($criteria);

        // if ($target instanceof \Application\Entity\NmtInventoryItem) {

        return new ViewModel(array(
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'target' => $target,
            'serialList' => $list,
            'n' => $n
        ));
        // }
        // return $this->redirect()->toRoute('access_denied');
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
        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');
        $currentState = $this->params()->fromQuery('currentState');

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
        $sort_criteria = array();

        $list = $this->doctrineEM->getRepository('Application\Entity\NmtInventorySerial')->findBy($criteria, $sort_criteria);

        $total_records = count($list);
        $paginator = null;

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            $list = $this->doctrineEM->getRepository('Application\Entity\NmtInventorySerial')->findBy($criteria, $sort_criteria, ($paginator->getMaxInPage() - $paginator->getMinInPage()) + 1, $paginator->getMinInPage() - 1);
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
