<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Inventory\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;
use Zend\View\Model\ViewModel;
use Application\Entity\NmtInventoryItem;
use User\Model\UserTable;
use MLA\Paginator;
use Inventory\Service\ItemSearchService;
use Zend\Math\Rand;
use Application\Entity\NmtInventoryTrx;
use Zend\Validator\Date;

/*
 * Control Panel Controller
 */
class ItemTransactionController extends AbstractActionController
{

    const CHAR_LIST = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";

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
            'checksum' => $checksum,
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
    public function grAction()
    {
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            $errors = array();
            $redirectUrl = $request->getPost('redirectUrl');
            $pr_row_id = $request->getPost('pr_row_id');
            $target_id = $request->getPost('item_id');
            
            $pr_row = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow')->find($pr_row_id);
            if ($pr_row !== null) {
                $target = $pr_row->getItem();
            } else {
                $criteria = array(
                    'id' => $target_id
                );
                
                /**
                 *
                 * @todo Update Target
                 */
                $target = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->findOneBy($criteria);
            }
            
            if ($target == null) {
                
                $errors[] = 'Item or PR Row object can\'t be empty. Or token key is not valid!';
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
                
                $entity = new NmtInventoryTrx();
                
                $entity->setFlow('IN');
                $entity->setItem($target);
                
                if ($pr_row !== null) {
                    $entity->setPrRow($pr_row);
                }
                
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
        $target_id = (int) $this->params()->fromQuery('target_id');
        $pr_row_id = (int) $this->params()->fromQuery('pr_row_id');
        
        $token = $this->params()->fromQuery('token');
        $checksum = $this->params()->fromQuery('checksum');
        
        $criteria = array(
            'id' => $target_id,
            'checksum' => $checksum,
            'token' => $token
        );
        
        $criteria1 = array(
            'id' => $pr_row_id,
            'checksum' => $checksum,
            'token' => $token
        );
        
        $entity = new NmtInventoryTrx();
        
        if ($pr_row_id > 0) {
            $pr_row = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow')->findOneBy($criteria1);
            $entity->setPrRow($pr_row);
        }
        
        $target = null;
        if ($target_id > 0) {
            $target = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->findOneBy($criteria);
            $entity->setItem($target);
        }
        
        // set null
        $entity->setTrxDate(null);
        $entity->setIsActive(1);
        
        $default_wh = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->findOneBy(array(
            'isDefault' => 1
        ));
        
        if ($default_wh !== null) {
            $entity->setWh($default_wh);
        }
        
        return new ViewModel(array(
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'target' => $target
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function gr1Action()
    {
        $request = $this->getRequest();
        
        $this->layout("layout/user/ajax");
        
        if (isset($_POST['submited_form'])) {
            
            $redirectUrl = null;
            $pr_row_id = null;
            $target_id = null;
            $vendor_id = null;
            $currency_id = null;
            $quantity = null;
            $target_wh_id = null;
            $trx_date = null;
            $isDraft = null;
            $isActive = null;
            $isPreferredVendor = null;
            $conversionFactor = null;
            $vendorItemCode = null;
            $vendorItemUnit = null;
            $vendorUnitPrice = null;
            $leadTime = null;
            $remarks = null;
            
            $errors = array();
            $a_json_final = array();
            
            $submited_form = json_decode($_POST['submited_form'], true);
            $a_json_final['field_number'] = count($submited_form);
            
            foreach ($submited_form as $f) {
                
                switch ($f['name']) {
                    case 'pr_row_id':
                        $pr_row_id = (int) $f['value'];
                        break;
                    case 'target_id':
                        $target_id = (int) $f['value'];
                        break;
                    case 'vendor_id':
                        $vendor_id = (int) $f['value'];
                        break;
                    case 'currency_id':
                        $currency_id = $f['value'];
                        break;
                    case 'quantity':
                        $quantity = $f['value'];
                        break;
                    case 'target_wh_id':
                        $target_wh_id = (int) $f['value'];
                        break;
                    case 'trx_date':
                        $trx_date = $f['value'];
                        break;
                    case 'isDraft':
                        $isDraft = (int) $f['value'];
                        break;
                    case 'isActive':
                        $isActive = (int) $f['value'];
                        break;
                    case 'isPreferredVendor':
                        $isPreferredVendor = (int) $f['value'];
                        break;
                    case 'conversionFactor':
                        $conversionFactor = $f['value'];
                        break;
                    case 'vendorItemCode':
                        $vendorItemCode = $f['value'];
                        break;
                    case 'vendorItemUnit':
                        $vendorItemUnit = $f['value'];
                        break;
                    case 'vendorUnitPrice':
                        $vendorUnitPrice = $f['value'];
                        break;
                    case 'leadTime':
                        $leadTime = $f['value'];
                        break;
                    case 'remarks':
                        $remarks = $f['value'];
                        break;
                    case 'redirectUrl':
                        $redirectUrl = $f['value'];
                        break;
                }
            }
            
            $a_json_final['redirect_url'] = $redirectUrl;
            
            $pr_row = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow')->find($pr_row_id);
            if ($pr_row !== null) {
                $target = $pr_row->getItem();
            } else {
                $criteria = array(
                    'id' => $target_id
                );
                $target = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->findOneBy($criteria);
            }
            
            if ($target == null) {
                $errors[] = 'Item or PR Row object can\'t be empty. Or token key is not valid!';
            } else {
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
                $entity = new NmtInventoryTrx();
                
                $entity->setFlow('IN');
                $entity->setItem($target);
                
                if ($pr_row !== null) {
                    $entity->setPrRow($pr_row);
                }
                
                $validator = new Date();
                
                if (! $validator->isValid($trx_date)) {
                    $errors[] = 'Transaction date is not correct or empty!';
                    $entity->setTrxDate(null);
                } else {
                    $entity->setTrxDate(new \DateTime($trx_date));
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
                
                $entity->setVendorItemCode($vendorItemCode);
                
                $entity->setLeadTime($leadTime);
                // $entity->setPmtTerm();
                $entity->setRemarks($remarks);
                
                if (count($errors) > 0) {
                    $a_json_final['status'] = - 1;
                    $a_json_final['errors'] = $errors;
                    $response = $this->getResponse();
                    $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
                    $response->setContent(json_encode($a_json_final));
                    return $response;
                }
                ;
                // OK now
                $a_json_final['status'] = 1;
                $a_json_final['errors'] = $errors;
                
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
                // return $this->redirect()->toUrl($redirectUrl);
                
                $response = $this->getResponse();
                $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
                $response->setContent(json_encode($a_json_final));
                return $response;
            }
        }
        
        // NO POST
        $redirectUrl = Null;
        if ($request->getHeader('Referer') == null) {
            // return $this->redirect()->toRoute('access_denied');
        } else {
            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }
        
        $target_id = (int) $this->params()->fromQuery('target_id');
        $pr_row_id = (int) $this->params()->fromQuery('pr_row_id');
        
        $token = $this->params()->fromQuery('token');
        $checksum = $this->params()->fromQuery('checksum');
        
        $criteria = array(
            'id' => $target_id,
            'checksum' => $checksum,
            'token' => $token
        );
        
        $criteria1 = array(
            'id' => $pr_row_id,
            'checksum' => $checksum,
            'token' => $token
        );
        
        $entity = new NmtInventoryTrx();
        
        if ($pr_row_id > 0) {
            $pr_row = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow')->findOneBy($criteria1);
            $entity->setPrRow($pr_row);
        }
        
        $target = null;
        if ($target_id > 0) {
            $target = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->findOneBy($criteria);
            $entity->setItem($target);
        }
        
        // set null
        $entity->setTrxDate(null);
        $entity->setIsActive(1);
        $entity->setIsPreferredVendor(1);
        
        $default_wh = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->findOneBy(array(
            'isDefault' => 1
        ));
        
        if ($default_wh !== null) {
            $entity->setWh($default_wh);
        }
        
        $criteria=array(
            'isActive'=>1,
        );
        $sort_criteria=array(
            'currency'=>'ASC',
        );
        
        $currency_list= $this->doctrineEM->getRepository ( 'Application\Entity\NmtApplicationCurrency' )->findBy($criteria,$sort_criteria);
        
        return new ViewModel(array(
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'target' => $target,
            'currency_list' => $currency_list,
        ));
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function giAction()
    {
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            $errors = array();
            $redirectUrl = $request->getPost('redirectUrl');
            $target_id = $request->getPost('item_id');
            
            $criteria = array(
                'id' => $target_id
            );
            
            /**
             *
             * @todo Update Target
             */
            $target = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->findOneBy($criteria);
            
            if ($target == null) {
                
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
                
                $issue_for_id = $request->getPost('issue_for_id');
                
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
                
                $entity = new NmtInventoryTrx();
                
                $entity->setFlow('OUT');
                $entity->setItem($target);
                
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
                
                $issue_for = $this->doctrineEM->find('Application\Entity\NmtInventoryItem', $issue_for_id);
                if ($issue_for == null) {
                    // $errors [] = 'Curency can\'t be empty. Please select a currency!';
                } else {
                    $entity->setIssueFor($issue_for);
                }
                
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
                // $entity->setConversionText ( $entity->getVendorItemUnit () . ' = ' . $entity->getConversionFactor () . '*' . $target->getStandardUom ()->getUomCode () );
                
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
        $target_id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');
        $checksum = $this->params()->fromQuery('checksum');
        
        $criteria = array(
            'id' => $target_id,
            'checksum' => $checksum,
            'token' => $token
        );
        
        $target = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->findOneBy($criteria);
        
        return new ViewModel(array(
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => null,
            'target' => $target
        ));
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function transferAction()
    {
        $request = $this->getRequest();
        $redirectUrl = $request->getHeader('Referer')->getUri();
        
        $item_id = (int) $this->params()->fromQuery('item_id');
        
        $item = $this->doctrineEM->find('Application\Entity\NmtInventoryItem', $item_id);
        
        return new ViewModel(array(
            "item" => $item,
            "errors" => null,
            'redirectUrl' => $redirectUrl
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
    public function prRowAction()
    {
        $request = $this->getRequest();
        
        // accepted only ajax request
        
        if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        $this->layout("layout/user/ajax");
        
        $pr_row_id = (int) $this->params()->fromQuery('pr_row_id');
        
        $criteria = array(
            'prRow' => $pr_row_id
        );
        
        $sort_criteria = array(
            // 'priceValidFrom' => "DESC"
        );
        
        $list = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryTrx')->findBy($criteria, $sort_criteria);
        $total_records = count($list);
        $paginator = null;
        
        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator
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

    public function getUserTable()
    {
        return $this->userTable;
    }

    public function setUserTable($userTable)
    {
        $this->userTable = $userTable;
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
