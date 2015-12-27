<?php
namespace Inventory\Model;

/**
 *
 * @author nmt
 *
 */
class AssetType
{
    public $id;
    public $type;
    public $description;
    public $created_on;
    
 
    public function exchangeArray($data){        
        $this->id     = (!empty($data['id'])) ? $data['id'] : null;
        $this->type = (!empty($data['type'])) ? $data['type'] : null;
        $this->description  = (!empty($data['description'])) ? $data['description'] : null;
        $this->created_on  = (!empty($data['created_on'])) ? $data['created_on'] : null;
        
      }


}

