<?php
namespace Finance\Service;

use Application\Entity\NmtInventoryTrx;
use Doctrine\ORM\EntityManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Math\Rand;
use Application\Service\AbstractService;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class JEService extends AbstractService
{

    /**
     *
     * @param \Application\Entity\FinVendorInvoice $entity
     *
     * @param \Application\Entity\MlaUsers $u
     *
     * @param \Application\Controller\Plugin\NmtPlugin $nmtPlugin
     *
     * @param boolean $isPosting
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function postAP($entity, $rows, $u, $nmtPlugin, $isFlush = false, $isPosting = 1)
    {
        if (! $entity instanceof \Application\Entity\FinVendorInvoice) {
            throw new \Exception("Invalid Argument! Invoice is expected");
        }

        if ($u == null) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }

        if (count($rows) == 0) {
            throw new \Exception("Invoice is empty. No Posting will be made!");
        }

        /**
         * Debit: Inventory, Expenses
         * Credit: Payable to supplier.
         */

        // Create JE

        $je = new \Application\Entity\FinJe();
        $je->setCurrency($entity->getCurrency());
        $je->setLocalCurrency($entity->getLocalCurrency());
        $je->setExchangeRate($entity->getExchangeRate());

        $je->setPostingDate($entity->getPostingDate());
        $je->setDocumentDate($entity->getPostingDate());
        $je->setPostingPeriod($entity->getPostingPeriod());

        $je->getDocType("JE");
        $je->setCreatedBy($u);
        $je->setCreatedOn($entity->getCreatedOn());
        $je->setSysNumber($nmtPlugin->getDocNumber($je));

        $je->setSourceClass(get_class($entity));
        $je->setSourceId($entity->getId());
        $je->setSourceToken($entity->getToken());

        $this->doctrineEM->persist($je);

        $n = 0;
        $total_credit = 0;
        $total_local_credit = 0;

        // Credit on GR - NI for clearing later.
        $criteria = array(
            'id' => 5
        );
        $clearing_gl = $this->doctrineEM->getRepository('Application\Entity\FinAccount')->findOneBy($criteria);

        foreach ($rows as $r) {
            $n ++;
            /** @var \Application\Entity\FinVendorInvoiceRow $r ; */

            // ignore row with Zero quantity
            if ($r->getQuantity() == 0) {
                $r->setIsActive(0);
                continue;
            }

            if ($r->getUnitPrice() > 0) {

                // Create JE Row - DEBIT
                $je_row = new \Application\Entity\FinJeRow();
                $je_row->setJe($je);

                $je_row->setCostCenter($r->getCostCenter());
                $je_row->setPostingKey(\Finance\Model\Constants::POSTING_KEY_DEBIT);
                $je_row->setPostingCode(\Finance\Model\Constants::POSTING_KEY_DEBIT);
                $je_row->setDocAmount($r->getQuantity() * $r->getUnitPrice());
                $je_row->setLocalAmount($r->getQuantity() * $r->getUnitPrice() * $je->getExchangeRate());
                $total_credit = $total_credit + $r->getQuantity() * $r->getUnitPrice();
                $total_local_credit = $total_local_credit + $r->getQuantity() * $r->getUnitPrice() * $je->getExchangeRate();
                $je_row->setSysNumber($je->getSysNumber() . "-" . $n);
                $je_row->setCreatedBy($u);
                $je_row->setCreatedOn($entity->getCreatedOn());

                $je_row->setApRow($r);
                $je_row->setGrRow($r->getGrRow());
                $je_row->setAp($r->getInvoice());

                $t = '';
                if ($r->getGrRow() !== null) {
                    $t .= ' // ' . $r->getGrRow()->getRowIdentifer();
                }
                $je_row->setJeDescription("AP." . $r->getRowIdentifer() . $t);

                $transactionType = $r->getTransactionType();

                switch ($transactionType) {

                    case \Application\Model\Constants::PROCURE_TRANSACTION_TYPE_GRIR:
                        $je_row->setGlAccount($r->getGlAccount());
                        break;

                    case \Application\Model\Constants::PROCURE_TRANSACTION_TYPE_GRNI:
                        $je_row->setGlAccount($clearing_gl);
                        break;
                }

                $this->doctrineEM->persist($je_row);
            }
        }

        if ($total_credit > 0) {

            // Create JE Row - Credit
            $je_row = new \Application\Entity\FinJeRow();

            $je_row->setJe($je);

            /**
             *
             * @todo: Using Control G/L account of Vendor
             */
            $criteria = array(
                'id' => 4
            );
            $gl_account = $this->doctrineEM->getRepository('Application\Entity\FinAccount')->findOneBy($criteria);
            $je_row->setGlAccount($gl_account);
            $je_row->setPostingKey(\Finance\Model\Constants::POSTING_KEY_CREDIT);
            $je_row->setPostingCode(\Finance\Model\Constants::POSTING_KEY_CREDIT);

            $je_row->setDocAmount($total_credit);
            $je_row->setLocalAmount($total_local_credit);

            $je_row->setCreatedBy($u);
            $je_row->setCreatedOn($entity->getCreatedOn());

            $n = $n + 1;
            $je_row->setSysNumber($je->getSysNumber() . "-" . $n);
            $this->doctrineEM->persist($je_row);

            // Create Vendor Balance.

            $vendor_bl_row = new \Application\Entity\NmtBpVendorBl();
            $vendor_bl_row->setVendor($entity->getVendor());
            $vendor_bl_row->setCompany($entity->getCompany());
            $vendor_bl_row->setPostingCode(\Finance\Model\Constants::POSTING_KEY_CREDIT);
            $vendor_bl_row->setDocAmount($total_credit);
            $vendor_bl_row->setLocalAmount($total_local_credit);
            $vendor_bl_row->setExchangeRate($entity->getExchangeRate());
            $vendor_bl_row->setGlAccount($gl_account);
            $vendor_bl_row->setCreatedBy($u);
            $vendor_bl_row->setCreatedOn($entity->getCreatedOn());
            $vendor_bl_row->setPostingDate($entity->getPostingDate());
            $vendor_bl_row->setPostingPeriod($entity->getPostingPeriod());
            $vendor_bl_row->setReference($entity->getInvoiceNo());
            $vendor_bl_row->setDocumentDate($entity->getInvoiceDate());
            $vendor_bl_row->setSysNumber($entity->getSysNumber());

            /**
             *
             * @todo: Set SourceID, Class, etc.
             */

            $this->doctrineEM->persist($vendor_bl_row);
        }

        if ($isFlush == true) {
            $this->doctrineEM->flush();
        }
    }

    /**
     *
     * @param \Application\Entity\FinVendorInvoice $entity
     *
     * @param \Application\Entity\MlaUsers $u
     *
     * @param \Application\Controller\Plugin\NmtPlugin $nmtPlugin
     *
     * @param boolean $isPosting
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function reverseAP($entity, $rows, $u, $nmtPlugin, $isFlush = false, $isPosting = 1)
    {
        if (! $entity instanceof \Application\Entity\FinVendorInvoice) {
            throw new \Exception("Invalid Argument! Invoice is expected");
        }

        if ($u == null) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }

        if (count($rows) == 0) {
            throw new \Exception("Invoice is empty. No Posting will be made!");
        }

        /**
         * Debit : Payable to supplier.
         * Credit : Inventory, Expenses
         */

        // Create JE

        $je = new \Application\Entity\FinJe();
        $je->setCurrency($entity->getCurrency());
        $je->setLocalCurrency($entity->getLocalCurrency());
        $je->setExchangeRate($entity->getExchangeRate());

        $je->setPostingDate($entity->getPostingDate());
        $je->setDocumentDate($entity->getPostingDate());
        $je->setPostingPeriod($entity->getPostingPeriod());

        $je->getDocType("JE");
        $je->setCreatedBy($u);
        $je->setCreatedOn($entity->getCreatedOn());
        $je->setSysNumber($nmtPlugin->getDocNumber($je));

        $je->setSourceClass(get_class($entity));
        $je->setSourceId($entity->getId());
        $je->setSourceToken($entity->getToken());

        $this->doctrineEM->persist($je);

        $n = 0;
        $total_credit = 0;
        $total_local_credit = 0;

        // Credit on GR - NI for clearing later.
        $criteria = array(
            'id' => 5
        );
        $clearing_gl = $this->doctrineEM->getRepository('Application\Entity\FinAccount')->findOneBy($criteria);

        foreach ($rows as $r) {
            $n ++;
            /** @var \Application\Entity\FinVendorInvoiceRow $r ; */

            // ignore row with Zero quantity
            if ($r->getQuantity() == 0) {
                $r->setIsActive(0);
                continue;
            }

            if ($r->getUnitPrice() > 0) {
                // Create JE Row - DEBIT
                $je_row = new \Application\Entity\FinJeRow();
                $je_row->setJe($je);

                $je_row->setCostCenter($r->getCostCenter());
                $je_row->setPostingKey(\Finance\Model\Constants::POSTING_KEY_CREDIT);
                $je_row->setPostingCode(\Finance\Model\Constants::POSTING_KEY_CREDIT);
                $je_row->setDocAmount($r->getQuantity() * $r->getUnitPrice());
                $je_row->setLocalAmount($r->getQuantity() * $r->getUnitPrice() * $je->getExchangeRate());
                $total_credit = $total_credit + $r->getQuantity() * $r->getUnitPrice();
                $total_local_credit = $total_local_credit + $r->getQuantity() * $r->getUnitPrice() * $je->getExchangeRate();
                $je_row->setSysNumber($je->getSysNumber() . "-" . $n);
                $je_row->setCreatedBy($u);
                $je_row->setCreatedOn($entity->getCreatedOn());

                $je_row->setApRow($r);
                $je_row->setGrRow($r->getGrRow());
                $je_row->setAp($r->getInvoice());

                $t = '';
                if ($r->getGrRow() !== null) {
                    $t .= ' // ' . $r->getGrRow()->getRowIdentifer();
                }
                $je_row->setJeDescription("[Reversal] AP." . $r->getRowIdentifer() . $t);

                $transactionType = $r->getTransactionType();

                switch ($transactionType) {

                    case \Application\Model\Constants::PROCURE_TRANSACTION_TYPE_GRIR:
                        $je_row->setGlAccount($r->getGlAccount());
                        break;

                    case \Application\Model\Constants::PROCURE_TRANSACTION_TYPE_GRNI:
                        $je_row->setGlAccount($clearing_gl);
                        break;
                }

                $this->doctrineEM->persist($je_row);
            }
        }

        if ($total_credit > 0) {

            // Create JE Row - Credit
            $je_row = new \Application\Entity\FinJeRow();

            $je_row->setJe($je);

            /**
             *
             * @todo: Using Control G/L account of Vendor
             */

            $gl_account_vendor = null;
            if ($r->getInvoice()->getVendor() !== null) {
                $gl_account_vendor = $r->getInvoice()
                    ->getVendor()
                    ->getGlAccount();
            }

            if ($gl_account_vendor == null) {
                $criteria = array(
                    'id' => 4
                );
                $gl_account = $this->doctrineEM->getRepository('Application\Entity\FinAccount')->findOneBy($criteria);
            } else {
                $gl_account = $gl_account_vendor;
            }

            $je_row->setGlAccount($gl_account);
            $je_row->setPostingKey(\Finance\Model\Constants::POSTING_KEY_DEBIT);
            $je_row->setPostingCode(\Finance\Model\Constants::POSTING_KEY_DEBIT);

            $je_row->setDocAmount($total_credit);
            $je_row->setLocalAmount($total_local_credit);

            $je_row->setCreatedBy($u);
            $je_row->setCreatedOn($entity->getCreatedOn());

            $n = $n + 1;
            $je_row->setSysNumber($je->getSysNumber() . "-" . $n);
            $this->doctrineEM->persist($je_row);
        }

        if ($isFlush == true) {
            $this->doctrineEM->flush();
        }
    }

    /**
     *
     * @param \Application\Entity\NmtProcureGr $entity
     *
     * @param \Application\Entity\MlaUsers $u
     *
     * @param \Application\Controller\Plugin\NmtPlugin $nmtPlugin
     *
     * @param boolean $isPosting
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function reverseGR($entity, $rows, $u, $nmtPlugin, $isFlush = false, $isPosting = 1)
    {
        if (! $entity instanceof \Application\Entity\NmtProcureGr) {
            throw new \Exception("Invalid Argument! GRPO is expected");
        }

        if (! $u instanceof \Application\Entity\MlaUsers) {
            throw new \Exception("Invalid Argument! User can is not indentided for this transaction.");
        }

        if (count($rows) == 0) {
            throw new \Exception("Good Receipt is empty. No Posting will be made!");
        }

        /**
         * Debit : Payable to supplier.
         * Credit : Inventory, Expenses
         */

        // Create JE

        $je = new \Application\Entity\FinJe();
        $je->setCurrency($entity->getCurrency());
        $je->setLocalCurrency($entity->getLocalCurrency());
        $je->setExchangeRate($entity->getExchangeRate());

        $je->setPostingDate($entity->getGrDate());
        $je->setDocumentDate($entity->getGrDate());
        $je->setPostingPeriod($entity->getPostingPeriod());

        $je->getDocType("JE");
        $je->setCreatedBy($u);
        $je->setCreatedOn($entity->getCreatedOn());
        $je->setSysNumber($nmtPlugin->getDocNumber($je));

        $je->setSourceClass(get_class($entity));
        $je->setSourceId($entity->getId());
        $je->setSourceToken($entity->getToken());

        $this->doctrineEM->persist($je);

        $n = 0;
        $total_credit = 0;
        $total_local_credit = 0;

        foreach ($rows as $r) {
            $n ++;
            /** @var \Application\Entity\NmtProcureGrRow $r ; */

            // ignore row with Zero quantity
            if ($r->getQuantity() == 0) {
                $r->setIsActive(0);
                continue;
            }

            if ($r->getUnitPrice() > 0) {
                // Create JE Row - DEBIT
                $je_row = new \Application\Entity\FinJeRow();
                $je_row->setJe($je);

                $je_row->setCostCenter($r->getCostCenter());
                $je_row->setPostingKey(\Finance\Model\Constants::POSTING_KEY_CREDIT);
                $je_row->setPostingCode(\Finance\Model\Constants::POSTING_KEY_CREDIT);
                $je_row->setDocAmount($r->getQuantity() * $r->getUnitPrice());
                $je_row->setLocalAmount($r->getQuantity() * $r->getUnitPrice() * $je->getExchangeRate());
                $total_credit = $total_credit + $r->getQuantity() * $r->getUnitPrice();
                $total_local_credit = $total_local_credit + $r->getQuantity() * $r->getUnitPrice() * $je->getExchangeRate();
                $je_row->setSysNumber($je->getSysNumber() . "-" . $n);
                $je_row->setCreatedBy($u);
                $je_row->setCreatedOn($entity->getCreatedOn());

                $je_row->setGrRow($r);
                $je_row->setGr($r->getGr());

                $t = '';
                if ($r !== null) {
                    $t .= ' // ' . $r->getRowIdentifer();
                }
                $je_row->setJeDescription("[Reversal] GR." . $r->getRowIdentifer() . $t);
                $je_row->setGlAccount($r->getGlAccount());
            }

            $this->doctrineEM->persist($je_row);
        }

        if ($total_credit > 0) {

            // Create JE Row - Credit
            $je_row = new \Application\Entity\FinJeRow();

            $je_row->setJe($je);

            $criteria = array(
                'id' => 5
            );
            $gl_account = $this->doctrineEM->getRepository('Application\Entity\FinAccount')->findOneBy($criteria);

            $je_row->setGlAccount($gl_account);
            $je_row->setPostingKey(\Finance\Model\Constants::POSTING_KEY_DEBIT);
            $je_row->setPostingCode(\Finance\Model\Constants::POSTING_KEY_DEBIT);

            $je_row->setDocAmount($total_credit);
            $je_row->setLocalAmount($total_local_credit);

            $je_row->setCreatedBy($u);
            $je_row->setCreatedOn($entity->getCreatedOn());

            $n = $n + 1;
            $je_row->setSysNumber($je->getSysNumber() . "-" . $n);
            $this->doctrineEM->persist($je_row);
        }

        if ($isFlush == true) {
            $this->doctrineEM->flush();
        }
    }

    /**
     *
     * @param \Application\Entity\PmtOutgoing $entity
     *
     * @param \Application\Entity\MlaUsers $u
     *
     * @param \Application\Controller\Plugin\NmtPlugin $nmtPlugin
     *
     * @param boolean $isPosting
     *
     */
    public function postAPPayment($entity, $u, $nmtPlugin, $isFlush = false, $isPosting = 1)
    {
        if (! $entity instanceof \Application\Entity\PmtOutgoing) {
            throw new \Exception("Invalid Argument! Invoice is expected");
        }

        if ($u == null) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }

        /**
         * Debit: Payable to supplier.
         * Credit: Payment Method GL Account
         */

        // Create JE

        $je = new \Application\Entity\FinJe();
        $je->setCurrency($entity->getDocCurrency());
        $je->setLocalCurrency($entity->getLocalCurrency());
        $je->setExchangeRate($entity->getExchangeRate());

        $je->setPostingDate($entity->getPostingDate());
        $je->setDocumentDate($entity->getPostingDate());
        $je->setPostingPeriod($entity->getPostingPeriod());

        $je->getDocType("JE");
        $je->setCreatedBy($u);
        $je->setCreatedOn($entity->getCreatedOn());
        $je->setSysNumber($nmtPlugin->getDocNumber($je));

        $je->setSourceClass(get_class($entity));
        $je->setSourceId($entity->getId());
        $je->setSourceToken($entity->getToken());
        $je->setJeRemarks($entity->getRemarks());

        $this->doctrineEM->persist($je);

        $total_debit = 0;
        $total_local_debit = 0;

        // Vendor GL
        $vendor_gl = $entity->getVendor()->getGlAccount();

        // Create JE Row - DEBIT
        $je_row = new \Application\Entity\FinJeRow();
        $je_row->setJe($je);

        $je_row->setPostingKey(\Finance\Model\Constants::POSTING_KEY_DEBIT);
        $je_row->setPostingCode(\Finance\Model\Constants::POSTING_KEY_DEBIT);
        $je_row->setGlAccount($vendor_gl);

        $je_row->setDocAmount($entity->getDocAmount());
        $je_row->setLocalAmount($entity->getDocAmount() * $je->getExchangeRate());

        $total_debit = $total_debit + $entity->getDocAmount();
        $total_local_debit = $total_local_debit + $entity->getDocAmount() * $je->getExchangeRate();

        $je_row->setSysNumber($je->getSysNumber() . "-1");
        $je_row->setCreatedBy($u);
        $je_row->setCreatedOn($entity->getCreatedOn());

        $je_row->setAp($entity->getApInvoice());
        $je_row->setJeDescription($entity->getRemarks());
        $this->doctrineEM->persist($je_row);

        if ($total_debit > 0) {

            // Create JE Row - Credit
            $je_row = new \Application\Entity\FinJeRow();

            $je_row->setJe($je);

            $je_row->setGlAccount($entity->getPmtMethod()
                ->getGlAccount());
            $je_row->setPostingKey(\Finance\Model\Constants::POSTING_KEY_CREDIT);
            $je_row->setPostingCode(\Finance\Model\Constants::POSTING_KEY_CREDIT);

            $je_row->setDocAmount($total_debit);
            $je_row->setLocalAmount($total_local_debit);

            $je_row->setCreatedBy($u);
            $je_row->setCreatedOn($entity->getCreatedOn());
            $je_row->setSysNumber($je->getSysNumber() . "-2");
            $je_row->setJeDescription($entity->getRemarks());

            $this->doctrineEM->persist($je_row);
        }

        if ($isFlush == true) {
            $this->doctrineEM->flush();
        }

        $m = sprintf('[OK] Journal Entry #%s posted. Ref.%s', $je->getSysNumber(), $entity->getSysNumber());

        $this->getEventManager()->trigger('finance.activity.log', __METHOD__, array(
            'priority' => \Zend\Log\Logger::INFO,
            'message' => $m,
            'createdBy' => $u,
            'createdOn' => $entity->getCreatedOn()
        ));
    }

    /**
     *
     * @param \Application\Entity\PmtOutgoing $entity
     *
     * @param \Application\Entity\MlaUsers $u
     *
     * @param \Application\Controller\Plugin\NmtPlugin $nmtPlugin
     *
     * @param boolean $isPosting
     *
     */
    public function postPOPayment($entity, $u, $nmtPlugin, $isFlush = false, $isPosting = 1)
    {
        if (! $entity instanceof \Application\Entity\PmtOutgoing) {
            throw new \Exception("Invalid Argument! Payment Object is expected");
        }

        if ($u == null) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }

        /**
         * Debit: Payable to supplier.
         * Credit: Payment Method GL Account
         */

        // Create JE

        $je = new \Application\Entity\FinJe();
        $je->setCurrency($entity->getDocCurrency());
        $je->setLocalCurrency($entity->getLocalCurrency());
        $je->setExchangeRate($entity->getExchangeRate());

        $je->setPostingDate($entity->getPostingDate());
        $je->setDocumentDate($entity->getPostingDate());
        $je->setPostingPeriod($entity->getPostingPeriod());

        $je->getDocType("JE");
        $je->setCreatedBy($u);
        $je->setCreatedOn($entity->getCreatedOn());
        $je->setSysNumber($nmtPlugin->getDocNumber($je));

        $je->setSourceClass(get_class($entity));
        $je->setSourceId($entity->getId());
        $je->setSourceToken($entity->getToken());
        $je->setJeRemarks($entity->getRemarks());

        $this->doctrineEM->persist($je);

        $total_debit = 0;
        $total_local_debit = 0;

        // Vendor GL
        $vendor_gl = $entity->getVendor()->getGlAccount();

        // Create JE Row - DEBIT
        $je_row = new \Application\Entity\FinJeRow();
        $je_row->setJe($je);

        $je_row->setPostingKey(\Finance\Model\Constants::POSTING_KEY_DEBIT);
        $je_row->setPostingCode(\Finance\Model\Constants::POSTING_KEY_DEBIT);
        $je_row->setGlAccount($vendor_gl);

        $je_row->setDocAmount($entity->getDocAmount());
        $je_row->setLocalAmount($entity->getDocAmount() * $je->getExchangeRate());

        $total_debit = $total_debit + $entity->getDocAmount();
        $total_local_debit = $total_local_debit + $entity->getDocAmount() * $je->getExchangeRate();

        $je_row->setSysNumber($je->getSysNumber() . "-1");
        $je_row->setCreatedBy($u);
        $je_row->setCreatedOn($entity->getCreatedOn());

        $je_row->setJeDescription($entity->getRemarks());
        $this->doctrineEM->persist($je_row);

        if ($total_debit > 0) {

            // Create JE Row - Credit
            $je_row = new \Application\Entity\FinJeRow();

            $je_row->setJe($je);

            $je_row->setGlAccount($entity->getPmtMethod()
                ->getGlAccount());
            $je_row->setPostingKey(\Finance\Model\Constants::POSTING_KEY_CREDIT);
            $je_row->setPostingCode(\Finance\Model\Constants::POSTING_KEY_CREDIT);

            $je_row->setDocAmount($total_debit);
            $je_row->setLocalAmount($total_local_debit);

            $je_row->setCreatedBy($u);
            $je_row->setCreatedOn($entity->getCreatedOn());
            $je_row->setSysNumber($je->getSysNumber() . "-2");
            $je_row->setJeDescription($entity->getRemarks());

            $this->doctrineEM->persist($je_row);
        }

        if ($isFlush == true) {
            $this->doctrineEM->flush();
        }

        $m = sprintf('[OK] Journal Entry #%s posted. Ref.%s', $je->getSysNumber(), $entity->getSysNumber());

        $this->getEventManager()->trigger('finance.activity.log', __METHOD__, array(
            'priority' => \Zend\Log\Logger::INFO,
            'message' => $m,
            'createdBy' => $u,
            'createdOn' => $entity->getCreatedOn()
        ));
    }

    /**
     *
     * @param \Application\Entity\PmtOutgoing $entity
     *
     * @param \Application\Entity\MlaUsers $u
     *
     * @param \Application\Controller\Plugin\NmtPlugin $nmtPlugin
     *
     * @param boolean $isPosting
     *
     */
    public function reverseAPPayment($entity, $u, $nmtPlugin, $isFlush = false, $isPosting = 1)
    {
        if (! $entity instanceof \Application\Entity\PmtOutgoing) {
            throw new \Exception("Invalid Argument! Invoice is expected");
        }

        if ($u == null) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }

        /**
         * Debit: Payable to supplier.
         * Credit: Payment Method GL Account
         */

        // Create JE

        $je = new \Application\Entity\FinJe();

        $je->setCurrency($entity->getDocCurrency());
        $je->setLocalCurrency($entity->getLocalCurrency());
        $je->setExchangeRate($entity->getExchangeRate());

        $je->setPostingDate($entity->getPostingDate());
        $je->setDocumentDate($entity->getPostingDate());
        $je->setPostingPeriod($entity->getPostingPeriod());

        $je->getDocType("JE-1");
        $je->setIsReversed(1);

        $je->setCreatedBy($u);
        $je->setCreatedOn($entity->getCreatedOn());
        $je->setSysNumber($nmtPlugin->getDocNumber($je));

        $je->setSourceClass(get_class($entity));
        $je->setSourceId($entity->getId());
        $je->setSourceToken($entity->getToken());
        $je->setJeRemarks("reversal");

        $this->doctrineEM->persist($je);

        $total_credit = 0;
        $total_local_credit = 0;

        // Vendor GL
        $vendor_gl = $entity->getVendor()->getGlAccount();

        // Create JE Row - CREDIT
        $je_row = new \Application\Entity\FinJeRow();
        $je_row->setJe($je);

        $je_row->setPostingKey(\Finance\Model\Constants::POSTING_KEY_CREDIT);
        $je_row->setPostingCode(\Finance\Model\Constants::POSTING_KEY_CREDIT);
        $je_row->setGlAccount($vendor_gl);

        $je_row->setDocAmount($entity->getDocAmount());
        $je_row->setLocalAmount($entity->getDocAmount() * $je->getExchangeRate());

        $total_credit = $total_credit + $entity->getDocAmount();
        $total_local_credit = $total_local_credit + $entity->getDocAmount() * $je->getExchangeRate();

        $je_row->setSysNumber($je->getSysNumber() . "-1");
        $je_row->setCreatedBy($u);
        $je_row->setCreatedOn($entity->getCreatedOn());

        $je_row->setAp($entity->getApInvoice());
        $je_row->setJeDescription('[Reversal] ' . $entity->getRemarks());
        $this->doctrineEM->persist($je_row);

        if ($total_credit > 0) {

            // Create JE Row - DEBIT
            $je_row = new \Application\Entity\FinJeRow();

            $je_row->setJe($je);

            $je_row->setGlAccount($entity->getPmtMethod()
                ->getGlAccount());
            $je_row->setPostingKey(\Finance\Model\Constants::POSTING_KEY_DEBIT);
            $je_row->setPostingCode(\Finance\Model\Constants::POSTING_KEY_DEBIT);

            $je_row->setDocAmount($total_credit);
            $je_row->setLocalAmount($total_local_credit);

            $je_row->setCreatedBy($u);
            $je_row->setCreatedOn($entity->getCreatedOn());
            $je_row->setSysNumber($je->getSysNumber() . "-2");
            $je_row->setJeDescription('[Reversal] ' . $entity->getRemarks());

            $this->doctrineEM->persist($je_row);
        }

        if ($isFlush == true) {
            $this->doctrineEM->flush();
        }

        $m = sprintf('[OK] Journal Entry #%s posted [Reserval]. Ref.%s', $je->getSysNumber(), $entity->getSysNumber());

        $this->getEventManager()->trigger('finance.activity.log', __METHOD__, array(
            'priority' => \Zend\Log\Logger::INFO,
            'message' => $m,
            'createdBy' => $u,
            'createdOn' => $entity->getCreatedOn()
        ));
    }

    /**
     *
     * @param \Application\Entity\PmtOutgoing $entity
     *
     * @param \Application\Entity\MlaUsers $u
     *
     * @param \Application\Controller\Plugin\NmtPlugin $nmtPlugin
     *
     * @param boolean $isPosting
     *
     */
    public function reversePOPayment($entity, $u, $nmtPlugin, $isFlush = false, $isPosting = 1)
    {
        if (! $entity instanceof \Application\Entity\PmtOutgoing) {
            throw new \Exception("Invalid Argument! Invoice is expected");
        }

        if ($u == null) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }

        /**
         * Debit: Payable to supplier.
         * Credit: Payment Method GL Account
         */

        // Create JE

        $je = new \Application\Entity\FinJe();

        $je->setCurrency($entity->getDocCurrency());
        $je->setLocalCurrency($entity->getLocalCurrency());
        $je->setExchangeRate($entity->getExchangeRate());

        $je->setPostingDate($entity->getPostingDate());
        $je->setDocumentDate($entity->getPostingDate());
        $je->setPostingPeriod($entity->getPostingPeriod());

        $je->getDocType("JE-1");
        $je->setIsReversed(1);

        $je->setCreatedBy($u);
        $je->setCreatedOn($entity->getCreatedOn());
        $je->setSysNumber($nmtPlugin->getDocNumber($je));

        $je->setSourceClass(get_class($entity));
        $je->setSourceId($entity->getId());
        $je->setSourceToken($entity->getToken());
        $je->setJeRemarks("reversal");

        $this->doctrineEM->persist($je);

        $total_credit = 0;
        $total_local_credit = 0;

        // Vendor GL
        $vendor_gl = $entity->getVendor()->getGlAccount();

        // Create JE Row - CREDIT
        $je_row = new \Application\Entity\FinJeRow();
        $je_row->setJe($je);

        $je_row->setPostingKey(\Finance\Model\Constants::POSTING_KEY_CREDIT);
        $je_row->setPostingCode(\Finance\Model\Constants::POSTING_KEY_CREDIT);
        $je_row->setGlAccount($vendor_gl);

        $je_row->setDocAmount($entity->getDocAmount());
        $je_row->setLocalAmount($entity->getDocAmount() * $je->getExchangeRate());

        $total_credit = $total_credit + $entity->getDocAmount();
        $total_local_credit = $total_local_credit + $entity->getDocAmount() * $je->getExchangeRate();

        $je_row->setSysNumber($je->getSysNumber() . "-1");
        $je_row->setCreatedBy($u);
        $je_row->setCreatedOn($entity->getCreatedOn());
        $je_row->setJeDescription('[Reversal] ' . $entity->getRemarks());
        $this->doctrineEM->persist($je_row);

        if ($total_credit > 0) {

            // Create JE Row - DEBIT
            $je_row = new \Application\Entity\FinJeRow();

            $je_row->setJe($je);

            $je_row->setGlAccount($entity->getPmtMethod()
                ->getGlAccount());
            $je_row->setPostingKey(\Finance\Model\Constants::POSTING_KEY_DEBIT);
            $je_row->setPostingCode(\Finance\Model\Constants::POSTING_KEY_DEBIT);

            $je_row->setDocAmount($total_credit);
            $je_row->setLocalAmount($total_local_credit);

            $je_row->setCreatedBy($u);
            $je_row->setCreatedOn($entity->getCreatedOn());
            $je_row->setSysNumber($je->getSysNumber() . "-2");
            $je_row->setJeDescription('[Reversal] ' . $entity->getRemarks());

            $this->doctrineEM->persist($je_row);
        }

        if ($isFlush == true) {
            $this->doctrineEM->flush();
        }

        $m = sprintf('[OK] Journal Entry #%s posted [Reserval]. Ref.%s', $je->getSysNumber(), $entity->getSysNumber());

        $this->getEventManager()->trigger('finance.activity.log', __METHOD__, array(
            'priority' => \Zend\Log\Logger::INFO,
            'message' => $m,
            'createdBy' => $u,
            'createdOn' => $entity->getCreatedOn()
        ));
    }

    /**
     *
     * @param \Application\Entity\NmtProcureGr $entity
     *
     * @param \Application\Entity\MlaUsers $u
     *
     * @param \Application\Controller\Plugin\NmtPlugin $nmtPlugin
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function postGR($entity, $rows, $u, $nmtPlugin, $isFlush = true)
    {
        if (! $entity instanceof \Application\Entity\NmtProcureGr) {
            throw new \Exception("Invalid Argument");
        }

        if ($u == null) {
            throw new \Exception("Invalid Argument! User is not indentided for this transaction.");
        }

        if ($nmtPlugin == null) {
            throw new \Exception("Invalid Argument! plugin not found.");
        }

        if (count($rows) == 0) {
            throw new \Exception("Good receipt is empty. No Posting will be made!");
        }

        /**
         * Debit: Inventory, Expenses
         * Credit: Payable to supplier.
         */

        // Create JE

        $je = new \Application\Entity\FinJe();
        $je->setCurrency($entity->getCurrency());
        $je->setLocalCurrency($entity->getLocalCurrency());
        $je->setExchangeRate($entity->getExchangeRate());

        $je->setPostingDate($entity->getGrDate());
        $je->setDocumentDate($entity->getGrDate());
        $je->setPostingPeriod($entity->getPostingPeriod());

        $je->getDocType("JE");
        $je->setCreatedBy($u);
        $je->setCreatedOn($entity->getCreatedOn());
        $je->setSysNumber($nmtPlugin->getDocNumber($je));

        $je->setSourceClass(get_class($entity));
        $je->setSourceId($entity->getId());
        $je->setSourceToken($entity->getToken());

        $this->doctrineEM->persist($je);

        $n = 0;
        $total_credit = 0;
        $total_local_credit = 0;

        // Credit on GR - NI for clearing later.
        $criteria = array(
            'id' => 5
        );
        $clearing_gl = $this->doctrineEM->getRepository('Application\Entity\FinAccount')->findOneBy($criteria);

        foreach ($rows as $r) {

            /** @var \Application\Entity\NmtProcureGrRow $r ; */

            // No need to create JE for ZERO value row.

            if ($r->getUnitPrice() > 0) {

                $n ++;

                // Create JE Row - DEBIT
                $je_row = new \Application\Entity\FinJeRow();
                $je_row->setJe($je);

                $je_row->setGlAccount($r->getGlAccount());
                $je_row->setPostingKey(\Finance\Model\Constants::POSTING_KEY_DEBIT);
                $je_row->setPostingCode(\Finance\Model\Constants::POSTING_KEY_DEBIT);

                $je_row->setDocAmount($r->getQuantity() * $r->getUnitPrice());
                $je_row->setLocalAmount($r->getQuantity() * $r->getUnitPrice() * $je->getExchangeRate());

                $total_credit = $total_credit + $r->getQuantity() * $r->getUnitPrice();
                $total_local_credit = $total_local_credit + $r->getQuantity() * $r->getUnitPrice() * $je->getExchangeRate();
                $je_row->setJeDescription("Goods Receipt ref." . $r->getRowIdentifer());
                $je_row->setGrRow($r);
                $je_row->setGr($entity);

                $je_row->setSysNumber($je->getSysNumber() . "-" . $n);

                $je_row->setCreatedBy($u);
                $je_row->setCreatedOn($entity->getCreatedOn());
                $this->doctrineEM->persist($je_row);

                // Create JE Row - DEBIT
                // Create JE Row - Credit - AP account
                $je_row1 = new \Application\Entity\FinJeRow();
                $je_row1->setJe($je);
                $je_row1->setGlAccount($clearing_gl);
                $je_row1->setGrRow($r);
                $je_row1->setGr($entity);

                $je_row1->setPostingKey(\Finance\Model\Constants::POSTING_KEY_CREDIT);
                $je_row1->setPostingCode(\Finance\Model\Constants::POSTING_KEY_CREDIT);
                $je_row1->setDocAmount($r->getQuantity() * $r->getUnitPrice());
                $je_row1->setLocalAmount($r->getQuantity() * $r->getUnitPrice() * $je->getExchangeRate());
                $je_row1->setJeDescription("Goods Receipt ref." . $r->getRowIdentifer());
                $je_row1->setCreatedBy($u);
                $je_row1->setCreatedOn($entity->getCreatedOn());
                $je_row1->setSysNumber($je->getSysNumber() . "-" . $n);
                $je_row1->setJeDescription("Goods Receipt ref." . $r->getRowIdentifer());
                $this->doctrineEM->persist($je_row1);
            }
        }

        if ($total_credit > 0) {}

        if ($isFlush == True) {
            $this->doctrineEM->flush();
        }
    }

    /**
     *
     * @param \Application\Entity\NmtInventoryOpeningBalance $entity
     * @param array $rows
     * @param \Application\Entity\MlaUsers $u
     * @param boolean $isFlush
     * @throws \Exception
     */
    public function postInventoryOB($entity, $rows, $u, $isFlush = false)
    {
        if (! $entity instanceof \Application\Entity\NmtInventoryOpeningBalance) {
            throw new \Exception("Invalid Argument. Inventory Opening Balance Object is expected!");
        }

        if ($u == null) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }

        if (count($rows) == 0) {
            throw new \Exception("Inventory Opening Balance is empty. No Posting will be made!");
        }

        /**
         * Debit: Inventory, Expenses
         * Credit: Payable to supplier.
         */

        // Create JE

        $je = new \Application\Entity\FinJe();
        $je->setCurrency($entity->getCurrency());
        $je->setLocalCurrency($entity->getLocalCurrency());
        $je->setExchangeRate(1);

        $je->setPostingDate($entity->getPostingDate());
        $je->setDocumentDate($entity->getPostingDate());
        $je->setPostingPeriod($entity->getPostingPeriod());

        $je->getDocType("OB");
        $je->setCreatedBy($u);
        $je->setCreatedOn($entity->getCreatedOn());
        $je->setSysNumber($this->getControllerPlugin()
            ->getDocNumber($je));

        $je->setSourceClass(get_class($entity));
        $je->setSourceId($entity->getId());
        $je->setSourceToken($entity->getToken());

        $this->doctrineEM->persist($je);

        $n = 0;

        foreach ($rows as $r) {
            $n ++;
            /** @var \Application\Entity\NmtProcureGrRow $r ; */

            // No need to create JE for ZERO value row.

            if ($r->getUnitPrice() > 0) {

                // Create JE Row - DEBIT
                $je_row = new \Application\Entity\FinJeRow();
                $je_row->setJe($je);

                $je_row->setGlAccount($r->getGlAccount());
                $je_row->setPostingKey(\Finance\Model\Constants::POSTING_KEY_DEBIT);
                $je_row->setPostingCode(\Finance\Model\Constants::POSTING_KEY_DEBIT);

                $je_row->setDocAmount($r->getQuantity() * $r->getUnitPrice());
                $je_row->setLocalAmount($r->getQuantity() * $r->getUnitPrice() * $je->getExchangeRate());

                $je_row->setJeDescription("Openning Balanace");
                $je_row->setSysNumber($je->getSysNumber() . "-" . $n);

                $je_row->setCreatedBy($u);
                $je_row->setCreatedOn($entity->getCreatedOn());
                $this->doctrineEM->persist($je_row);
            }
        }

        if ($isFlush == True) {
            $this->doctrineEM->flush();
        }
    }
}
