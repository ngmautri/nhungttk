<?php
namespace Inventory\Model;

/**
 *
 * @author nmt
 *        
 */
class Warehouse
{

    public $id;

    public $wh_code;

    public $wh_name;

    public $wh_address;

    public $wh_country;

    public $wh_contact_person;

    public $wh_telephone;

    public $wh_email;

    public $wh_status;

    public $created_on;

    public $created_by;

    public function exchangeArray($data)
    {
        $this->id = (! empty($data['id'])) ? $data['id'] : null;
        $this->wh_name = (! empty($data['wh_name'])) ? $data['wh_name'] : null;
        $this->wh_code = (! empty($data['wh_code'])) ? $data['wh_code'] : null;

        $this->wh_address = (! empty($data['wh_address'])) ? $data['wh_address'] : null;
        $this->wh_country = (! empty($data['wh_country'])) ? $data['wh_country'] : null;
        $this->wh_contact_person = (! empty($data['wh_contact_person'])) ? $data['wh_contact_person'] : null;
        $this->wh_telephone = (! empty($data['wh_telephone'])) ? $data['wh_telephone'] : null;
        $this->wh_email = (! empty($data['wh_email'])) ? $data['wh_email'] : null;
        $this->wh_status = (! empty($data['wh_status'])) ? $data['wh_status'] : null;

        $this->created_on = (! empty($data['created_on'])) ? $data['created_on'] : null;
        $this->created_by = (! empty($data['created_by'])) ? $data['created_by'] : null;
    }
}

