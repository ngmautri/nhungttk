<?php
namespace Procure\Domain\Service\Contracts;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
interface SharedServiceInterface
{

    public function getSharedSpecificationFactory();

    public function getDomainSpecificationFactory();

    /**
     *
     * @return PostingServiceInterface
     */
    public function getPostingService();

    public function getFxService();
}
