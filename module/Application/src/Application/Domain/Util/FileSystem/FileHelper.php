<?php
namespace Application\Domain\Util\FileSystem;

use Ramsey\Uuid\Uuid;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class FileHelper
{

    const CHAR_LIST = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";

    public static function generateName()
    {
        $uuid = Uuid::uuid4();
        $name = sprintf("%s_%s", $uuid, uniqid());
        return $name;

        $folder_relative = $name[0] . $name[1] . DIRECTORY_SEPARATOR . $name[2] . $name[3] . DIRECTORY_SEPARATOR . $name[4] . $name[5];
        return $folder_relative;
    }

    public static function generateNameAndPath($level = 3)
    {
        if ($level < 3 or $level > 4) {
            throw new \InvalidArgumentException("Path should be 3 or 4!");
        }

        $uuid = Uuid::uuid4();
        $name = sprintf("%s_%s", $uuid, uniqid());

        $path = null;
        switch ($level) {
            case 3:
                $format = "%s%s/%s%s/%s%s";
                $path = sprintf($format, $name[0], $name[1], $name[2], $name[3], $name[4], $name[5]);
                break;

            case 4:
                $format = "%s%s/%s%s/%s%s/%s%s";
                $path = sprintf($format, $name[0], $name[1], $name[2], $name[3], $name[4], $name[5], $name[6], $name[7]);
                break;
        }

        return [
            "file_name" => $name,
            "path" => $path
        ];
    }

    public static function makePathFromFileName($name, $level = 3)
    {
        if ($level < 3 or $level > 4) {
            throw new \InvalidArgumentException("Path should be 3 or 4!");
        }

        if (strlen($name) < 10) {
            throw new \InvalidArgumentException("File name is too short!");
        }

        $path = null;
        switch ($level) {
            case 3:
                $format = "%s%s/%s%s/%s%s";
                $path = sprintf($format, $name[0], $name[1], $name[2], $name[3], $name[4], $name[5]);
                break;

            case 4:
                $format = "%s%s/%s%s/%s%s/%s%s";
                $path = sprintf($format, $name[0], $name[1], $name[2], $name[3], $name[4], $name[5], $name[6], $name[7]);
                break;
        }

        return $path;
    }
}

