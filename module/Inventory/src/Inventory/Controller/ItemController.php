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
use Zend\Barcode\Barcode;
use Application\Entity\NmtInventoryItem;
use Application\Entity\NmtInventoryItemPicture;
use User\Model\UserTable;
use MLA\Paginator;
use Application\Entity\NmtInventoryItemCategoryMember;
use Application\Entity\NmtInventoryItemDepartment;
use Inventory\Service\ItemSearchService;
use Zend\Math\Rand;

/*
 * Control Panel Controller
 */
class ItemController extends AbstractActionController
{

    const CHAR_LIST = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";

    protected $doctrineEM;

    protected $itemSearchService;

    protected $userTable;

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
        
        $redirectUrl = null;
        if ($request->getHeader('Referer') == null) {
            // return $this->redirect ()->toRoute ( 'access_denied' );
        } else {
            $redirectUrl = $request->getHeader('Referer')->getUri();
        }
        
        $entity_id = (int) $this->params()->fromQuery('entity_id');
        $checksum = $this->params()->fromQuery('checksum');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $entity_id,
            'checksum' => $checksum,
            'token' => $token
        );
        
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->findOneBy($criteria);
        $pictures = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemPicture')->findBy(array(
            "item" => $entity_id
        ));
        
        if (! $entity == null) {
            return new ViewModel(array(
                'entity' => $entity,
                'department' => null,
                'category' => null,
                'pictures' => $pictures,
                'redirectUrl' => $redirectUrl
            
            ));
        }
        
        return $this->redirect()->toRoute('access_denied');
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function show1Action()
    {
        $request = $this->getRequest();
        $redirectUrl = null;
        
        // accepted only ajax request
        if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }
        ;
        
        $this->layout("layout/user/ajax");
        
        $entity_id = (int) $this->params()->fromQuery('entity_id');
        $checksum = $this->params()->fromQuery('checksum');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $entity_id,
            'checksum' => $checksum,
            'token' => $token
        );
        
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->findOneBy($criteria);
        $pictures = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemPicture')->findBy(array(
            "item" => $entity_id
        ));
        
        if (! $entity == null) {
            return new ViewModel(array(
                'entity' => $entity,
                'department' => null,
                'category' => null,
                'pictures' => $pictures,
                'redirectUrl' => $redirectUrl
            
            ));
        }
        
        return $this->redirect()->toRoute('access_denied');
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function addAction()
    {
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            
            $errors = array();
            $redirectUrl = $request->getPost('redirectUrl');
            
            $itemSku = $request->getPost('itemSku');
            $itemName = $request->getPost('itemName');
            $itemNameForeign = $request->getPost('itemNameForeign');
            $itemDescription = $request->getPost('itemDescription');
            
            $barcode = $request->getPost('barcode');
            $keywords = $request->getPost('keywords');
            
            $itemType = $request->getPost('itemType');
            $monitoredBy = $request->getPost('monitoredBy');
            
            
            $item_category_id = $request->getPost('item_category_id');
            $department_id = $request->getPost('department_id');
            $standard_uom_id = $request->getPost('standard_uom_id');
            
            $leadTime = $request->getPost('leadTime');
            
            $isActive = (int) $request->getPost('isActive');
            $isFixedAsset = (int) $request->getPost('isFixedAsset');
            $isPurchased = (int) $request->getPost('isPurchased');
            $isSaleItem = (int) $request->getPost('isSaleItem');
            $isSparepart = (int) $request->getPost('isSparepart');
            $isStocked = (int) $request->getPost('isStocked');
            
            $manufacturer = $request->getPost('manufacturer');
            $manufacturerCatalog = $request->getPost('manufacturerCatalog');
            $manufacturerCode = $request->getPost('manufacturerCode');
            $manufacturerModel = $request->getPost('manufacturerModel');
            $manufacturerSerial = $request->getPost('manufacturerSerial');
            
            $location = $request->getPost('location');
            $localAvailabiliy = (int) $request->getPost('localAvailabiliy');
            
            $remarks = $request->getPost('remarks');
            
            // Create NEW ITEM
            $entity = new NmtInventoryItem();
            
            if ($itemSku === '' or $itemSku === null) {
                $errors[] = 'Please give Item ID';
            } else {
                $entity->setItemSku($itemSku);
            }
            
            if ($itemName === '' or $itemName === null) {
                $errors[] = 'Please give item name';
            } else {
                $entity->setItemName($itemName);
            }
            
            if ($isActive !== 1) {
                $isActive = 0;
            }
            
            if ($isFixedAsset !== 1) {
                $isFixedAsset = 0;
            }
            if ($isPurchased !== 1) {
                $isPurchased = 0;
            }
            if ($isSaleItem !== 1) {
                $isSaleItem = 0;
            }
            
            if ($isSparepart !== 1) {
                $isSparepart = 0;
            }
            
            if ($isStocked !== 1) {
                $isStocked = 0;
            }
            
            if ($localAvailabiliy !== 1) {
                $localAvailabiliy = 0;
            }
            $entity->setItemNameForeign($itemNameForeign);
            $entity->setItemDescription($itemDescription);
            $entity->setKeywords($keywords);
            $entity->setBarcode($barcode);
            $entity->setCompany();
            
            $entity->setIsActive($isActive);
            $entity->setIsFixedAsset($isFixedAsset);
            $entity->setIsPurchased($isPurchased);
            $entity->setIsSaleItem($isSaleItem);
            $entity->setIsSparepart($isSparepart);
            $entity->setIsStocked($isStocked);
            
            // $entity->setItemGroup ( $itemGroup );
            // $entity->setItemInternalLabel ( $itemInternalLabel );
            
            $entity->setItemType($itemType);
            $entity->setMonitoredBy($monitoredBy);
            
            $entity->setManufacturer($manufacturer);
            $entity->setManufacturerCatalog($manufacturerCatalog);
            $entity->setManufacturerCode($manufacturerCode);
            $entity->setManufacturerModel($manufacturerModel);
            $entity->setManufacturerSerial($manufacturerSerial);
            
            // $entity->setOrigin($origin);
            // $entity->setSerialNumber($serialNumber);
            
            if ($standard_uom_id === '' or $standard_uom_id === null) {
                $errors[] = 'Please give standard measurement!';
            } else {
                $uom = $this->doctrineEM->find('Application\Entity\NmtApplicationUom', $standard_uom_id);
                if ($uom !== null) {
                    $entity->setStandardUom($uom);
                }
            }
            
            $department = null;
            if ($department_id > 0) {
                $department = $this->doctrineEM->find('Application\Entity\NmtApplicationDepartment', $department_id);
            }
            
            $category = null;
            if ($item_category_id > 0) {
                $category = $this->doctrineEM->find('Application\Entity\NmtInventoryItemCategory', $item_category_id);
            }
            // $entity->setStatus($status);
            // $entity->setValidFromDate($validFromDate);
            // $entity->setValidToDate($validToDate);
            // $entity->setWarehouse ();
            
            $entity->setRemarks($remarks);
            $entity->setLocation($location);
            $entity->setLocalAvailabiliy($localAvailabiliy);
            $entity->setLeadTime($leadTime);
            
            $company_id = 1;
            $company = $this->doctrineEM->find('Application\Entity\NmtApplicationCompany', $company_id);
            $entity->setCompany($company);
            
            if (count($errors) > 0) {
                return new ViewModel(array(
                    'errors' => $errors,
                    'redirectUrl' => $redirectUrl,
                    'entity' => $entity,
                    'department' => $department,
                    'category' => $category
                ));
            }
            
            // No Error
            try {
                
                $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                    'email' => $this->identity()
                ));
                
                $entity->setCreatedOn(new \DateTime());
                $entity->setCreatedBy($u);
                $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
                
                $this->doctrineEM->persist($entity);
                $this->doctrineEM->flush();
                $new_id = $entity->getId();
                $new_item = $this->doctrineEM->find('Application\Entity\NmtInventoryItem', $new_id);
                $entity->setChecksum(md5($new_id . uniqid(microtime())));
                $this->doctrineEM->flush();
                
                // add category member
                if ($item_category_id > 0) {
                    
                    $itemCategory = $this->doctrineEM->find('Application\Entity\NmtInventoryItemCategory', $item_category_id);
                    $entity = new NmtInventoryItemCategoryMember();
                    $entity->setItem($new_item);
                    $entity->setCategory($itemCategory);
                    
                    $entity->setCreatedBy($u);
                    $entity->setCreatedOn(new \DateTime());
                    
                    $this->doctrineEM->persist($entity);
                    $this->doctrineEM->flush();
                }
                
                // add department member
                if ($department_id > 0) {
                    $entity = new NmtInventoryItemDepartment();
                    
                    $department = $this->doctrineEM->find('Application\Entity\NmtApplicationDepartment', $department_id);
                    $entity->setDepartment($department);
                    $entity->setItem($new_item);
                    
                    $entity->setCreatedBy($u);
                    $entity->setCreatedOn(new \DateTime());
                    
                    $this->doctrineEM->persist($entity);
                    $this->doctrineEM->flush();
                }
                
                // update search index.
                // $this->itemSearchService->addDocument ( $new_item, true );
                $this->itemSearchService->updateIndex(1, $new_item, false);
            } catch (Exception $e) {
                return new ViewModel(array(
                    'errors' => $e->getMessage(),
                    'redirectUrl' => $redirectUrl,
                    'entity' => $entity,
                    'department' => $department,
                    'category' => $category
                ));
            }
            
            $this->flashMessenger()->addMessage("Item " . $itemName . " has been created sucessfully");
            
            $redirectUrl = "/inventory/item/show?token=" . $new_item->getToken() . "&entity_id=" . $new_item->getId() . "&checksum=" . $new_item->getChecksum();
            return $this->redirect()->toUrl($redirectUrl);
        }
        
        $redirectUrl = null;
        if ($request->getHeader('Referer') == null) {
            // return $this->redirect ()->toRoute ( 'access_denied' );
        } else {
            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }
        return new ViewModel(array(
            'errors' => null,
            'redirectUrl' => $redirectUrl,
            'entity' => null,
            'department' => null,
            'category' => null
        ));
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function editAction()
    {
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            
            $errors = array();
            $redirectUrl = $request->getPost('redirectUrl');
            $entity_id = (int) $request->getPost('entity_id');
            $token = $request->getPost('token');
            
            $criteria = array(
                'id' => $entity_id,
                'token' => $token
            );
            
            $entity = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->findOneBy($criteria);
            
            if ($entity == null) {
                
                $errors[] = 'Entity object can\'t be empty!';
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => null,
                    'department' => null,
                    'category' => null
                ));
                
                // might need redirect
            } else {
                
                $itemSku = $request->getPost('itemSku');
                $itemName = $request->getPost('itemName');
                $itemNameForeign = $request->getPost('itemNameForeign');
                $itemDescription = $request->getPost('itemDescription');
                
                $barcode = $request->getPost('barcode');
                $keywords = $request->getPost('keywords');
                
                $itemType = $request->getPost('itemType');
                $monitoredBy = $request->getPost('monitoredBy');
                
                
                $item_category_id = $request->getPost('item_category_id');
                $department_id = $request->getPost('department_id');
                $standard_uom_id = $request->getPost('standard_uom_id');
                
                $leadTime = $request->getPost('leadTime');
                
                $isActive = (int) $request->getPost('isActive');
                $isFixedAsset = (int) $request->getPost('isFixedAsset');
                $isPurchased = (int) $request->getPost('isPurchased');
                $isSaleItem = (int) $request->getPost('isSaleItem');
                $isSparepart = (int) $request->getPost('isSparepart');
                $isStocked = (int) $request->getPost('isStocked');
                
                $manufacturer = $request->getPost('manufacturer');
                $manufacturerCatalog = $request->getPost('manufacturerCatalog');
                $manufacturerCode = $request->getPost('manufacturerCode');
                $manufacturerModel = $request->getPost('manufacturerModel');
                $manufacturerSerial = $request->getPost('manufacturerSerial');
                
                $location = $request->getPost('location');
                $localAvailabiliy = (int) $request->getPost('localAvailabiliy');
                
                $remarks = $request->getPost('remarks');
                
                // Create NEW ITEM
                // $entity = new NmtInventoryItem ();
                
                if ($itemSku === '' or $itemSku === null) {
                    $errors[] = 'Please give Item ID';
                } else {
                    $entity->setItemSku($itemSku);
                }
                
                if ($itemName === '' or $itemName === null) {
                    $errors[] = 'Please give item name';
                } else {
                    $entity->setItemName($itemName);
                }
                
                if ($isActive !== 1) {
                    $isActive = 0;
                }
                
                if ($isFixedAsset !== 1) {
                    $isFixedAsset = 0;
                }
                if ($isPurchased !== 1) {
                    $isPurchased = 0;
                }
                if ($isSaleItem !== 1) {
                    $isSaleItem = 0;
                }
                
                if ($isSparepart !== 1) {
                    $isSparepart = 0;
                }
                
                if ($isStocked !== 1) {
                    $isStocked = 0;
                }
                
                if ($localAvailabiliy !== 1) {
                    $localAvailabiliy = 0;
                }
                
                $entity->setItemNameForeign($itemNameForeign);
                $entity->setItemDescription($itemDescription);
                $entity->setKeywords($keywords);
                $entity->setBarcode($barcode);
                $entity->setCompany();
                
                $entity->setIsActive($isActive);
                $entity->setIsFixedAsset($isFixedAsset);
                $entity->setIsPurchased($isPurchased);
                $entity->setIsSaleItem($isSaleItem);
                $entity->setIsSparepart($isSparepart);
                $entity->setIsStocked($isStocked);
                
                // $entity->setItemGroup ( $itemGroup );
                // $entity->setItemInternalLabel ( $itemInternalLabel );
                
                $entity->setItemType($itemType);
                $entity->setMonitoredBy($monitoredBy);
                
                
                $entity->setManufacturer($manufacturer);
                $entity->setManufacturerCatalog($manufacturerCatalog);
                $entity->setManufacturerCode($manufacturerCode);
                $entity->setManufacturerModel($manufacturerModel);
                $entity->setManufacturerSerial($manufacturerSerial);
                
                // $entity->setOrigin($origin);
                // $entity->setSerialNumber($serialNumber);
                
                if ($standard_uom_id === '' or $standard_uom_id === null) {
                    $errors[] = 'Please give standard measurement!';
                } else {
                    $uom = $this->doctrineEM->find('Application\Entity\NmtApplicationUom', $standard_uom_id);
                    if ($uom !== null) {
                        $entity->setStandardUom($uom);
                    }
                }
                
                $department = null;
                if ($department_id > 0) {
                    $department = $this->doctrineEM->find('Application\Entity\NmtApplicationDepartment', $department_id);
                }
                
                $category = null;
                if ($item_category_id > 0) {
                    $category = $this->doctrineEM->find('Application\Entity\NmtInventoryItemCategory', $item_category_id);
                }
                // $entity->setStatus($status);
                // $entity->setValidFromDate($validFromDate);
                // $entity->setValidToDate($validToDate);
                // $entity->setWarehouse ();
                
                $entity->setRemarks($remarks);
                $entity->setLocation($location);
                $entity->setLocalAvailabiliy($localAvailabiliy);
                $entity->setLeadTime($leadTime);
                
                $company_id = 1;
                $company = $this->doctrineEM->find('Application\Entity\NmtApplicationCompany', $company_id);
                $entity->setCompany($company);
                
                if (count($errors) > 0) {
                    return new ViewModel(array(
                        'errors' => $errors,
                        'redirectUrl' => $redirectUrl,
                        'entity' => $entity,
                        'department' => $department,
                        'category' => $category
                    ));
                }
                
                // No Error
                try {
                    
                    $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                        'email' => $this->identity()
                    ));
                    
                    $entity->setLastChangeOn(new \DateTime());
                    $entity->setLastChangeBy($u);
                    $this->doctrineEM->flush();
                    
                    $new_item = $entity;
                    
                    // add category member
                    if ($item_category_id > 0) {
                        $entity = new NmtInventoryItemCategoryMember();
                        $entity->setItem($new_item);
                        $entity->setCategory($category);
                        
                        $entity->setCreatedBy($u);
                        $entity->setCreatedOn(new \DateTime());
                        
                        $this->doctrineEM->persist($entity);
                        $this->doctrineEM->flush();
                    }
                    
                    // add department member
                    if ($department_id > 0) {
                        $entity = new NmtInventoryItemDepartment();
                        $entity->setDepartment($department);
                        $entity->setItem($new_item);
                        
                        $entity->setCreatedBy($u);
                        $entity->setCreatedOn(new \DateTime());
                        
                        $this->doctrineEM->persist($entity);
                        $this->doctrineEM->flush();
                    }
                    
                    /**
                     *
                     * @todo : update search index.
                     */
                    $this->itemSearchService->updateIndex(0, $new_item, false);
                } catch (Exception $e) {
                    return new ViewModel(array(
                        'errors' => $e->getMessage(),
                        'redirectUrl' => $redirectUrl,
                        'entity' => $entity,
                        'department' => $department,
                        'category' => $category
                    
                    ));
                }
                
                $this->flashMessenger()->addMessage("Item " . $itemName . " has been updated sucessfully");
                return $this->redirect()->toUrl($redirectUrl);
            }
        }
        
        // NO POST
        $redirectUrl = null;
        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        } else {
            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }
        
        $entity_id = (int) $this->params()->fromQuery('entity_id');
        $checksum = $this->params()->fromQuery('checksum');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $entity_id,
            'checksum' => $checksum,
            'token' => $token
        );
        
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->findOneBy($criteria);
        
        if (! $entity == null) {
            return new ViewModel(array(
                'errors' => null,
                'redirectUrl' => $redirectUrl,
                'entity' => $entity,
                'category' => null,
                'department' => null
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
        
        $item_type = $this->params()->fromQuery('item_type');
        $is_active = (int) $this->params()->fromQuery('is_active');
        $is_fixed_asset = (int) $this->params()->fromQuery('is_fixed_asset');
        
        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');
        
        $criteria1 = array();
        if (! $item_type == null) {
            $criteria1 = array(
                "itemType" => $item_type
            );
        }
        
        if ($is_active == null) {
            $is_active = 1;
        }
        
        $criteria2 = array();
        
        if ($is_active == 1) {
            $criteria2 = array(
                "isActive" => 1
            );
        } elseif ($is_active == - 1) {
            $criteria2 = array(
                "isActive" => 0
            );
        }
        
        $criteria3 = array();
        if (! $is_fixed_asset == '') {
            $criteria3 = array(
                "isFixedAsset" => $is_fixed_asset
            );
            
            if ($is_fixed_asset == - 1) {
                $criteria3 = array(
                    "isFixedAsset" => 0
                );
            }
        }
        
        if ($sort_by == null) :
            $sort_by = "createdOn";		
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
        
        // $list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItem' )->findBy ( $criteria, $sort_criteria );
        $total_records = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->getTotalItem($item_type, $is_active, $is_fixed_asset);
        // echo($total_records);
        
        // $total_records =count($list);
        
        $paginator = null;
        
        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            // $list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItem' )->findBy ( $criteria, $sort_criteria, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
            // $list = array_slice($list, $paginator->minInPage - 1, ($paginator->maxInPage - $paginator->minInPage) + 1);
            $list = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->getItems($item_type, $is_active, $is_fixed_asset, $sort_by, $sort, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
        } else {
            $list = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->getItems($item_type, $is_active, $is_fixed_asset, $sort_by, $sort, 0, 0);
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
    public function itemPriceAction()
    {
        $sort_criteria = array();
        $criteria = array();
        
        $item_type = $this->params()->fromQuery('item_type');
        $is_active = (int) $this->params()->fromQuery('is_active');
        $is_fixed_asset = (int) $this->params()->fromQuery('is_fixed_asset');
        
        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');
        
        $criteria1 = array();
        if (! $item_type == null) {
            $criteria1 = array(
                "itemType" => $item_type
            );
        }
        
        if ($is_active == null) {
            $is_active = 1;
        }
        
        $criteria2 = array();
        
        if ($is_active == 1) {
            $criteria2 = array(
                "isActive" => 1
            );
        } elseif ($is_active == - 1) {
            $criteria2 = array(
                "isActive" => 0
            );
        }
        
        $criteria3 = array();
        if (! $is_fixed_asset == '') {
            $criteria3 = array(
                "isFixedAsset" => $is_fixed_asset
            );
            
            if ($is_fixed_asset == - 1) {
                $criteria3 = array(
                    "isFixedAsset" => 0
                );
            }
        }
        
        if ($sort_by == null) :
            $sort_by = "createdOn";
	    endif;
        
        if ($sort == null) :
            $sort = "ASC";
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
        
        $list = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->getItemPrice($item_type, $is_active, $is_fixed_asset, $sort_by, $sort, 0, 0);
        // $total_records = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItem' )->getTotalItem($item_type, $is_active, $is_fixed_asset);
        // echo($total_records);
        
        //$total_records = count($list);
        $total_records = 100;
        
        $paginator = null;
        
        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            // $list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItem' )->findBy ( $criteria, $sort_criteria, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
            // $list = array_slice($list, $paginator->minInPage - 1, ($paginator->maxInPage - $paginator->minInPage) + 1);
            $list = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->getItemPrice($item_type, $is_active, $is_fixed_asset, $sort_by, $sort, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
        }else{
            $list = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->getItemPrice($item_type, $is_active, $is_fixed_asset, $sort_by, $sort, 0, 0);
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
     * @return \Zend\Stdlib\ResponseInterface|\Zend\View\Model\ViewModel
     */
    public function uploadPictureAction()
    {
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            
            $pictures = $_POST['pictures'];
            $id = $_POST['target_id'];
            $documentSubject = $_POST['subject'];
            
            $result = array();
            $success = 0;
            $failed = 0;
            $n = 0;
            
            foreach ($pictures as $p) {
                $n ++;
                $filetype = $p[0];
                $original_filename = $p[2];
                
                if (preg_match('/(jpg|jpeg)$/', $filetype)) {
                    $ext = 'jpg';
                } else if (preg_match('/(gif)$/', $filetype)) {
                    $ext = 'gif';
                } else if (preg_match('/(png)$/', $filetype)) {
                    $ext = 'png';
                }
                
                $tmp_name = md5($id . uniqid(microtime())) . '.' . $ext;
                
                // remove "data:image/png;base64,"
                $uri = substr($p[1], strpos($p[1], ",") + 1);
                
                // save to file
                file_put_contents($tmp_name, base64_decode($uri));
                
                $checksum = md5_file($tmp_name);
                
                $root_dir = ROOT . "/data/inventory/picture/item/";
                
                $criteria = array(
                    "checksum" => $checksum,
                    "item" => $id
                );
                
                $ck = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemPicture')->findby($criteria);
                
                if (count($ck) == 0) {
                    try {
                        $name_part1 = Rand::getString(6, self::CHAR_LIST, true) . "_" . Rand::getString(10, self::CHAR_LIST, true);
                        $name = md5($id . $checksum . uniqid(microtime())) . '_' . $name_part1 . '.' . $ext;
                        
                        $folder_relative = $name[0] . $name[1] . DIRECTORY_SEPARATOR . $name[2] . $name[3] . DIRECTORY_SEPARATOR . $name[4] . $name[5];
                        
                        $folder = $root_dir . DIRECTORY_SEPARATOR . $folder_relative;
                        
                        if (! is_dir($folder)) {
                            mkdir($folder, 0777, true); // important
                        }
                        
                        rename($tmp_name, "$folder/$name");
                        
                        $entity = new NmtInventoryItemPicture();
                        
                        if ($documentSubject == null) {
                            $documentSubject = "Picture for " . $id;
                        }
                        $entity->setDocumentSubject($documentSubject);
                        $entity->setIsActive(1);
                        
                        $entity->setUrl($folder . DIRECTORY_SEPARATOR . $name);
                        $entity->setFiletype($filetype);
                        $entity->setFilename($name);
                        $entity->setOriginalFilename($original_filename);
                        $entity->setFolder($folder);
                        $entity->setFolderRelative($folder_relative . DIRECTORY_SEPARATOR);
                        
                        $entity->setChecksum($checksum);
                        $entity->setVisibility(1);
                        $item = $this->doctrineEM->find('Application\Entity\NmtInventoryItem', $id);
                        $entity->setItem($item);
                        
                        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                            "email" => $this->identity()
                        ));
                        
                        $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
                        $entity->setCreatedBy($u);
                        $entity->setCreatedOn(new \DateTime());
                        
                        $this->doctrineEM->persist($entity);
                        $this->doctrineEM->flush();
                        
                        // trigger uploadPicture. AbtractController is EventManagerAware.
                        $this->getEventManager()->trigger('uploadPicture', __CLASS__, array(
                            'picture_name' => $name,
                            'pictures_dir' => $folder
                        ));
                        
                        $this->flashMessenger()->addMessage("'" . $original_filename . "' has been uploaded sucessfully");
                        $result[] = $original_filename . ' uploaded sucessfully';
                        $success ++;
                    } catch (Exception $e) {
                        $this->flashMessenger()->addMessage($e);
                        $result[] = $e;
                        $failed ++;
                    }
                } else {
                    $this->flashMessenger()->addMessage("'" . $original_filename . "' exits already!");
                    $result[] = $original_filename . ' exits already. Please select other file!';
                    $failed ++;
                }
            }
            // $data['filetype'] = $filetype;
            $data = array();
            $data['message'] = $result;
            $data['total_uploaded'] = $n;
            $data['success'] = $success;
            $data['failed'] = $failed;
            
            $response = $this->getResponse();
            $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            return $response;
        }
        
        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();
        
        $target_id = (int) $this->params()->fromQuery('target_id');
        $checksum = $this->params()->fromQuery('checksum');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $target_id,
            'checksum' => $checksum,
            'token' => $token
        );
        
        $target = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->findOneBy($criteria);
        
        if ($target !== null) {
            return new ViewModel(array(
                'item' => $target,
                'redirectUrl' => $redirectUrl,
                'errors' => null
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     *
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function getPictureAction()
    {
        /*
         * $request = $this->getRequest ();
         *
         * // accepted only ajax request
         * if (!$request->isXmlHttpRequest ()) {
         * return $this->redirect ()->toRoute ( 'access_denied' );
         * }
         */
        $item_id = (int) $this->params()->fromQuery('item_id');
        $pic1 = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemPicture')->findOneBy(array(
            'item' => $item_id
        ));
        
        if ($pic1 instanceof NmtInventoryItemPicture) {
            
            $pic = new NmtInventoryItemPicture();
            $pic = $pic1;
            $pic_folder = getcwd() . "/data/inventory/picture/item/" . $pic->getFolderRelative() . "thumbnail_450_" . $pic->getFileName();
            $imageContent = file_get_contents($pic_folder);
            
            $response = $this->getResponse();
            
            $response->setContent($imageContent);
            $response->getHeaders()
                ->addHeaderLine('Content-Transfer-Encoding', 'binary')
                ->addHeaderLine('Content-Type', $pic->getFiletype())
                ->addHeaderLine('Content-Length', mb_strlen($imageContent));
            return $response;
        } else {
            $pic_folder = getcwd() . "/public/images/no-pic.jpg";
            $imageContent = file_get_contents($pic_folder);
            
            $response = $this->getResponse();
            
            $response->setContent($imageContent);
            $response->getHeaders()
                ->addHeaderLine('Content-Transfer-Encoding', 'binary')
                ->addHeaderLine('Content-Type', 'image/jpeg')
                ->addHeaderLine('Content-Length', mb_strlen($imageContent));
            return $response;
        }
    }

    /**
     */
    public function barcodeAction()
    {
        $barcode = $this->params()->fromQuery('barcode');
        
        if ($barcode !== "") {
            // Only the text to draw is required
            $barcodeOptions = array(
                'text' => $barcode
            );
            
            // No required options
            $rendererOptions = array();
            
            // Draw the barcode in a new image,
            Barcode::factory('code39', 'image', $barcodeOptions, $rendererOptions)->render();
        } else {
            return;
        }
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
     * @return \Zend\View\Model\ViewModel
     */
    public function updatePictureTokenAction()
    {
        $criteria = array();
        $sort_criteria = array();
        
        $list = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemPicture')->findBy($criteria, $sort_criteria);
        
        if (count($list) > 0) {
            foreach ($list as $entity) {
                /**
                 *
                 * @todo ONLY TOKEN
                 */
                $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
            }
        }
        
        $this->doctrineEM->flush();
        
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

    public function getUserTable() {
		return $this->userTable;
	}
	public function setUserTable($userTable) {
		$this->userTable = $userTable;
		return $this;
	}
	public function getItemSearchService() {
		return $this->itemSearchService;
	}
	public function setItemSearchService(ItemSearchService $itemSearchService) {
		$this->itemSearchService = $itemSearchService;
		return $this;
	}
}
