<?php
namespace Inventory\Domain\Item\Specification;

use Application\Domain\Shared\Specification\AbstractSpecification;
use Inventory\Domain\Item\AbstractItem;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemSpecification extends AbstractSpecification
{

    /**
     *
     * @var AbstractItem
     */
    private $subject;

    /**
     *
     * @param AbstractItem $subject
     */
    public function __construct(AbstractItem $subject)
    {
        $this->subject = $subject;
    }

   /**
    * 
    * @param AbstractItem $subject
    * @return boolean
    */
    public function isSatisfiedBy($subject)
    {
        if ($subject == null)
            return false;

        if ($subject->getItemName() == null)
            return false;

        return true;
    }
}