<?php
namespace User\Infrastructure\Persistence;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface UserRepositoryInterface
{

    public function isAdministrator($userId);

    public function getRoleByUserId($userId);

    public function getOtherAgentOfWfTransition($transition_id, $limit = 0, $offset = 0);
}
