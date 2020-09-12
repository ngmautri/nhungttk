<?php
namespace User\Infrastructure\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use User\Domain\User\GenericUser;
use User\Domain\User\UserSnapshot;
use User\Domain\User\Repository\UserCmdRepositoryInterface;
use User\Infrastructure\Mapper\UserMapper;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class UserCmdRepositoryImpl extends AbstractDoctrineRepository implements UserCmdRepositoryInterface
{

    const ROOT_ENTITY_NAME = "\Application\Entity\MlaUser";

    /**
     *
     * {@inheritdoc}
     * @see \User\Domain\User\Repository\UserCmdRepositoryInterface::storeUser()
     */
    public function storeUser(GenericUser $rootEntity, $generateSysNumber = false, $isPosting = false)
    {
        $rootSnapshot = $this->_getRootSnapshot($rootEntity);

        $isFlush = true;
        $increaseVersion = true;

        /**
         *
         * @var \Application\Entity\MlaUsers $entity
         */
        $entity = $this->_storeUser($rootSnapshot, $generateSysNumber, $isPosting, $isFlush, $increaseVersion);

        if ($entity == null) {
            throw new InvalidArgumentException("Something wrong. Doctrine root entity not created");
        }

        $rootSnapshot->id = $entity->getId();
        return $rootSnapshot;
    }

    /**
     *
     * @param UserSnapshot $rootSnapshot
     * @param bool $generateSysNumber
     * @param bool $isPosting
     * @param bool $isFlush
     * @param bool $increaseVersion
     * @throws InvalidArgumentException
     * @return \Application\Entity\MlaUsers
     */
    private function _storeUser(UserSnapshot $rootSnapshot, $generateSysNumber, $isPosting, $isFlush, $increaseVersion)
    {
        if ($rootSnapshot == null) {
            throw new InvalidArgumentException("Root snapshot not given.");
        }

        /**
         *
         * @var \Application\Entity\MlaUsers $entity ;
         *     
         */
        if ($rootSnapshot->getId() > 0) {
            $entity = $this->getDoctrineEM()->find(self::ROOT_ENTITY_NAME, $rootSnapshot->getId());
            if ($entity == null) {
                throw new InvalidArgumentException(sprintf("Doctrine entity not found. %s", $rootSnapshot->getId()));
            }

            // just in case, it is not updated.
            if ($entity->getToken() == null) {
                $entity->setToken($entity->getUuid());
            }
        } else {
            $rootClassName = self::ROOT_ENTITY_NAME;
            $entity = new $rootClassName();
        }

        // Populate with data
        $entity = UserMapper::mapSnapshotEntity($this->getDoctrineEM(), $rootSnapshot, $entity);

        if ($generateSysNumber) {
            $entity->setSysNumber($this->generateSysNumber($entity));
        }

        if ($increaseVersion) {
            // Optimistic Locking
            if ($rootSnapshot->getId() > 0) {
                $entity->setRevisionNo($entity->getRevisionNo() + 1);
            }
        }

        $this->doctrineEM->persist($entity);

        if ($isFlush) {
            $this->doctrineEM->flush();
        }

        return $entity;
    }

    /**
     *
     * @param GenericUser $rootEntity
     * @throws InvalidArgumentException
     * @return \User\Domain\User\UserSnapshot
     */
    private function _getRootSnapshot(GenericUser $rootEntity)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("Root entity not given!");
        }

        /**
         *
         * @todo
         * @var UserSnapshot $rootSnapshot ;
         */
        $rootSnapshot = $rootEntity->makeSnapshot();
        if (! $rootSnapshot instanceof UserSnapshot) {
            throw new InvalidArgumentException("Root snapshot not created!");
        }

        return $rootSnapshot;
    }
}