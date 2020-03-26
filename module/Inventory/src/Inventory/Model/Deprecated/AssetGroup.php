<?php
namespace Inventory\Model;

/**
 *
 * @author nmt
 *        
 */
class AssetGroup
{

    public $id;

    public $category_id;

    public $name;

    public $description;

    public $created_on;

    public function exchangeArray($data)
    {
        $this->id = (! empty($data['id'])) ? $data['id'] : null;
        $this->category_id = (! empty($data['category_id'])) ? $data['category_id'] : null;
        $this->name = (! empty($data['description'])) ? $data['description'] : null;
        $this->description = (! empty($data['description'])) ? $data['description'] : null;
        $this->created_on = (! empty($data['created_on'])) ? $data['created_on'] : null;
    }
}

