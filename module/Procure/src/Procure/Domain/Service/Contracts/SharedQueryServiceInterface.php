<?php
namespace Procure\Domain\Service\Contracts;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface SharedQueryServiceInterface
{

    public function getAPQueryRepository();

    public function getPRQueryRepository();

    public function getPOQueryRepository();

    public function getGRQueryRepository();

    public function getQRQueryRepository();
}
