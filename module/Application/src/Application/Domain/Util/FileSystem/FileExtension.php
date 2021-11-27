<?php
namespace Application\Domain\Util\FileSystem;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class FileExtension
{

    static public function isSpreadsheetSupported($ext)
    {
        $supported = array(
            "xlsx",
            "xls",
            "csv"
        );
        return in_array($ext, $supported);
    }

    static public function get($file_type)
    {
        if ($file_type == null) {
            return null;
        }

        $ext = null;
        if (preg_match('/(jpg|jpeg)$/', $file_type)) {
            $ext = 'jpg';
        } else if (preg_match('/(gif)$/', $file_type)) {
            $ext = 'gif';
        } else if (preg_match('/(png)$/', $file_type)) {
            $ext = 'png';
        } else if (preg_match('/(pdf)$/', $file_type)) {
            $ext = 'pdf';
        } else if (preg_match('/(vnd.ms-excel)$/', $file_type)) {
            $ext = 'xls';
        } else if (preg_match('/(vnd.openxmlformats-officedocument.spreadsheetml.sheet)$/', $file_type)) {
            $ext = 'xlsx';
        } else if (preg_match('/(msword)$/', $file_type)) {
            $ext = 'doc';
        } else if (preg_match('/(vnd.openxmlformats-officedocument.wordprocessingml.document)$/', $file_type)) {
            $ext = 'docx';
        } else if (preg_match('/(x-zip-compressed)$/', $file_type)) {
            $ext = 'zip';
        }
        return $ext;
    }
}

