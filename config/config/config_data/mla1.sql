select l11.*, concat(lt1.purchase_request_id,'+++',max(lt1.updated_on)) as id_updated_on from mla_purchase_requests_workflows as lt1
