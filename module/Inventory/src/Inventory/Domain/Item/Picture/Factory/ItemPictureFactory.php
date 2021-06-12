<?php
namespace Inventory\Domain\Item\Picture\Factory;

use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Inventory\Domain\Item\Picture\GenericPicture;
use Inventory\Domain\Item\Picture\PictureSnapshot;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ItemPictureFactory
{

    public static function contructFromDB(PictureSnapshot $snapshot)
    {
        if (! $snapshot instanceof PictureSnapshot) {
            throw new InvalidArgumentException("PictureSnapshot not found!");
        }

        $instance = new GenericPicture();
        GenericObjectAssembler::updateAllFieldsFrom($instance, $snapshot);

        return $instance;
    }
}