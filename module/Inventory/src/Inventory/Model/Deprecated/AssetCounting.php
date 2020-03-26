<?php
namespace Inventory\Model;

/**
 *
 * @author nmt
 *        
 */
class AssetCounting
{

    public $id;

    public $name;

    public $description;

    public $start_date;

    public $end_date;

    public $asset_cat_id;

    public $status;

    public $created_by;

    public $created_on;

    public function exchangeArray($data)
    {
        $this->id = (! empty($data['id'])) ? $data['id'] : null;
        $this->name = (! empty($data['name'])) ? $data['name'] : null;
        $this->description = (! empty($data['description'])) ? $data['description'] : null;

        $this->start_date = (! empty($data['start_date'])) ? $data['start_date'] : null;
        $this->end_date = (! empty($data['end_date'])) ? $data['end_date'] : null;

        $this->asset_cat_id = (! empty($data['asset_cat_id'])) ? $data['asset_cat_id'] : null;
        $this->status = (! empty($data['status'])) ? $data['status'] : null;
        $this->created_by = (! empty($data['created_by'])) ? $data['created_by'] : null;
        $this->created_on = (! empty($data['created_on'])) ? $data['created_on'] : null;
    }
}

