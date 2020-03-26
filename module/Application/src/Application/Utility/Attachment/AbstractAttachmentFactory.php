<?php
namespace Application\Utility\Attachment;

use Doctrine\ORM\EntityManager;
use Zend\Math\Rand;

/**
 *
 * @author nmt
 *        
 */
abstract class AbstractAttachmentFactory
{

    const CHAR_LIST = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";

    protected $attachmentFile;

    protected $attachmentHeader;

    protected $doctrineEM;

    protected $target;

    /**
     *
     * @param unknown $target
     * @param unknown $attachmentHeader
     * @param unknown $attachmentFile
     */
    public function __construct($target, $attachmentHeader, $attachmentFile)
    {
        $this->setTarget($target);

        $file_name = $attachmentFile['name'];
        $file_size = $attachmentFile['size'];
        $file_tmp = $attachmentFile['tmp_name'];
        $file_type = $attachmentFile['type'];
        $file_ext = strtolower(end(explode('.', $attachmentFile['name'])));

        $ext = '';
        $isPicture = 0;

        if (preg_match('/(jpg|jpeg)$/', $file_type)) {
            $ext = 'jpg';
            $isPicture = 1;
        } else if (preg_match('/(gif)$/', $file_type)) {
            $ext = 'gif';
            $isPicture = 1;
        } else if (preg_match('/(png)$/', $file_type)) {
            $ext = 'png';
            $isPicture = 1;
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
        } else if (preg_match('/(octet-stream)$/', $file_type)) {
            $ext = $file_ext;
        }

        $checksum = md5_file($file_tmp);

        $name_part1 = Rand::getString(6, self::CHAR_LIST, true) . "_" . Rand::getString(10, self::CHAR_LIST, true);

        $name = md5($checksum . uniqid(microtime())) . '_' . $name_part1 . '.' . $ext;

        $folder_relative = $name[0] . $name[1] . DIRECTORY_SEPARATOR . $name[2] . $name[3] . DIRECTORY_SEPARATOR . $name[4] . $name[5];
        $folder = ROOT . self::ATTACHMENT_FOLDER . DIRECTORY_SEPARATOR . $folder_relative;

        if (! is_dir($folder)) {
            mkdir($folder, 0777, true); // important
        }

        // echo ("$folder/$name");
        move_uploaded_file($file_tmp, "$folder/$name");

        $this->attachmentFile;
    }

    /**
     */
    public function validateAttachment()
    {}

    /**
     *
     * @return \Application\Entity\NmtApplicationAttachment;
     */
    abstract public function saveAttachment();

    /**
     *
     * @return the $attachmentFile
     */
    public function getAttachmentFile()
    {
        return $this->attachmentFile;
    }

    /**
     *
     * @return the $doctrineEM
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     *
     * @param field_type $attachmentFile
     */
    public function setAttachmentFile($attachmentFile)
    {
        $this->attachmentFile = $attachmentFile;
    }

    /**
     *
     * @param EntityManager $doctrineEM
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
    }

    /**
     *
     * @return the $target
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     *
     * @param field_type $target
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }
}
