<?php
namespace Inventory\Model;

/**
 *
 * @author nmt
 *        
 */
class ArticleCategory
{

    public $id;

    public $name;

    public $description;

    public $parent_id;

    public $path;

    public $path_depth;

    public $created_on;

    public $created_by;

    public function exchangeArray($data)
    {
        $this->id = (! empty($data['id'])) ? $data['id'] : null;
        $this->name = (! empty($data['name'])) ? $data['name'] : null;
        $this->description = (! empty($data['description'])) ? $data['description'] : null;
        $this->parent_id = (! empty($data['parent_id'])) ? $data['parent_id'] : null;
        $this->path = (! empty($data['path'])) ? $data['path'] : null;
        $this->path_depth = (! empty($data['path_depth'])) ? $data['path_depth'] : null;
        $this->created_on = (! empty($data['created_on'])) ? $data['created_on'] : null;
        $this->created_by = (! empty($data['created_by'])) ? $data['created_by'] : null;
    }
}

