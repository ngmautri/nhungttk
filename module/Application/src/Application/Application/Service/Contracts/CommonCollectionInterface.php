<?php
namespace Application\Application\Service\Contracts;

/**
 *
 * @author Nguyen Mau Tri
 *
 */
Interface CommonCollectionInterface
{

    public function getUomCollection($companyId = null);

    public function getUomGroupCollection($companyId = null);
}