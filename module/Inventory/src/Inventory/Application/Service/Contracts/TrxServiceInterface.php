<?php
namespace Inventory\Application\Service\Contracts;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
interface TrxServiceInterface
{

    public function getDocHeaderByTokenId($id, $token, $locale = 'en_EN');

    public function getDocDetailsByTokenId($id, $token, $outputStrategy = null, $locale = 'en_EN');

    public function getRootEntityOfRow($target_id, $target_token, $entity_id, $entity_token, $locale = 'en_EN');

    public function getDocDetailsByTokenIdFromDB($id, $token, $outputStrategy = null, $locale = 'en_EN');

    public function getDocDetailsByIdFromDB($id, $outputStrategy = null, $locale = 'en_EN');
}
