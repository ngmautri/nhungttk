<?php
namespace Inventory\Domain\Association\Contracts;

/**
 * Default item association
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
final class DefaultAssociationType
{

    const SIMILAR = 1;

    const IS_SPARE_PART_OF = 2;

    const IS_PART_OF = 3;

    const REPLACEMENT = 4;

    const ALTERNATIVE = 5;

    const IS_ACCESSORY_OF = 6;
}
