<?php
namespace Application\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class FinAccountRepository extends EntityRepository
{

    /** @var \Application\Entity\FinAccount $e*/
    // @ORM\Entity(repositoryClass="Application\Repository\FinAccountRepository")
    
    public function getAccountList($is_active = 1, $current_state = null, $filter_by = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0)
    {
        $sql = "Select * from fin_account Where 1";

        if ($is_active == 1) {
            $sql = $sql . " AND fin_account.is_active=1 ";
        } elseif ($is_active == - 1) {
            $sql = $sql . " AND fin_account.is_active = 0 ";
        }

        switch ($sort_by) {
            case "invoiceDate":
                //$sql = $sql . " ORDER BY fin_vendor_invoice.invoice_date " . $sort;
                break;
            case "grossAmount":
                //$sql = $sql . " ORDER BY SUM(CASE WHEN fin_vendor_invoice_row.is_active =1 THEN (fin_vendor_invoice_row.gross_amount) ELSE 0 END) " . $sort;
                break;
            case "createdOn":
                $sql = $sql . " ORDER BY fin_account.created_on " . $sort;
                break;
            case "accountNumber":
                $sql = $sql . " ORDER BY fin_account.account_number " . $sort;
                break;
            case "currencyCode":
                //$sql = $sql . " ORDER BY fin_vendor_invoice.currency_iso3 " . $sort;
                break;
        }

        if ($limit > 0) {
            $sql = $sql . " LIMIT " . $limit;
        }

        if ($offset > 0) {
            $sql = $sql . " OFFSET " . $offset;
        }
        $sql = $sql . ";";

        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\FinAccount', 'fin_account');
            $query = $this->_em->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }
}

