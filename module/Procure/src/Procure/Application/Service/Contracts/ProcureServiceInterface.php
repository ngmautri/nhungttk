<?php
namespace Procure\Application\Service\Contracts;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface ProcureServiceInterface
{

    public function getDocHeaderByTokenId($id, $token, $locale = 'en_EN');

    public function getDocDetailsByTokenId($id, $token, $outputStrategy = null, $locale = 'en_EN');

    public function getRootEntityOfRow($target_id, $target_token, $entity_id, $entity_token, $locale = 'en_EN');

    public function getDocDetailsByTokenIdFromDB($id, $token, $outputStrategy = null, $locale = 'en_EN');

    public function getDocDetailsByIdFromDB($id, $outputStrategy = null, $locale = 'en_EN');

    public function getDocGirdByTokenId($id, $token, $offset = null, $limit = null, $locale = 'en_EN');
}
