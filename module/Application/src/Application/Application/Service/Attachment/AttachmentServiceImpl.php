<?php
namespace Application\Application\Service\Attachment;

use Application\Domain\Attachment\AttachmentFileSnapshot;
use Application\Domain\Attachment\Contracts\AttachmentServiceInterface;
use Application\Domain\Util\FileSystem\FileHelper;
use Application\Domain\Util\FileSystem\MimeType;
use Cake\Filesystem\File;

/**
 * Attachment Service
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AttachmentServiceImpl implements AttachmentServiceInterface
{

    public function createAttachmentFileFrom($path)
    {
        $file = new File($path);
        if (! $file->exists()) {
            throw new \InvalidArgumentException("File not exits => " . $file->name());
        }

        // var_dump($file->info());
        $file_info = $file->info();

        $file_dest = FileHelper::generateNameAndPath();

        $snapshot = new AttachmentFileSnapshot();

        if (isset($file_info['basename'])) {
            $snapshot->setFileNameOriginal($file_info['basename']);
        }

        if (isset($file_info['extension'])) {
            $snapshot->setExtension($file_info['extension']);
        }

        $snapshot->setFileSize($file->size());
        $snapshot->setMime($file->mime());
        $snapshot->setFileName($file_dest['file_name']);
        $snapshot->setRelativePath($file_dest['path']);
        $snapshot->setRelativePath($file_dest['path']);
        $snapshot->setIsPicture(MimeType::isPicture($file->mime()));
        $snapshot->setChecksum(md5_file($path));

        return $snapshot;
    }
}
