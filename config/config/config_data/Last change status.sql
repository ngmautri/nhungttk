/* PR - Last Status begin */
SELECT 
    *
FROM
    (SELECT 
        lt1.purchase_request_id,
            lt1.status AS last_status,
            lt2.last_change
    FROM
        (SELECT 
        lt3.*,
            CONCAT(lt3.purchase_request_id, '+++', lt3.updated_on) AS id_updated_on
    FROM
        mla_purchase_requests_workflows AS lt3) AS lt1
    JOIN (SELECT 
        CONCAT(tt1.purchase_request_id, '+++', MAX(tt1.updated_on)) AS id_lastchanged,
            tt1.purchase_request_id,
            MAX(tt1.updated_on) AS last_change
    FROM
        mla_purchase_requests_workflows AS tt1
    GROUP BY tt1.purchase_request_id) AS lt2 ON lt1.id_updated_on = lt2.id_lastchanged) AS lt4
WHERE
    1
/* PR - Last Status end */