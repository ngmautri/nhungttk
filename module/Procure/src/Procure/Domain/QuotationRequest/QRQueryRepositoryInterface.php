<?php
namespace Procure\Domain\QuotationRequest;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface QRQueryRepositoryInterface
{

    public function findAll();

    public function getById($id, $outputStragegy = null);

    public function getHeaderById($id, $token = null);

    public function getByUUID($uuid);
    
    public function getGrDetailsByTokenId($id, $token = null);
}
