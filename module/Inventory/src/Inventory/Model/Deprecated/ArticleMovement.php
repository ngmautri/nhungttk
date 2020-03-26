<?php
namespace Inventory\Model;

/**
 *
 * @author nmt
 *        
 */
class ArticleMovement
{

    public $id;

    public $movement_date;

    public $article_id;

    public $flow;

    public $quantity;

    public $reason;

    public $requester;

    public $comment;

    public $created_on;

    public $created_by;

    public $dn_item_id;

    public $pr_item_id;

    public $wh_id;

    public $movement_type;

    public function exchangeArray($data)
    {
        $this->id = (! empty($data['id'])) ? $data['id'] : null;
        $this->movement_date = (! empty($data['sparepart_id'])) ? $data['movement_date'] : null;
        $this->article_id = (! empty($data['article_id'])) ? $data['article_id'] : null;

        $this->flow = (! empty($data['flow'])) ? $data['flow'] : null;
        $this->quantity = (! empty($data['quantity'])) ? $data['quantity'] : null;

        $this->reason = (! empty($data['reason'])) ? $data['reason'] : null;
        $this->requester = (! empty($data['requester'])) ? $data['requester'] : null;
        $this->comment = (! empty($data['comment'])) ? $data['comment'] : null;
        $this->created_on = (! empty($data['created_on'])) ? $data['created_on'] : null;
        $this->created_by = (! empty($data['created_by'])) ? $data['created_by'] : null;

        $this->dn_item_id = (! empty($data['dn_item_id'])) ? $data['dn_item_id'] : null;
        $this->pr_item_id = (! empty($data['pr_item_id'])) ? $data['pr_item_id'] : null;

        $this->wh_id = (! empty($data['wh_id'])) ? $data['wh_id'] : null;
        $this->movement_type = (! empty($data['movement_type'])) ? $data['movement_type'] : null;
    }
}

