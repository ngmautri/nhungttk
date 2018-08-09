<?php
namespace BP\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;
use Zend\View\Model\ViewModel;
use Application\Entity\NmtBpVendor;
use MLA\Paginator;
use Zend\Math\Rand;
use Exception;
use BP\Service\VendorSearchService;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class VendorController extends AbstractActionController
{

    const CHAR_LIST = "_0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";

    protected $doctrineEM;

    protected $vendorSearchService;

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function showAction()
    {
        $request = $this->getRequest();

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $gl_list = $nmtPlugin->glAccountList();

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
            'token' => $token,
            'checksum' => $checksum
        );

        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtBpVendor')->findOneBy($criteria);
        if ($entity == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        return new ViewModel(array(
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'gl_list' => $gl_list
        ));
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function addAction()
    {
        $request = $this->getRequest();

        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            'email' => $this->identity()
        ));

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $gl_list = $nmtPlugin->glAccountList();

        // Is Posing
        // =============================

        if ($request->isPost()) {

            $redirectUrl = $request->getPost('redirectUrl');
            $errors = array();

            $gl_account_id = $request->getPost('gl_account_id');

            $vendorNumber = $request->getPost('vendorNumber');
            $vendorName = $request->getPost('vendorName');
            $vendorShortName = $request->getPost('vendorShortName');

            $street = $request->getPost('street');
            $city = $request->getPost('city');
            $telephone = $request->getPost('telephone');
            $email = $request->getPost('email');
            $fax = $request->getPost('fax');
            $contractPerson = $request->getPost('contractPerson');

            $keywords = $request->getPost('keywords');
            $isActive = (int) $request->getPost('isActive');
            $remarks = $request->getPost('remarks');

            $country_id = $request->getPost('country_id');

            if ($isActive !== 1) :
                $isActive = 0;
			endif;

            $entity = new NmtBpVendor();

            $gl = $this->doctrineEM->getRepository('Application\Entity\FinAccount')->find($gl_account_id);

            if ($gl == null) {
                $errors[] = 'G/L account can\'t be empty!';
            } else {
                $entity->setGlAccount($gl);
            }

            $entity->setVendorNumber($vendorNumber);
            $entity->setIsActive($isActive);
            $entity->setKeywords($keywords);
            $entity->setRemarks($remarks);
            $entity->setVendorShortName($vendorShortName);
            $entity->setStreet($street);
            $entity->setCity($city);
            $entity->setTelephone($telephone);
            $entity->setEmail($email);
            $entity->setFax($fax);
            $entity->setContractPerson($contractPerson);

            if ($vendorName === '' or $vendorName === null) {
                $errors[] = 'Please give vendor name';
            } else {
                $entity->setVendorName($vendorName);
            }

            if ($country_id === '' or $country_id === null) {
                $errors[] = 'Please give a country!';
            } else {
                $country = $this->doctrineEM->find('Application\Entity\NmtApplicationCountry', $country_id);
                $entity->setCountry($country);
            }

            if (count($errors) > 0) {
                return new ViewModel(array(
                    'errors' => $errors,
                    'redirectUrl' => $redirectUrl,
                    'entity' => $entity,
                    'gl_list' => $gl_list
                ));
            }

            // NO ERROR
            // Saving into Database..........
            // ++++++++++++++++++++++++++++++

            try {

                $createdOn = new \DateTime();
                $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));

                $entity->setCreatedOn($createdOn);
                $entity->setCreatedBy($u);

                $this->doctrineEM->persist($entity);
                $this->doctrineEM->flush();

                $new_entity_id = $entity->getId();

                $entity->setChecksum(md5($new_entity_id . uniqid(microtime())));
                $this->doctrineEM->flush();
                $m = sprintf('"[OK] Vendor #%s - %s" created.', $entity->getId(), $entity->getVendorName());

                // Trigger Activity Log . AbtractController is EventManagerAware.
                $this->getEventManager()->trigger('bp.activity.log', __METHOD__, array(
                    'priority' => \Zend\Log\Logger::INFO,
                    'message' => $m,
                    'createdBy' => $u,
                    'createdOn' => $createdOn
                ));

                $this->vendorSearchService->updateIndex(1, $entity, false);

                $this->flashMessenger()->addSuccessMessage($m);
                return $this->redirect()->toUrl($redirectUrl);
            } catch (Exception $e) {
                return new ViewModel(array(
                    'errors' => $e->getMessage(),
                    'redirectUrl' => $redirectUrl,
                    'entity' => $entity,
                    'gl_list' => $gl_list
                ));
            }
        }

        // NO POST
        // ==========================

        $redirectUrl = Null;

        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();

        return new ViewModel(array(
            'errors' => null,
            'redirectUrl' => $redirectUrl,
            'entity' => null,
            'gl_list' => $gl_list
        ));
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function editAction()
    {
        $request = $this->getRequest();
        
        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $gl_list = $nmtPlugin->glAccountList();
        

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

            /**@var \Application\Entity\NmtBpVendor $entity ;*/
            $entity = $this->doctrineEM->getRepository('Application\Entity\NmtBpVendor')->findOneBy($criteria);

            if (! $entity instanceof \Application\Entity\NmtBpVendor) {

                $errors[] = 'Entity object can\'t be empty!';
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => null,
                    'n' => $nTry,
                    'gl_list' => $gl_list,
                    
                ));

                // might need redirect
            } else {

                $oldEntity = clone ($entity);

                $vendorNumber = $request->getPost('vendorNumber');
                $vendorName = $request->getPost('vendorName');
                $vendorShortName = $request->getPost('vendorShortName');
                $keywords = $request->getPost('keywords');
                $isActive = (int) $request->getPost('isActive');

                $country_id = (int) $request->getPost('country_id');
                
                $gl_account_id = $request->getPost('gl_account_id');
                
                $gl = $this->doctrineEM->getRepository('Application\Entity\FinAccount')->find($gl_account_id);
                
                if ($gl == null) {
                    $errors[] = 'G/L account can\'t be empty!';
                } else {
                    $entity->setGlAccount($gl);
                }
                

                $street = $request->getPost('street');
                $city = $request->getPost('city');
                $telephone = $request->getPost('telephone');
                $email = $request->getPost('email');
                $fax = $request->getPost('fax');
                $contractPerson = $request->getPost('contractPerson');

                $remarks = $request->getPost('remarks');

                if ($isActive !== 1) :
                    $isActive = 0;
				endif;

                if ($vendorName === '' or $vendorName === null) {
                    $errors[] = 'Please give vendor name';
                }

                $entity->setVendorNumber($vendorNumber);
                $entity->setVendorName($vendorName);
                $entity->setVendorShortName($vendorShortName);

                $entity->setKeywords($keywords);
                $entity->setRemarks($remarks);
                $entity->setIsActive($isActive);

                $entity->setStreet($street);
                $entity->setCity($city);
                $entity->setTelephone($telephone);
                $entity->setEmail($email);
                $entity->setFax($fax);
                $entity->setContractPerson($contractPerson);

                $country = null;
                if ($country_id > 0) {
                    $country = $this->doctrineEM->find('Application\Entity\NmtApplicationCountry', $country_id);
                }

                if ($country instanceof \Application\Entity\NmtApplicationCountry) {
                    $entity->setCountry($country);
                } else {
                    $errors[] = 'Please give a country!';
                }

                /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
                $nmtPlugin = $this->Nmtplugin();
                $changeArray = $nmtPlugin->objectsAreIdentical($oldEntity, $entity);

                if (count($changeArray) == 0) {
                    $nTry ++;
                    $errors[] = sprintf('Nothing changed! n = %s', $nTry);
                }

                if ($nTry >= 3) {
                    $errors[] = sprintf('Do you really want to edit "%s (%s)"?', $entity->getVendorName(), $entity->getId());
                }

                if ($nTry == 5) {
                    $m = sprintf('You might be not ready to edit "%s (%s)". Please try later!', $entity->getVendorName(), $entity->getId());
                    $this->flashMessenger()->addMessage($m);
                    return $this->redirect()->toUrl($redirectUrl);
                }

                if (count($errors) > 0) {
                    return new ViewModel(array(
                        'errors' => $errors,
                        'redirectUrl' => $redirectUrl,
                        'entity' => $entity,
                        'n' => $nTry,
                        'gl_list' => $gl_list,
                        
                    ));
                }

                // NO ERROR
                // Saving into Database..........
                // ++++++++++++++++++++++++++++++
                
                try {

                    $changeOn = new \DateTime();
                    $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                        "email" => $this->identity()
                    ));

                    $entity->setRevisionNo($entity->getRevisionNo() + 1);
                    $entity->setLastchangeBy($u);
                    $entity->setLastchangeOn($changeOn);

                    $this->doctrineEM->persist($entity);
                    $this->doctrineEM->flush();

                    $this->vendorSearchService->updateIndex(0, $entity, false);

                    $m = sprintf('[OK] Vendor #%s - %s updated. Change No.:%s.', $entity->getId(), $entity->getVendorName(), count($changeArray));

                    // Trigger Change Log. AbtractController is EventManagerAware.
                    $this->getEventManager()->trigger('bp.change.log', __METHOD__, array(
                        'priority' => 7,
                        'message' => $m,
                        'objectId' => $entity->getId(),
                        'objectToken' => $entity->getToken(),
                        'changeArray' => $changeArray,
                        'changeBy' => $u,
                        'changeOn' => $changeOn,
                        'revisionNumber' => $entity->getRevisionNo(),
                        'changeDate' => $changeOn,
                        'changeValidFrom' => $changeOn
                    ));

                    // Trigger Activity Log . AbtractController is EventManagerAware.
                    $this->getEventManager()->trigger('bp.activity.log', __METHOD__, array(
                        'priority' => \Zend\Log\Logger::INFO,
                        'message' => $m,
                        'createdBy' => $u,
                        'createdOn' => $changeOn
                    ));

                    $this->flashMessenger()->addMessage($m);
                    return $this->redirect()->toUrl($redirectUrl);
                } catch (Exception $e) {
                    return new ViewModel(array(
                        'errors' => $e->getMessage(),
                        'redirectUrl' => $redirectUrl,
                        'entity' => $entity,
                        'n' => $nTry
                    ));
                }
            }
        }

        // NO POST
        // ========================

        $redirectUrl = null;
        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();

        $entity_id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');

        $criteria = array(
            'id' => $entity_id,
            'token' => $token
        );

        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtBpVendor')->findOneBy($criteria);
        if ($entity instanceof \Application\Entity\NmtBpVendor) {

            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'errors' => null,
                'entity' => $entity,
                'n' => 0,
                'gl_list' => $gl_list,
                
            ));
        }

        return $this->redirect()->toRoute('access_denied');
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function listAction()
    {
        $sort_criteria = array();
        $criteria = array();

        $is_active = $this->params()->fromQuery('is_active');
        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');

        if ($is_active == null) {
            $is_active = 1;
        }

        $criteria1 = array();

        if ($is_active == 1) {
            $criteria1 = array(
                "isActive" => 1
            );
        } elseif ($is_active == - 1) {
            $criteria1 = array(
                "isActive" => 0
            );
        }

        if ($sort_by == null) :
            $sort_by = "vendorName";
		endif;

        if ($sort == null) :
            $sort = "ASC";
		endif;

        $sort_criteria = array(
            $sort_by => $sort
        );

        // $criteria = array_merge ( $criteria1, $criteria2, $criteria3);
        // var_dump($criteria);
        $criteria = $criteria1;

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

        $list = $this->doctrineEM->getRepository('Application\Entity\NmtBpVendor')->findBy($criteria, $sort_criteria);
        $total_records = count($list);
        $paginator = null;

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            $list = $this->doctrineEM->getRepository('Application\Entity\NmtBpVendor')->findBy($criteria, $sort_criteria, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
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

        $sort_criteria = array(
            "vendorName" => "ASC"
        );
        $criteria = array(
            "isActive" => 1
        );

        $context = $this->params()->fromQuery('context');

        // accepted only ajax request
        if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }

        $this->layout("layout/user/ajax");
        // $list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtBpVendor' )->findAll ();
        $list = $this->doctrineEM->getRepository('Application\Entity\NmtBpVendor')->findBy($criteria, $sort_criteria);

        $total_records = count($list);
        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => null,
            'context' => $context
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function selectAction()
    {
        $request = $this->getRequest();

        // accepted only ajax request
        if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }
        $this->layout("layout/user/ajax");
        $list = $this->doctrineEM->getRepository('Application\Entity\NmtBpVendor')->findAll();
        $total_records = count($list);
        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => null
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

        $list = $this->doctrineEM->getRepository('Application\Entity\NmtBpVendor')->findBy($criteria, $sort_criteria);

        if (count($list) > 0) {
            foreach ($list as $entity) {
                $entity->setChecksum(md5(uniqid($entity->getId()) . microtime()));
                $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
            }
        }

        $this->doctrineEM->flush();

        // update search index()
        $this->vendorSearchService->createVendorIndex();
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

    /**
     *
     * @param EntityManager $doctrineEM
     * @return \BP\Controller\VendorController
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
        return $this;
    }

    public function getVendorSearchService()
    {
        return $this->vendorSearchService;
    }

    public function setVendorSearchService(VendorSearchService $vendorSearchService)
    {
        $this->vendorSearchService = $vendorSearchService;
        return $this;
    }
}
