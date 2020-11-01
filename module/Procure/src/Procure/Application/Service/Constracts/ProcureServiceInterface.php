<?php
namespace Procure\Application\Service\Contracts;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
interface ProcureServiceInterface
{

    public function getDocHeaderByTokenId($id, $token);

    public function getDocDetailsByTokenId($id, $token, $outputStrategy = null);

    public function getRootEntityOfRow($target_id, $target_token, $entity_id, $entity_token);

    public function getDocDetailsByTokenIdFromDB($id, $token, $outputStrategy = null);

    public function getDocDetailsByIdFromDB($id, $outputStrategy = null);
}
