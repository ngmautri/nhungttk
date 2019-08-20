<?php
namespace Procure\Infrastructure\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Procure\Domain\APInvoice\APInvoiceCmdRepositoryInterface;
use Procure\Domain\APInvoice\APInvoiceRow;
use Procure\Domain\APInvoice\GenericAPInvoice;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DoctrineAPInvoiceCmdRepository extends AbstractDoctrineRepository implements APInvoiceCmdRepositoryInterface
{

    public function storeRow(GenericAPInvoice $inv, APInvoiceRow $row, $isPosting = false)
    {}

    public function post(GenericAPInvoice $inv, $generateSysNumber = True)
    {}

    public function createRow($invId, APInvoiceRow $row, $isPosting = false)
    {}

    public function storeHeader(GenericAPInvoice $inv, $generateSysNumber = false, $isPosting = false)
    {}

    public function store(GenericAPInvoice $inv, $generateSysNumber = false, $isPosting = false)
    {}
}
