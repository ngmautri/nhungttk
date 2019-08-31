<?php
namespace Procure\Infrastructure\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Procure\Domain\QuotationRequest\AbstractQR;
use Procure\Domain\QuotationRequest\QRCmdRepositoryInterface;
use Procure\Domain\QuotationRequest\QRRow;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DoctrineQRCmdRepository extends AbstractDoctrineRepository implements QRCmdRepositoryInterface
{

    public function storeRow(AbstractQR $rootEntity, QRRow $row, $isPosting = false)
    {}

    public function post(AbstractQR $rootEntity, $generateSysNumber = True)
    {}

    public function createRow($id, AbstractQR $row, $isPosting = false)
    {}

    public function storeHeader(AbstractQR $rootEntity, $generateSysNumber = false, $isPosting = false)
    {}

    public function store(AbstractQR $rootEntity, $generateSysNumber = false, $isPosting = false)
    {}
}
