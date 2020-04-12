<?php
namespace Procure\Infrastructure\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Procure\Domain\APInvoice\Repository\ApQueryRepositoryInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class APQueryRepositoryImpl extends AbstractDoctrineRepository implements ApQueryRepositoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\APInvoice\Repository\ApQueryRepositoryInterface::getHeaderIdByRowId()
     */
    public function getHeaderIdByRowId($id)
    {
        $sql = "
	SELECT
    fin_vendor_invoice_row.invoice_id as invoiceId
	FROM fin_vendor_invoice_row
	WHERE id = %s";

        $sql = sprintf($sql, $id);

        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\FinVendorInvoice', 'fin_vendor_invoice_row');
            $rsm->addScalarResult("invoiceId", "invoiceId");
            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            return $query->getSingleResult()["invoiceId"];
        } catch (NoResultException $e) {
            return null;
        }
    }
    
    public function getVersion($id, $token = null)
    {}

   
    public function getVersionArray($id, $token = null)
    {}
}
