<?php

namespace Procure\Controller;

use Zend\Escaper\Escaper;
use Zend\Math\Rand;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Date;
use Zend\View\Model\ViewModel;
use Doctrine\ORM\EntityManager;
use Application\Domain\Util\Pagination\Paginator;
use Application\Entity\NmtProcurePo;
use Application\Entity\NmtProcurePoRow;
use Application\Entity\NmtInventoryTrx;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * Good Receipt PO or PR or AP
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ReturnRowController extends AbstractActionController
{

    const CHAR_LIST = "_0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";

    protected $doctrineEM;


    /**
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function addAction()
    {
        $this->layout("Procure/layout-fullscreen");
        
        $criteria = array(
            'isActive' => 1
        );
        $sort_criteria = array(
            'currency' => 'ASC'
        );
        
        $currency_list = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->findBy($criteria, $sort_criteria);
        
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            $errors = array();
            $redirectUrl = $request->getPost('redirectUrl');
            $po_id = $request->getPost('po_id');
            $po_token = $request->getPost('po_token');
            
            /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
            $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
            $po = $res->getPo($po_id, $po_token);
            
            if ($po == null) {
                return $this->redirect()->toRoute('access_denied');
            }
            
            $target = null;
            if ($po[0] instanceof NmtProcurePo) {
                
                /**@var \Application\Entity\NmtProcurePo $target ;*/
                $target = $po[0];
            }
            
            if ($target == null) {
                
                $errors[] = 'Contract /PO object can\'t be empty. Or token key is not valid!';
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'target' => null,
                    'entity' => null,
                    'currency_list' => $currency_list
                ));
                // might need redirect
            } else {
                
                $item_id = $request->getPost('item_id');
                $pr_row_id = $request->getPost('pr_row_id');
                $isActive = (int) $request->getPost('isActive');
                
                $rowNumber = $request->getPost('rowNumber');
                
                $vendorItemCode = $request->getPost('vendorItemCode');
                $unit = $request->getPost('unit');
                $conversionFactor = $request->getPost('conversionFactor');
                $converstionText = $request->getPost('converstionText');
                
                $quantity = $request->getPost('quantity');
                $unitPrice = $request->getPost('unitPrice');
                $taxRate = $request->getPost('taxRate');
                $traceStock = $request->getPost('traceStock');
                
                $remarks = $request->getPost('remarks');
                
                if ($isActive !== 1) {
                    $isActive = 0;
                }
                
                // Inventory Transaction:
                
                $entity = new NmtProcurePoRow();
                $entity->setIsActive($isActive);
                
                $entity->setPo($target);
                $entity->setRowNumber($rowNumber);
                
                $pr_row = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow')->find($pr_row_id);
                $item = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->find($item_id);
                
                if ($pr_row == null) {
                    // $errors[] = 'Item can\'t be empty!';
                } else {
                    $entity->setPrRow($pr_row);
                }
                
                if ($item == null) {
                    $errors[] = 'Item can\'t be empty!';
                } else {
                    $entity->setItem($item);
                }
                
                $entity->setVendorItemCode($vendorItemCode);
                $entity->setUnit($unit);
                $entity->setConversionFactor($conversionFactor);
                $entity->setConverstionText($converstionText);
                
                $n_validated = 0;
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
                        $n_validated ++;
                    }
                }
                
                if ($unitPrice !== null) {
                    if (! is_numeric($unitPrice)) {
                        $errors[] = 'Price is not valid. It must be a number.';
                    } else {
                        if ($unitPrice <= 0) {
                            $errors[] = 'Price must be greate than 0!';
                        }
                        $entity->setUnitPrice($unitPrice);
                        $n_validated ++;
                    }
                }
                
                if ($n_validated == 2) {
                    $netAmount = $entity->getQuantity() * $entity->getUnitPrice();
                    $entity->setNetAmount($netAmount);
                }
                
                if ($taxRate !== null) {
                    if (! is_numeric($taxRate)) {
                        $errors[] = '$taxRate is not valid. It must be a number.';
                    } else {
                        if ($taxRate < 0) {
                            $errors[] = '$taxRate must be greate than 0!';
                        }
                        $entity->setTaxRate($taxRate);
                        $n_validated ++;
                    }
                }
                
                if ($n_validated == 3) {
                    $taxAmount = $entity->getNetAmount() * $entity->getTaxRate() / 100;
                    $grossAmount = $entity->getNetAmount() + $taxAmount;
                    $entity->setTaxAmount($taxAmount);
                    $entity->setGrossAmount($grossAmount);
                }
                
                // $entity->setTraceStock($traceStock);
                $entity->setRemarks($remarks);
                
                if (count($errors) > 0) {
                    
                    return new ViewModel(array(
                        'redirectUrl' => $redirectUrl,
                        'errors' => $errors,
                        'target' => $target,
                        'entity' => $entity,
                        'currency_list' => $currency_list
                    
                    ));
                }
                ;
                // OK now
                
                $n = $po['total_row'] + 1;
                $rowIdentifer = $target->getSysNumber() . "-$n";
                $entity->setRowIdentifer($rowIdentifer);
                
                $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                    'email' => $this->identity()
                ));
                
                $entity->setCurrentState($target->getCurrentState());
                $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
                
                $entity->setCreatedBy($u);
                $entity->setCreatedOn(new \DateTime());
                $this->doctrineEM->persist($entity);
                $this->doctrineEM->flush();
                
                /*
                 * $gr_entity = new NmtInventoryTrx();
                 * $gr_entity->setVendor($target->getVendor());
                 * $gr_entity->setFlow('IN');
                 * $gr_entity->setInvoiceRow($entity);
                 * $gr_entity->setItem($entity->getItem());
                 * $gr_entity->setPrRow($entity->getPrRow());
                 * $gr_entity->setQuantity($quantity);
                 * $gr_entity->setVendorItemCode($entity->getVendorItemCode());
                 * $gr_entity->setVendorItemUnit($entity->getUnit());
                 * $gr_entity->setVendorUnitPrice($entity->getUnitPrice());
                 * $gr_entity->setTrxDate($target->getGrDate());
                 * $gr_entity->setCurrency($target->getCurrency());
                 * $gr_entity->setRemarks("Auto:");
                 * $gr_entity->setWh($target->getWarehouse());
                 * $gr_entity->setCreatedBy($u);
                 * $gr_entity->setCreatedOn(new \DateTime());
                 * $gr_entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
                 * $gr_entity->setChecksum(Rand::getString(32, self::CHAR_LIST, true));
                 *
                 * $gr_entity->setTaxRate($entity->getTaxRate());
                 *
                 * $gr_entity->setCurrentState($target->getCurrentState());
                 *
                 * if ($target->getCurrentState() == "finalInvoice") {
                 * $gr_entity->setIsActive(1);
                 * } else {
                 * $gr_entity->setIsActive(0);
                 * }
                 *
                 * $this->doctrineEM->persist($gr_entity);
                 * $this->doctrineEM->flush();
                 */
                $redirectUrl = "/procure/po-row/add?token=" . $target->getToken() . "&target_id=" . $target->getId();
                $m = sprintf("[OK] Contract /PO Line: %s created!",  $rowIdentifer);
                $this->flashMessenger()->addMessage($m);
                
                
                return $this->redirect()->toUrl($redirectUrl);
            }
        }
        
        // NO POST ==========================
        $redirectUrl = Null;
        
        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();
        
        $id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');
        
        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
        $po = $res->getPo($id, $token);
        
        if ($po == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        $target = null;
        if ($po[0] instanceof NmtProcurePo) {
            $target = $po[0];
        }
        
        if ($target == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        $entity = new NmtProcurePoRow();
        
        // set null
        $entity->setIsActive(1);
        $entity->setConversionFactor(1);
        $entity->setUnit("each");
        
        return new ViewModel(array(
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'target' => $target,
            'currency_list' => $currency_list,
            'total_row' => $po['total_row'],
            'max_row_number' => $po['total_row'],
            'net_amount' => $po['net_amount'],
            'tax_amount' => $po['tax_amount'],
            'gross_amount' => $po['gross_amount']
        ));
    }

    /**
     */
    public function showAction()
    {
        $request = $this->getRequest();
        
        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();
        
        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $id,
            'token' => $token
        );
        
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod')->findOneBy($criteria);
        if ($entity !== null) {
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'entity' => $entity,
                'errors' => null
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function processAction()
    {
        $request = $this->getRequest();
        
        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();
        
        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $id,
            'token' => $token
        );
        
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod')->findOneBy($criteria);
        if ($entity !== null) {
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'entity' => $entity,
                'errors' => null
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function editAction()
    {
        $criteria = array(
            'isActive' => 1
        );
        $sort_criteria = array(
            'currency' => 'ASC'
        );
        
        $currency_list = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->findBy($criteria, $sort_criteria);
        
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
            
            /** @var \Application\Entity\NmtProcurePoRow $entity ; */
            $entity = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePoRow')->findOneBy($criteria);
            
            if ($entity == null) {
                
                $errors[] = 'Entity object can\'t be empty. Or token key is not valid!';
                $this->flashMessenger()->addMessage('Something wrong!');
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => null,
                    'target' => null,
                    'currency_list' => $currency_list
                
                ));
                
                // might need redirect
            } else {
                
                $isActive = (int) $request->getPost('isActive');
                $remarks = $request->getPost('remarks');
                
                if ($isActive != 1) {
                    $isActive = 0;
                }
                
                $entity->setRemarks($remarks);
                $entity->setIsActive($isActive);
                
                //
                $quantity = $request->getPost('quantity');
                $unitPrice = $request->getPost('unitPrice');
                $taxRate = $request->getPost('taxRate');
                
                $n_validated = 0;
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
                        $n_validated ++;
                    }
                }
                
                if ($unitPrice !== null) {
                    if (! is_numeric($unitPrice)) {
                        $errors[] = 'Price is not valid. It must be a number.';
                    } else {
                        if ($unitPrice <= 0) {
                            $errors[] = 'Price must be greate than 0!';
                        }
                        $entity->setUnitPrice($unitPrice);
                        $n_validated ++;
                    }
                }
                
                if ($n_validated == 2) {
                    $netAmount = $entity->getQuantity() * $entity->getUnitPrice();
                    $entity->setNetAmount($netAmount);
                }
                
                if ($taxRate !== null) {
                    if (! is_numeric($taxRate)) {
                        $errors[] = 'taxRate is not valid. It must be a number.';
                    } else {
                        if ($taxRate < 0) {
                            $errors[] = 'taxRate must be greate than 0!';
                        }
                        $entity->setTaxRate($taxRate);
                        $n_validated ++;
                    }
                }
                
                if ($n_validated == 3) {
                    $taxAmount = $entity->getNetAmount() * $entity->getTaxRate() / 100;
                    $grossAmount = $entity->getNetAmount() + $taxAmount;
                    $entity->setTaxAmount($taxAmount);
                    $entity->setGrossAmount($grossAmount);
                }
                
                if (count($errors) > 0) {
                    
                    return new ViewModel(array(
                        'redirectUrl' => $redirectUrl,
                        'errors' => $errors,
                        'target' => $target,
                        'entity' => $entity,
                        'currency_list' => $currency_list
                        
                    ));
                }
                
                // NO ERROR
                $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                    "email" => $this->identity()
                ));
                
                $entity->setLastchangedBy($u);
                $entity->setLastChangeOn(new \DateTime());
                $this->doctrineEM->persist($entity);
                
               /*  $criteria = array(
                    'invoiceRow' => $entity->getId()
                );
                */ 
                
                /**@var \Application\Entity\NmtInventoryTrx $gr_entity*/
                
                /* $gr_entity = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryTrx')->findOneBy($criteria);
                if ($gr_entity !== null) {
                    $gr_entity->setIsActive($isActive);
                    $gr_entity->setLastChangeBy($u);
                    $gr_entity->setLastChangeOn(new \DateTime());
                    $this->doctrineEM->persist($gr_entity);
                } */ 
                
                $this->doctrineEM->flush();
                
                $redirectUrl = "/procure/po/add1?token=" . $entity->getPo()->getToken() . "&entity_id=" . $entity->getPo()->getId();
                $this->flashMessenger()->addMessage('Row ' . $entity->getRowIdentifer() . ' is updated successfully!');
                // $this->flashMessenger()->addMessage($redirectUrl);
                return $this->redirect()->toUrl($redirectUrl);
            }
        }
        
        // NO Post
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
        
        /** @var \Application\Entity\NmtProcurePoRow $entity ; */
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePoRow')->findOneBy($criteria);
        
        if ($entity !== null) {
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'errors' => null,
                'entity' => $entity,
                'target' => $entity->getPo(),
                'currency_list' => $currency_list
            
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function girdAction()
    {
        $request = $this->getRequest();
        
        // $pq_curPage = $_GET ["pq_curpage"];
        // $pq_rPP = $_GET ["pq_rpp"];
        
        $target_id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $target_id,
            'token' => $token
        );
        
        /**
         *
         * @todo : Change Target
         */
        $target = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo')->findOneBy($criteria);
        
        $a_json_final = array();
        $a_json = array();
        $a_json_row = array();
        $escaper = new Escaper();
        
        if ($target !== null) {
            
            $criteria = array(
                'invoice' => $target_id,
                'isActive' => 1
            );
            
            $query = 'SELECT e FROM Application\Entity\NmtProcurePoRow e
            WHERE e.po=?1 AND e.isActive =?2 ORDER BY e.rowNumber';
            
            $list = $this->doctrineEM->createQuery($query)
                ->setParameters(array(
                "1" => $target,
                "2" => 1
            
            ))
                ->getResult();
            
            $total_records = 0;
            if (count($list) > 0) {
                $escaper = new Escaper();
                
                $total_records = count($list);
                foreach ($list as $a) {
                    
                    /** @var \Application\Entity\NmtProcurePoRow $a ;*/
                    
                    $a_json_row["row_identifer"] = $a->getRowIdentifer();
                    $a_json_row["row_id"] = $a->getId();
                    $a_json_row["row_token"] = $a->getToken();
                    $a_json_row["row_number"] = $a->getRowNumber();
                    $a_json_row["row_unit"] = $a->getUnit();
                    $a_json_row["row_quantity"] = $a->getQuantity();
                    
                    if ($a->getUnitPrice() !== null) {
                        $a_json_row["row_unit_price"] = number_format($a->getUnitPrice(), 2);
                    } else {
                        $a_json_row["row_unit_price"] = 0;
                    }
                    
                    if ($a->getNetAmount() !== null) {
                        $a_json_row["row_net"] = number_format($a->getNetAmount(), 2);
                    } else {
                        $a_json_row["row_net"] = 0;
                    }
                    
                    if ($a->getTaxRate() !== null) {
                        $a_json_row["row_tax_rate"] = $a->getTaxRate();
                    } else {
                        $a_json_row["row_tax_rate"] = 0;
                    }
                    
                    if ($a->getGrossAmount() !== null) {
                        $a_json_row["row_gross"] = number_format($a->getGrossAmount(), 2);
                    } else {
                        $a_json_row["row_gross"] = 0;
                    }
                    
                    $a_json_row["pr_number"] = "";
                    if ($a->getPrRow() !== null) {
                        if ($a->getPrRow()->getPr() !== null) {
                            
                            $link = '<a target="_blank" href="/procure/pr/show?token=' . $a->getPrRow()
                                ->getPr()
                                ->getToken() . '&entity_id=' . $a->getPrRow()
                                ->getPr()
                                ->getId() . '&checkum=' . $a->getPrRow()
                                ->getPr()
                                ->getChecksum() . '"> ... </a>';
                            
                            $a_json_row["pr_number"] = $a->getPrRow()
                                ->getPr()
                                ->getPrNumber() . $link;
                        }
                    }
                    
                    // $a_json_row ["item_name"]="";
                    /*
                     * if( $a_json_row ["item_name"]!==null){
                     * $a_json_row ["item_name"] = $escaper->escapeJs($a->getItem()->getItemName());
                     * }
                     */
                    
                    $item_detail = "/inventory/item/show1?token=" . $a->getItem()->getToken() . "&checksum=" . $a->getItem()->getChecksum() . "&entity_id=" . $a->getItem()->getId();
                    if ($a->getItem()->getItemName() !== null) {
                        $onclick = "showJqueryDialog('Detail of Item: " . $escaper->escapeJs($a->getItem()
                            ->getItemName()) . "','1200',$(window).height()-50,'" . $item_detail . "','j_loaded_data', true);";
                    } else {
                        $onclick = "showJqueryDialog('Detail of Item: " . ($a->getItem()->getItemName()) . "','1200',$(window).height()-50,'" . $item_detail . "','j_loaded_data', true);";
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
                    $a_json_row["fa_remarks"] = $a->getFaRemarks();
                    $a_json_row["remarks"] = $a->getRemarks();
                    
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
     */
    public function downloadAction()
    {
        $request = $this->getRequest();
        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        $target_id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');
        
        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
        $rows = $res->downLoadVendorPo($target_id, $token);
        
        if ($rows !== null) {
            
            $target = null;
            if (count($rows) > 0) {
                $pr_row_1 = $rows[0];
                if ($pr_row_1 instanceof NmtProcurePoRow) {
                    $target = $pr_row_1->getPo();
                }
                
                // Create new PHPExcel object
                $objPHPExcel = new Spreadsheet();
                
                // Set document properties
                $objPHPExcel->getProperties()
                    ->setCreator("Nguyen Mau Tri")
                    ->setLastModifiedBy("Nguyen Mau Tri")
                    ->setTitle("Office 2007 XLSX Test Document")
                    ->setSubject("Office 2007 XLSX Test Document")
                    ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                    ->setKeywords("office 2007 openxml php")
                    ->setCategory("Test result file");
                
                // Add some data
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', $target->getInvoiceNo());
                
                // Add some data
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1', $target->getInvoiceDate());
                
                $header = 3;
                $i = 0;
                
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', "Contract/PO:". $target->getSysNumber());
                
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $header, "FA Remarks");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $header, "#");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $header, "SKU");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $header, "Item");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $header, "Unit");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $header, "Quantity");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $header, "Unit Price");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $header, "Net Amount");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I' . $header, "Tax Rate");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . $header, "Tax Amount");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . $header, "Gross Amount");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L' . $header, "PR Number");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('M' . $header, "PR Date");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N' . $header, "Requested Q/ty");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('O' . $header, "Requested Name");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('P' . $header, "RowNo.");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q' . $header, "Remarks");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('R' . $header, "Ref.No.");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('S' . $header, "Item.No.");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('T' . $header, "Po.Item Name");
                
                
                
                foreach ($rows as $r) {
                    
                    /**@var \Application\Entity\NmtProcurePoRow $a ;*/
                    $a = $r;
                    
                    $i ++;
                    $l = $header + $i;
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $l, $a->getFaRemarks());
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $l, $i);
                    
                    if ($a->getItem() !== null) {
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $l, $a->getItem()
                            ->getItemSku());
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $l, $a->getItem()
                            ->getItemName());
                    } else {
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $l, "NA");
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $l, "NA");
                    }
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $l, $a->getUnit());
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $l, $a->getQuantity());
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $l, $a->getUnitPrice());
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $l, $a->getNetAmount());
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I' . $l, $a->getTaxRate());
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . $l, $a->getTaxAmount());
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . $l, $a->getGrossAmount());
                    
                    if ($a->getPrRow() !== null) {
                        
                        if ($a->getPrRow()->getPr() !== null) {
                            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L' . $l, $a->getPrRow()
                                ->getPr()
                                ->getPrNumber());
                            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('M' . $l, $a->getPrRow()
                                ->getPr()
                                ->getSubmittedOn());
                        }
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N' . $l, $a->getPrRow()
                            ->getQuantity());
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('O' . $l, $a->getPrRow()
                            ->getRowName());
                    } else {
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L' . $l, "NA");
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('M' . $l, "NA");
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N' . $l, 0);
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('O' . $l, "");
                    }
                    
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('P' . $l, $a->getRowNumber());
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q' . $l, $a->getRemarks());
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('R' . $l, $a->getRowIdentifer());
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('S' . $l, $a->getItem()->getSysNumber());                    
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('T' . $l, $a->getVendorItemCode());
                    
                }
                
                // Rename worksheet
                $objPHPExcel->getActiveSheet()->setTitle("Contract-PO");
                
                $objPHPExcel->getActiveSheet()->setAutoFilter("A" . $header . ":T" . $header);
                
                // Set active sheet index to the first sheet, so Excel opens this as the first sheet
                $objPHPExcel->setActiveSheetIndex(0);
                
                // Redirect output to a client’s web browser (Excel2007)
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="invoice' . $target->getId() . '.xlsx"');
                header('Cache-Control: max-age=0');
                // If you're serving to IE 9, then the following may be needed
                header('Cache-Control: max-age=1');
                
                // If you're serving to IE over SSL, then the following may be needed
                header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
                header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
                header('Pragma: public'); // HTTP/1.0
                
                $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xlsx');
                $objWriter->save('php://output');
                exit();
            }
        }
        return $this->redirect()->toRoute('access_denied');
    }

    /**
     *
     * @return \Zend\View\Helper\ViewModel
     */
    public function listAction()
    {
        $request = $this->getRequest();
        
        // accepted only ajax request
        if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }
        ;
        
        $this->layout("layout/user/ajax");
        
        $invoice_id = (int) $this->params()->fromQuery('target_id');
        $invoice_token = $this->params()->fromQuery('token');
        
        $criteria = array(
            'id' => $invoice_id,
            'token' => $invoice_token
        );
        
        /** @var \Application\Entity\FinVendorInvoice $target ;*/
        $target = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->findOneBy($criteria);
        
        if ($target !== null) {
            
            $criteria = array(
                'invoice' => $invoice_id,
                'isActive' => 1
            );
            
            $query = 'SELECT e FROM Application\Entity\FinVendorInvoiceRow e
            WHERE e.invoice=?1 AND e.isActive =?2';
            
            $list = $this->doctrineEM->createQuery($query)
                ->setParameters(array(
                "1" => $target,
                "2" => 1
            
            ))
                ->getResult();
            return new ViewModel(array(
                'list' => $list,
                'total_records' => count($list),
                'paginator' => null
            ));
        }
        
        return $this->redirect()->toRoute('access_denied');
    }
    
    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function poOfItemAction()
    {
        $request = $this->getRequest();
        // accepted only ajax request
        if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }
        $this->layout("layout/user/ajax");
        
        $item_id = (int) $this->params()->fromQuery('item_id');
        $token = $this->params()->fromQuery('token');
        
        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
        $rows = $res->getPoOfItem($item_id, $token);
         return new ViewModel(array(
            'rows' => $rows
        ));
    }
    

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function updateRowAction()
    {
        $a_json_final = array();
        $escaper = new Escaper();
        
        /*
         * $pq_curPage = $_GET ["pq_curpage"];
         * $pq_rPP = $_GET ["pq_rpp"];
         */
        $sent_list = json_decode($_POST['sent_list'], true);
        // echo json_encode($sent_list);
        
        $to_update = $sent_list['updateList'];
        foreach ($to_update as $a) {
            $criteria = array(
                'id' => $a['row_id'],
                'token' => $a['row_token']
            );
            
            /** @var \Application\Entity\NmtProcurePoRow $entity */
            $entity = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePoRow')->findOneBy($criteria);
            
            if ($entity != null) {
                $entity->setFaRemarks($a['fa_remarks']);
                $entity->setRowNumber($a['row_number']);
                // $a_json_final['updateList']=$a['row_id'] . 'has been updated';
                $this->doctrineEM->persist($entity);
            }
        }
        $this->doctrineEM->flush();
        
        // $a_json_final["updateList"]= json_encode($sent_list["updateList"]);
        
        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $response->setContent(json_encode($sent_list));
        return $response;
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function updateTokenAction()
    {
        $criteria = array();
        $sort_criteria = array();
        
        $list = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod')->findBy($criteria, $sort_criteria);
        
        if (count($list) > 0) {
            foreach ($list as $entity) {
                $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
            }
        }
        
        $this->doctrineEM->flush();
        
        return new ViewModel(array(
            'list' => $list
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
     * @return \PM\Controller\IndexController
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
        return $this;
    }
}
