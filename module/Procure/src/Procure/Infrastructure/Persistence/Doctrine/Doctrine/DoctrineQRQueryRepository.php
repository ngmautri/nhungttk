<?php
namespace Procure\Infrastructure\Doctrine;

use Procure\Domain\QuotationRequest\QRQueryRepositoryInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DoctrineQRQueryRepository implements QRQueryRepositoryInterface
{

    public function getHeaderById($id, $token = null)
    {}

    public function getById($id, $outputStragegy = null)
    {}

    public function getByUUID($uuid)
    {}

    public function findAll()
    {}
}
