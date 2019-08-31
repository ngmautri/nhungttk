<?php
namespace Procure\Domain\Service;


/**
 * Transaction Domain Service
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface QueryRepositoryFactoryInteface
{
    public function createPRQueryRepository();
    public function createQRQueryRepository();
    public function createPOQueryRepository();    
    public function createAPQueryRepository();
}
