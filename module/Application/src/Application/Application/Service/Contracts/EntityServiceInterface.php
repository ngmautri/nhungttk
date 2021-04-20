<?php
namespace Application\Application\Service\Contracts;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
interface EntityServiceInterface
{

    public function getRootEntityByUuid($uuid, $locale = 'en_EN');

    public function getRootEntityById($id, $locale = 'en_EN');

    public function getRootEntityByTokenId($id, $token, $locale = 'en_EN');

    public function getRootEntityDetailByTokenId($id, $token, $outputStrategy = null, $locale = 'en_EN');

    public function getRootEntityOfMember($target_id, $target_token, $entity_id, $entity_token, $locale = 'en_EN');

    public function getRootEntityByTokenIdFromDB($id, $token, $outputStrategy = null, $locale = 'en_EN');

    public function getRootEntityByIdFromDB($id, $outputStrategy = null, $locale = 'en_EN');
}
