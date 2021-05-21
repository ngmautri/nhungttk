<?php
namespace Application\Domain\Company\ItemAttribute;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Company\ItemAttribute\Repository\ItemAttributeCmdRepositoryInterface;
use Application\Domain\Company\ItemAttribute\Tree\DefaultItemAttributeGroupTree;
use Application\Domain\Company\ItemAttribute\Validator\ItemAttributeValidatorFactory;
use Application\Domain\Company\Repository\CompanyCmdRepositoryInterface;
use Application\Domain\Event\Company\ItemAttribute\AttributeCreated;
use Application\Domain\Event\Company\ItemAttribute\AttributeRemoved;
use Application\Domain\Event\Company\ItemAttribute\AttributeUpdated;
use Application\Domain\Service\Contracts\ItemAttributeValidationServiceInterface;
use Application\Domain\Service\Contracts\SharedServiceInterface;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Application\Domain\Util\Translator;
use Doctrine\Common\Collections\ArrayCollection;
use Webmozart\Assert\Assert;
use Closure;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class BaseAttributeGroup extends AbstractAttributeGroup
{

    private $attributeCollection;

    private $attributeCollectionRef;

    /**
     *
     * @param BaseAttributeSnapshot $snapshot
     * @param CommandOptions $options
     * @param SharedServiceInterface $sharedService
     * @param boolean $storeNow
     * @throws \RuntimeException
     * @return \Application\Domain\Company\ItemAttribute\BaseAttributeGroup|\Application\Domain\Company\ItemAttribute\AttributeSnapshot
     */
    public function createAttributeFrom(BaseAttributeSnapshot $snapshot, CommandOptions $options, SharedServiceInterface $sharedService, $storeNow = true)
    {
        Assert::notNull($snapshot, "Account Snapshot not founds");
        Assert::notNull($options, "Options not founds");

        $validationService = ItemAttributeValidatorFactory::forCreatingAttribute($sharedService);
        $snapshot->init($options);
        $snapshot->setGroup($this->getId()); // important;
        $attribute = GenericAttribute::createFromSnapshot($this, $snapshot);

        $this->validateAttribute($attribute, $validationService);

        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getNotification()->errorMessage());
        }

        $this->clearEvents();
        $this->getAttributeCollection()->add($attribute);

        if (! $storeNow) {
            return $this;
        }

        /**
         *
         * @var AttributeSnapshot $localSnapshot ;
         * @var CompanyCmdRepositoryInterface $rep ;
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $localSnapshot = $rep->storeAttribute($this, $attribute);

        $params = [
            "rowId" => $localSnapshot->getId(),
            "rowToken" => $localSnapshot->getToken()
        ];

        $target = $this->makeSnapshot();
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($this->getId());
        $defaultParams->setTargetToken($this->getToken());
        // $defaultParams->setTargetDocVersion($this->getDocVersion());
        // $defaultParams->setTargetRrevisionNo($this->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());

        $event = new AttributeCreated($target, $defaultParams, $params);
        $this->addEvent($event);

        return $localSnapshot;
    }

    /**
     *
     * @param BaseAttribute $attribute
     * @param BaseAttributeSnapshot $snapshot
     * @param CommandOptions $options
     * @param SharedServiceInterface $sharedService
     * @param boolean $storeNow
     * @throws \RuntimeException
     * @return \Application\Domain\Company\ItemAttribute\BaseAttributeGroup|\Application\Domain\Company\ItemAttribute\BaseAttributeSnapshot
     */
    public function updateAttributeFrom(BaseAttribute $attribute, BaseAttributeSnapshot $snapshot, CommandOptions $options, SharedServiceInterface $sharedService, $storeNow = true)
    {
        Assert::notNull($attribute, Translator::translate("BaseAttribute entity not found!"));
        Assert::notNull($snapshot, Translator::translate("$snapshot snapshot not found!"));

        $validationService = ItemAttributeValidatorFactory::forCreatingAttribute($sharedService);

        AttributeSnapshotAssembler::updateDefaultExcludedFieldsFrom($snapshot, $attribute);

        $this->validateAttribute($attribute, $validationService);

        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getNotification()->errorMessage());
        }

        $this->clearEvents();
        // $this->addRow($account);

        if (! $storeNow) {
            return $this;
        }

        /**
         *
         * @var BaseAttributeSnapshot $localSnapshot ;
         * @var CompanyCmdRepositoryInterface $rep ;
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $localSnapshot = $rep->storeAttribute($this, $attribute);

        $params = [
            "rowId" => $localSnapshot->getId(),
            "rowToken" => $localSnapshot->getToken()
        ];

        $target = $this->makeSnapshot();
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($this->getId());
        $defaultParams->setTargetToken($this->getToken());
        // $defaultParams->setTargetDocVersion($this->getDocVersion());
        // $defaultParams->setTargetRrevisionNo($this->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());

        $event = new AttributeUpdated($target, $defaultParams, $params);
        $this->addEvent($event);

        return $localSnapshot;
    }

    /**
     *
     * @param int $id
     * @throws \InvalidArgumentException
     * @return \Application\Domain\Company\ItemAttribute\BaseAttribute
     */
    public function getAttributeById($id)
    {
        $collection = $this->getLazyAttributeCollection();
        if ($collection->isEmpty()) {
            throw new \InvalidArgumentException("Attribute id [$id] not found!");
        }

        foreach ($collection as $e) {
            /**
             *
             * @var BaseAttribute $e
             */
            if ($e->getId() == $id) {
                return $e;
            }
        }

        throw new \InvalidArgumentException("Attribute id [$id] not found!");
    }

    /**
     *
     * @param string $name
     * @throws \InvalidArgumentException
     * @return \Application\Domain\Company\ItemAttribute\BaseAttribute
     */
    public function getAttributeByNumber($name)
    {
        $collection = $this->getLazyAttributeCollection();
        if ($collection->isEmpty()) {
            throw new \InvalidArgumentException("Attribute [$name] not found!");
        }

        foreach ($collection as $e) {
            /**
             *
             * @var BaseAttribute $e
             */
            if (\strtolower(trim($e->getAttributeName())) == \strtolower(trim($name))) {
                return $e;
            }
        }

        throw new \InvalidArgumentException("Attribute number [$name] not found!");
    }

    /**
     *
     * @param BaseAttribute $localEntity
     * @param CommandOptions $options
     * @param SharedServiceInterface $sharedService
     * @return \Application\Domain\Company\ItemAttribute\BaseAttributeGroup
     */
    public function removeAttribute(BaseAttribute $localEntity, CommandOptions $options, SharedServiceInterface $sharedService)
    {
        Assert::notNull($localEntity, "BaseAccount not found");
        Assert::notNull($options, "Options not founds");
        Assert::notNull($sharedService, "SharedService not found");

        /**
         *
         * @var ItemAttributeCmdRepositoryInterface $rep ;
         *
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rep->removeAttribute($this, $localEntity);

        $params = null;

        $target = $localEntity->makeSnapshot();
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($localEntity->getId());
        $defaultParams->setTargetToken($this->getToken());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());

        $event = new AttributeRemoved($target, $defaultParams, $params);
        $this->addEvent($event);

        return $this;
    }

    /**
     *
     * @param ItemAttributeValidationServiceInterface $validatorService
     * @param boolean $isPosting
     */
    public function validate(ItemAttributeValidationServiceInterface $validatorService, $isPosting = false)
    {
        $this->validateAttributeGroup($validatorService);
        foreach ($this->getAttributeCollection() as $attribute) {
            $this->validateAttribute($attribute, $validatorService);
        }
    }

    /**
     *
     * @param ItemAttributeValidationServiceInterface $validatorService
     * @param boolean $isPosting
     */
    public function validateAttributeGroup(ItemAttributeValidationServiceInterface $validatorService, $isPosting = false)
    {
        $validatorService->getAttributeGroupValidators()->validate($this);

        if ($this->hasErrors()) {
            $this->addErrorArray($this->getErrors());
        }
    }

    /**
     *
     * @param BaseAttribute $attribute
     * @param ItemAttributeValidationServiceInterface $validationService
     * @param boolean $isPosting
     */
    public function validateAttribute(BaseAttribute $attribute, ItemAttributeValidationServiceInterface $validationService, $isPosting = false)
    {
        if ($validationService->getAttributeValidators() == null) {
            return;
        }
        $validationService->getAttributeValidators()->validate($this, $attribute);

        if ($attribute->hasErrors()) {
            $this->addErrorArray($this->getErrors());
        }
    }

    /**
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getLazyAttributeCollection()
    {
        $ref = $this->getAttributeCollectionRef();
        if (! $ref instanceof Closure) {
            return new ArrayCollection();
        }

        $this->attributeCollection = $ref();
        return $this->attributeCollection;
    }

    /**
     *
     * @return \Application\Domain\Company\ItemAttribute\Tree\DefaultItemAttributeGroupTree
     */
    public function createAttributeTree()
    {
        $tree = new DefaultItemAttributeGroupTree($this);
        $tree->initTree();
        $tree->createTree($this->getGroupName(), 0);
        return $tree;
    }

    public function makeSnapshot()
    {
        $snapshot = new BaseAttributeGroupSnapshot();
        GenericObjectAssembler::updateAllFieldsFrom($snapshot, $this);
        return $snapshot;
    }

    public function equals(BaseAttributeGroup $other)
    {
        if ($other == null) {
            return false;
        }

        return \strtolower(trim($this->getGroupCode())) == \strtolower(trim($other->getGroupCode()));
    }

    /*
     * |=================================
     * | GETTER AND SETTER
     * |
     * |==================================
     */

    /**
     *
     * @return mixed
     */
    public function getAttributeCollection()
    {
        if ($this->accountCollection == null) {
            return new ArrayCollection();
        }
        return $this->accountCollection;
    }

    /**
     *
     * @param mixed $attributeCollection
     */
    public function setAttributeCollection($attributeCollection)
    {
        $this->attributeCollection = $attributeCollection;
    }

    /**
     *
     * @return Closure
     */
    public function getAttributeCollectionRef()
    {
        return $this->attributeCollectionRef;
    }

    /**
     *
     * @param Closure $attributeCollectionRef
     */
    public function setAttributeCollectionRef(Closure $attributeCollectionRef)
    {
        $this->attributeCollectionRef = $attributeCollectionRef;
    }
}
