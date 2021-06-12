<?php
namespace Inventory\Domain\Item\Picture;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PictureDefinition
{

    public static $fields = [
        "id",
        "documentSubject",
        "url",
        "filename",
        "originalFilename",
        "filetype",
        "size",
        "visibility",
        "folder",
        "folderRelative",
        "checksum",
        "token",
        "remarks",
        "isDefault",
        "isActive",
        "markedForDeletion",
        "createdOn",
        "fileExits",
        "createdBy",
        "item"
    ];

    public static $defaultExcludedFields = [];

    public static $defaultIncludedFields = [];
}
