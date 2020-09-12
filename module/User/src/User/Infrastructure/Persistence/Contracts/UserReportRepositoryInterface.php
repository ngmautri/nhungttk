<?php
namespace User\Infrastructure\Persistence\Contracts;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface UserReportRepositoryInterface
{

    public function getUserList($idList);

    public function isAdministrator($userId);

    public function getRoleByUserId($userId);

    public function getOtherAgentOfWfTransition($transition_id, $limit = 0, $offset = 0);
}
