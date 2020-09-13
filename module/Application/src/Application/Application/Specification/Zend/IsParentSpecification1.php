<?php
namespace Application\Application\Specification\Zend;

use User\Infrastructure\Persistence\UserRepositoryInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class IsParentSpecification extends DoctrineSpecification
{

    private $userRepository;

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecification::isSatisfiedBy()
     */
    public function isSatisfiedBy($subject)
    {
        $userId1 = null;
        if (isset($subject["userId1"])) {
            $userId1 = $subject["userId1"];
        }

        // var_dump($subject);

        $userId2 = null;
        if (isset($subject["userId2"])) {
            $userId2 = $subject["userId2"];
        }

        if ($this->doctrineEM == null || $userId2 == null || $userId1 == null || $this->userRepository == null)
            return false;

        $criteria = array(
            'id' => $userId1
        );
        $user1 = $this->doctrineEM->getRepository('\Application\Entity\MlaUsers')->findOneBy($criteria);

        $criteria = array(
            'id' => $userId2
        );
        $user2 = $this->doctrineEM->getRepository('\Application\Entity\MlaUsers')->findOneBy($criteria);

        $result = $this->isParent($user1, $user2);

        return ($result["result"]);
    }

    /**
     * to check, if user1 is parent of user2
     *
     * @param \Application\Entity\MlaUsers $user1
     * @param \Application\Entity\MlaUsers $user2
     */
    public function isParent(\Application\Entity\MlaUsers $user1, \Application\Entity\MlaUsers $user2)
    {
        $result = array();

        if (! $user1 instanceof \Application\Entity\MlaUsers or ! $user2 instanceof \Application\Entity\MlaUsers) {

            $result['result'] = 0;
            $result['message'] = ' User not found';
            return $result;
        }

        if ($user1 === $user2) {
            $result['result'] = 1;
            $result['message'] = 'Owner operation';
            return $result;
        }

        $isAdmin = $this->userRepository->isAdministrator($user1);

        if ($isAdmin == true) {
            $result['result'] = 1;
            $result['message'] = 'User is administrator.';
            return $result;
        }

        // Get of User 1
        $criteria = array(
            'user' => $user1
        );

        /**@var \Application\Entity\NmtApplicationAclUserRole $role1 ;*/
        $role1 = $this->doctrineEM->getRepository('\Application\Entity\NmtApplicationAclUserRole')->findOneBy($criteria);

        if ($role1 == null) {
            $result['result'] = 0;
            $result['message'] = ' User not found';
            return $result;
        }

        // Get of User 2
        $criteria = array(
            'user' => $user2
        );
        /**@var \Application\Entity\NmtApplicationAclUserRole $role2 ;*/
        $role2 = $this->doctrineEM->getRepository('\Application\Entity\NmtApplicationAclUserRole')->findOneBy($criteria);
        if ($role2 == null) {
            $result['result'] = 0;
            $result['message'] = ' User not found';
            return $result;
        }

        $path_array = explode("/", $role2->getRole()->getPath());
        $role_level = array();
        $test = '';

        if (count($path_array) > 0) {
            $level = 0;
            foreach ($path_array as $a) {
                $level ++;
                $tmp = array(
                    $a => $level
                );
                $role_level[] = $tmp;

                $test = $test . '/' . $a;
            }
        }

        $ck_level = $this->_isParent($role1->getRole()
            ->getId(), $role_level);

        if ($ck_level > 0 and $ck_level <= $role2->getRole()->getPath()) {
            $result['result'] = 1;
            $result['message'] = $role1->getRole()->getId() . ' allowed ' . $test;
        } else {

            $result['result'] = 0;
            $result['message'] = $role1->getRole()->getId() . ' not allowed ' . $test;
        }

        return $result;
    }

    /**
     *
     * @param int $roleId
     * @param array $roles
     * @return int;
     */
    private function _isParent($roleId, array $role_level)
    {
        if (count($role_level) == 0) {
            return - 1;
        }

        foreach ($role_level as $l) {

            foreach ($l as $k => $v) {

                if ($k == $roleId) {
                    return $v;
                }
            }
        }
        return - 1;
    }

    /**
     *
     * @return mixed
     */
    public function getUserId1()
    {
        return $this->userId1;
    }

    /**
     *
     * @return mixed
     */
    public function getUserId2()
    {
        return $this->userId2;
    }

    /**
     *
     * @param mixed $userId1
     */
    public function setUserId1($userId1)
    {
        $this->userId1 = $userId1;
    }

    /**
     *
     * @param mixed $userId2
     */
    public function setUserId2($userId2)
    {
        $this->userId2 = $userId2;
    }

    /**
     *
     * @return \User\Infrastructure\Persistence\UserRepositoryInterface
     */
    public function getUserRepository()
    {
        return $this->userRepository;
    }

    /**
     *
     * @param UserRepositoryInterface $userRepository
     */
    public function setUserRepository(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }
}