<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Inventory\Controller;

use Doctrine\ORM\EntityManager;
use Inventory\Service\ItemSearchService;
use MLA\Paginator;
use Zend\Math\Rand;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Date;
use Zend\View\Model\ViewModel;

/**
 *
 * @author nmt
 *        
 */
class BatchNumberController extends AbstractActionController
{

    const CHAR_LIST = "__0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";

    protected $doctrineEM;

    protected $itemSearchService;

    /*
     * Defaul Action
     */
    public function indexAction()
    {}

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
            $entity_id = $request->getPost('entity_id');
            $token = $request->getPost('token');
            
            $criteria = array(
                'id' => $entity_id,
                'token' => $token
            );
            
            /**
             *
             * @todo Update Target
             */
            
            /** @var \Application\Entity\NmtInventoryTrx $entity */
            $entity = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryTrx')->findOneBy($criteria);
            
            if ($entity == null) {
                
                $errors[] = 'Target object can\'t be empty. Or token key is not valid!';
                $this->flashMessenger()->addMessage('Something went wrong!');
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'target' => null,
                    'entity' => null
                ));
                
                // might need redirect
            } else {
                $vendor_id = $request->getPost('vendor_id');
                $currency_id = $request->getPost('currency_id');
                // $pmt_method_id = $request->getPost ( 'pmt_method_id' );
                
                $quantity = $request->getPost('quantity');
                $target_wh_id = $request->getPost('target_wh_id');
                $trx_date = $request->getPost('trx_date');
                $isDraft = (int) $request->getPost('isDraft');
                $isActive = (int) $request->getPost('isActive');
                
                $isPreferredVendor = (int) $request->getPost('isPreferredVendor');
                
                $conversionFactor = $request->getPost('conversionFactor');
                $vendorItemCode = $request->getPost('vendorItemCode');
                $vendorItemUnit = $request->getPost('vendorItemUnit');
                $vendorUnitPrice = $request->getPost('vendorUnitPrice');
                $leadTime = $request->getPost('leadTime');
                
                $remarks = $request->getPost('remarks');
                
                if ($isDraft !== 1) {
                    $isDraft = 0;
                }
                
                if ($isActive !== 1) {
                    $isActive = 0;
                }
                
                if ($isPreferredVendor !== 1) {
                    $isPreferredVendor = 0;
                }
                
                // Inventory Transaction:
                
                // $entity = new NmtInventoryTrx ();
                $target = $entity->getItem();
                
                // $entity->setFlow ( 'IN' );
                // $entity->setItem ( $target );
                
                $validator = new Date();
                if (! $validator->isValid($trx_date)) {
                    $errors[] = 'Transaction date is not correct or empty!';
                    $entity->setTrxDate(null);
                } else {
                    $entity->setTrxDate(new \DateTime($trx_date));
                    // $date_validated ++;
                }
                
                $wh = $this->doctrineEM->find('Application\Entity\NmtInventoryWarehouse', $target_wh_id);
                if ($wh == null) {
                    $errors[] = 'Warehouse can\'t be empty. Please select a Warehouse!';
                } else {
                    $entity->setWH($wh);
                }
                
                if ($quantity == null) {
                    $errors[] = 'Please  enter quantity!';
                } else {
                    
                    if (! is_numeric($quantity)) {
                        $errors[] = 'Quantity must be a number.';
                    } else {
                        if ($quantity <= 0) {
                            $errors[] = 'Quantity must be greater than 0!';
                        }
                        $entity->setQuantity($quantity);
                    }
                }
                
                $entity->setIsDraft($isDraft);
                $entity->setIsActive($isActive);
                
                $entity->setIsPreferredVendor($isPreferredVendor);
                
                $vendor = $this->doctrineEM->find('Application\Entity\NmtBpVendor', $vendor_id);
                
                if ($vendor == null) {
                    // $errors [] = 'Vendor can\'t be empty. Please select a vendor!';
                } else {
                    $entity->setVendor($vendor);
                }
                
                if ($vendorItemUnit == null) {
                    // $errors [] = 'Please enter unit of purchase';
                } else {
                    $entity->setVendorItemUnit($vendorItemUnit);
                }
                
                if ($conversionFactor == null) {
                    // $errors [] = 'Please enter conversion factor';
                } else {
                    
                    if (! is_numeric($conversionFactor)) {
                        $errors[] = 'converstion_factor must be a number.';
                    } else {
                        if ($conversionFactor <= 0) {
                            $errors[] = 'converstion_factor must be greater than 0!';
                        }
                        $entity->setConversionFactor($conversionFactor);
                    }
                }
                
                if ($vendorUnitPrice !== null) {
                    if (! is_numeric($vendorUnitPrice)) {
                        // $errors [] = 'Price is not valid. It must be a number.';
                    } else {
                        if ($vendorUnitPrice <= 0) {
                            $errors[] = 'Price must be greate than 0!';
                        }
                        $entity->setVendorUnitPrice($vendorUnitPrice);
                    }
                }
                
                $currency = $this->doctrineEM->find('Application\Entity\NmtApplicationCurrency', $currency_id);
                if ($currency == null) {
                    // $errors [] = 'Curency can\'t be empty. Please select a currency!';
                } else {
                    $entity->setCurrency($currency);
                }
                
                /*
                 * $pmt_method = $this->doctrineEM->find ( 'Application\Entity\NmtApplicationPmtMethod', $pmt_method_id );
                 * if (! $pmt_method == null) {
                 * $entity->setPmtMethod ( $pmt_method );
                 * }
                 */
                
                /*
                 * $date_validated = 0;
                 * $validator = new Date ();
                 * if (! $validator->isValid ( $priceValidFrom )) {
                 * $errors [] = 'Start date is not correct or empty!';
                 * } else {
                 * $entity->setPriceValidFrom ( new \DateTime ( $priceValidFrom ) );
                 * $date_validated ++;
                 * }
                 *
                 * if ($priceValidTo !== "") {
                 * if (! $validator->isValid ( $priceValidTo )) {
                 * $errors [] = 'End Date to is not correct or empty!';
                 * } else {
                 * $entity->setPriceValidTo ( new \DateTime ( $priceValidTo ) );
                 * $date_validated ++;
                 * }
                 *
                 * if ($date_validated == 2) {
                 *
                 * if ($priceValidFrom > $priceValidTo) {
                 * $errors [] = 'End date must be in the future!';
                 * }
                 * }
                 * }
                 */
                $entity->setVendorItemCode($vendorItemCode);
                
                $entity->setLeadTime($leadTime);
                // $entity->setPmtTerm();
                $entity->setRemarks($remarks);
                
                if (count($errors) > 0) {
                    
                    return new ViewModel(array(
                        'redirectUrl' => $redirectUrl,
                        'errors' => $errors,
                        'target' => $target,
                        'entity' => $entity
                    ));
                }
                ;
                // OK now
                $entity->setConversionText($entity->getVendorItemUnit() . ' = ' . $entity->getConversionFactor() . '*' . $target->getStandardUom()
                    ->getUomCode());
                
                $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                    'email' => $this->identity()
                ));
                
                $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
                
                $entity->setCreatedBy($u);
                $entity->setCreatedOn(new \DateTime());
                $this->doctrineEM->persist($entity);
                $this->doctrineEM->flush();
                $new_entity_id = $entity->getId();
                
                $entity->setChecksum(md5($new_entity_id . uniqid(microtime())));
                $this->doctrineEM->flush();
                
                $this->flashMessenger()->addMessage($quantity . ' of Item "' . $target->getItemName() . '" has been received successfully!');
                return $this->redirect()->toUrl($redirectUrl);
            }
        }
        
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
            'checksum' => $checksum,
            'token' => $token
        );
        
        /** @var \Application\Entity\NmtInventoryTrx $entity */
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryTrx')->findOneBy($criteria);
        
        if ($entity !== null) {
            
            // check if posting is close
            /** @var \Application\Repository\NmtFinPostingPeriodRepository $p */
            $p = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod');
            
            /** @var \Application\Entity\NmtFinPostingPeriod $postingPeriod */
            $postingPeriod = $p->getPostingPeriod($entity->getTrxDate());
            
            if ($postingPeriod->getPeriodStatus() == "C") {
                $this->flashMessenger()->addMessage("Period :'" . $postingPeriod->getPeriodName() . "' is closed. Can't change!");
                return $this->redirect()->toUrl('/inventory/item-transaction/show?token=' . $token . '&entity_id=' . $entity_id);
            }
            
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
    public function listAction()
    {
        $item_type = $this->params()->fromQuery('item_type');
        $is_active = (int) $this->params()->fromQuery('is_active');
        $is_fixed_asset = $this->params()->fromQuery('is_fixed_asset');
        
        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');
        
        $criteria1 = array();
        /*
         * if (! $item_type == null) {
         * $criteria1 = array (
         * "itemType" => $item_type
         * );
         * }
         */
        $criteria2 = array();
        if (! $is_active == null) {
            $criteria2 = array(
                "isActive" => $is_active
            );
            
            if ($is_active == - 1) {
                $criteria2 = array(
                    "isActive" => '0'
                );
            }
        }
        
        $criteria3 = array();
        
        if ($sort_by == null) :
            $sort_by = "trxDate";
        endif;
        
        if ($sort == null) :
            $sort = "DESC";
        endif;
        
        $sort_criteria = array(
            $sort_by => $sort
        );
        
        $criteria = array_merge($criteria1, $criteria2, $criteria3);
        
        // var_dump($criteria);
        
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
        
        // $list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItemPurchasing' )->findBy ( $criteria, $sort_criteria );
        
        $query = 'SELECT e, i FROM Application\Entity\NmtInventoryTrx e JOIN e.item i JOIN e.vendor v Where 1=?1';
        
        if ($is_active == 0) {
            $is_active = 1;
        }
        
        if ($is_active == - 1) {
            $query = $query . " AND e.isActive = 0";
        } elseif ($is_active == 1) {
            $query = $query . " AND e.isActive = 1";
        }
        
        switch ($sort_by) {
            case "itemName":
                $query = $query . ' ORDER BY i.' . $sort_by . ' ' . $sort . ' ,e.currency';
                break;
            case "vendorName":
                $query = $query . ' ORDER BY v.' . $sort_by . ' ' . $sort . ' ,e.currency';
                break;
            case "trxDate":
                $query = $query . ' ORDER BY e.' . $sort_by . ' ' . $sort . ' ,e.currency';
                break;
        }
        
        $list = $this->doctrineEM->createQuery($query)
            ->setParameters(array(
            "1" => 1
        ))
            ->getResult();
        
        $total_records = count($list);
        $paginator = null;
        
        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            $list = $this->doctrineEM->createQuery($query)
                ->setParameters(array(
                "1" => 1
            ))
                ->setFirstResult($paginator->minInPage - 1)
                ->setMaxResults(($paginator->maxInPage - $paginator->minInPage) + 1)
                ->getResult();
        }
        
        // $all = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItem' )->getAllItem();
        // var_dump (count($all));
        
        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator,
            'sort_by' => $sort_by,
            'sort' => $sort,
            'is_active' => $is_active,
            'is_fixed_asset' => $is_fixed_asset,
            'per_pape' => $resultsPerPage,
            'item_type' => $item_type
        
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
