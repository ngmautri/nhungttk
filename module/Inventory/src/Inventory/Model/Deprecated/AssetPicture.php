<?php
namespace Inventory\Model;

/**
 *
 * @author nmt
 *        
 */
class AssetPicture
{

    public $id;

    public $asset_id;

    public $url;

    public $size;

    public $filetype;

    public $visibility;

    public $comments;

    public $uploaded_on;

    public $filename;

    public $folder;

    public $checksum;

    public function exchangeArray($data)
    {
        $this->id = (! empty($data['id'])) ? $data['id'] : null;
        $this->asset_id = (! empty($data['asset_id'])) ? $data['asset_id'] : null;
        $this->url = (! empty($data['url'])) ? $data['url'] : null;

        $this->filetype = (! empty($data['filetype'])) ? $data['filetype'] : null;
        $this->size = (! empty($data['size'])) ? $data['size'] : null;

        $this->comments = (! empty($data['comments'])) ? $data['comments'] : null;
        $this->uploaded_on = (! empty($data['uploaded_on'])) ? $data['uploaded_on'] : null;
        $this->filename = (! empty($data['filename'])) ? $data['filename'] : null;
        $this->folder = (! empty($data['folder'])) ? $data['folder'] : null;
        $this->checksum = (! empty($data['checksum'])) ? $data['checksum'] : null;
    }
}

