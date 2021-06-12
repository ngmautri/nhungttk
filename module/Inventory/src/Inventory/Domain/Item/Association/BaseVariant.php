<?php
namespace Inventory\Domain\Item\Variant;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Event\Company\AccountChart\AccountCreated;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Application\Domain\Util\Collection\GenericCollection;
use Inventory\Domain\Item\Collection\ItemVariantAttributteCollection;
use Inventory\Domain\Item\Repository\ItemCmdRepositoryInterface;
use Inventory\Domain\Item\ValueObject\VariantCode;
use Inventory\Domain\Item\Variant\Validator\VariantValidatorFactory;
use Inventory\Domain\Service\SharedService;
use Inventory\Domain\Service\Contracts\VariantValidationServiceInterface;
use Procure\Domain\AccountPayable\APRowSnapshot;
use Webmozart\Assert\Assert;
use Closure;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class BaseVariant extends AbstractVariant
{

    protected $attributeCollection;

    protected $attributeCollectionRef;

    protected $variantCodeVO;

    public function getLazyAttributeCollection()
    {
        $ref = $this->getAttributeCollectionRef();

        if (! $ref instanceof Closure) {
            $this->attributeCollection = new ItemVariantAttributteCollection();
        } else {
            $this->attributeCollection = $ref();
        }

        return $this->attributeCollection;
    }

    /**
     *
     * @param VariantAttributeSnapshot $snapshot
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @param boolean $storeNow
     * @throws \RuntimeException
     * @return void|\Procure\Domain\AccountPayable\APRowSnapshot
     */
    public function createAttributeFrom(VariantAttributeSnapshot $snapshot, CommandOptions $options, SharedService $sharedService, $storeNow = true)
    {
        Assert::notNull($snapshot, "VariantAttributeSnapshot not founds");
        Assert::notNull($options, "Options not founds");

        $validationService = VariantValidatorFactory::forCreatingVariantAttribute($sharedService);
        // $snapshot->init($options);
        $snapshot->setVariant($this->getId()); // important;

        // var_dump($snapshot->getAttribute());
        $attr = GenericVariantAttribute::createFromSnapshot($this, $snapshot);
        // var_dump($attr);

        $attributeCollection = $this->getAttributeCollection();

        if ($attributeCollection->isExits($attr)) {
            throw new \RuntimeException("Attribute exits already: " . $attr->getAttribute());
        }

        $this->validateAttribute($attr, $validationService);

        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getNotification()->errorMessage());
        }

        // Adding into Collection
        $attributeCollection->add($attr);

        $this->clearEvents();

        if (! $storeNow) {
            return;
        }

        /**
         *
         * @var APRowSnapshot $localSnapshot ;
         * @var ItemCmdRepositoryInterface $rep ;
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $localSnapshot = $rep->storeAttribute($this, $attr);

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

        $event = new AccountCreated($target, $defaultParams, $params);
        $this->addEvent($event);

        return $localSnapshot;
    }

    public function createVariantCodeVO($itemId, $attributes)
    {
        $this->variantCodeVO = new VariantCode($itemId, $attributes);
        $v = $this->getVariantCodeVO()->getValue();
        $this->setCombinedName($v);
        $this->setVariantCode($v);
    }

    public function createVOFromVariantCode()
    {
        $this->variantCodeVO = VariantCode::createFrom($this->getVariantCode());
    }

    /**
     *
     * @param VariantValidationServiceInterface $validatorService
     * @param boolean $isPosting
     */
    public function validate(VariantValidationServiceInterface $validatorService, $isPosting = false)
    {
        $this->validateVariant($validatorService);
        foreach ($this->getAttributeCollection() as $attr) {
            $this->validateAttribute($attr, $validatorService);
        }
    }

    /**
     *
     * @param VariantValidationServiceInterface $validatorService
     * @param boolean $isPosting
     */
    public function validateVariant(VariantValidationServiceInterface $validatorService, $isPosting = false)
    {
        $validatorService->getVariantValidators()->validate($this);

        if ($this->hasErrors()) {
            $this->addErrorArray($this->getErrors());
        }
    }

    /**
     *
     * @param BaseVariantAttribute $attr
     * @param VariantValidationServiceInterface $validationService
     * @param boolean $isPosting
     */
    public function validateAttribute(BaseVariantAttribute $attr, VariantValidationServiceInterface $validationService, $isPosting = false)
    {
        if ($validationService->getVariantAttributeValidators() == null) {
            return;
        }
        $validationService->getVariantAttributeValidators()->validate($this, $attr);
        if ($attr->hasErrors()) {
            $this->addErrorArray($this->getErrors());
        }
    }

    /**
     *
     * @param BaseVariant $other
     * @return boolean
     */
    public function equals(BaseVariant $other)
    {
        if ($other == null) {
            return false;
        }

        return $this->getVariantCodeVO()->equals($other->getVariantCodeVO());
    }

    /**
     *
     * @return \Inventory\Domain\Item\Variant\VariantSnapshot
     */
    public function makeSnapshot()
    {
        $snapshot = new VariantSnapshot();
        GenericObjectAssembler::updateAllFieldsFrom($snapshot, $this);
        return $snapshot;
    }

    public function getAttributeCollection()
    {
        if (! $this->attributeCollection instanceof ItemVariantAttributteCollection) {
            $this->attributeCollection = new ItemVariantAttributteCollection();
            return $this->attributeCollection;
        }
        return $this->attributeCollection;
    }

    /**
     *
     * @param GenericCollection $attributeCollection
     */
    public function setAttributeCollection(GenericCollection $attributeCollection)
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

    /**
     *
     * @return \Inventory\Domain\Item\ValueObject\VariantCode
     */
    public function getVariantCodeVO()
    {
        return $this->variantCodeVO;
    }
}
