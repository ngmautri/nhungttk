			explain
              SELECT 
				mla_sparepart_cats.sparepart_cat_id AS sparepart_cat_id,
                mla_sparepart_cats.name AS cat_name,
				mla_spareparts.*,
                IFNULL(mla_sparepart_movements.total_inflow,0) AS total_inflow,
				IFNULL(mla_sparepart_movements.total_outflow,0) AS total_outflow,
				(IFNULL(mla_sparepart_movements.total_inflow,0) - IFNULL(mla_sparepart_movements.total_outflow,0)) AS current_balance,
				IFNULL(mla_sparepart_minimum_balance.minimum_balance,0) AS minimum_balance,
				((IFNULL(mla_sparepart_movements.total_inflow,0) - IFNULL(mla_sparepart_movements.total_outflow,0)) - IFNULL(mla_sparepart_minimum_balance.minimum_balance,0)) AS remaining_to_order,
                mla_sparepart_pics.id AS sp_pic_id,
                mla_sparepart_pics.filename,
                mla_sparepart_pics.url,
                mla_sparepart_pics.folder,
                mla_purchase_cart.*
			FROM mla_spareparts
			LEFT JOIN
            (
				SELECT  
					mla_sparepart_movements.sparepart_id,
                    SUM(CASE WHEN mla_sparepart_movements.flow='IN' THEN  mla_sparepart_movements.quantity ELSE 0 END) AS total_inflow,
					SUM(CASE WHEN mla_sparepart_movements.flow='OUT' THEN mla_sparepart_movements.quantity ELSE 0 END) AS total_outflow
				FROM mla_sparepart_movements 
				GROUP BY mla_sparepart_movements.sparepart_id
			) 
            AS mla_sparepart_movements
            ON mla_sparepart_movements.sparepart_id = mla_spareparts.id
            
			/*minimum_balance*/
			LEFT JOIN mla_sparepart_minimum_balance
			ON mla_sparepart_minimum_balance.sparepart_id =  mla_spareparts.id
            
			/*picture*/
            LEFT JOIN 
            (
	            SELECT 	
				* 
				FROM mla_sparepart_pics
				ORDER BY mla_sparepart_pics.uploaded_on DESC
				LIMIT 1
            )
            AS mla_sparepart_pics
            ON mla_sparepart_pics.sparepart_id = mla_spareparts.id
           
			/* sp_category; take one only*/
           LEFT JOIN 
		   (
			   SELECT
				mla_sparepart_cats.name,
				mla_sparepart_cats_members.sparepart_id,
				mla_sparepart_cats_members.sparepart_cat_id
				FROM mla_sparepart_cats
              
				LEFT JOIN mla_sparepart_cats_members
				ON mla_sparepart_cats_members.sparepart_cat_id = mla_sparepart_cats.id
                
                GROUP BY mla_sparepart_cats_members.sparepart_id
			)
            AS mla_sparepart_cats
			ON mla_sparepart_cats.sparepart_id = mla_spareparts.id
            
            /* check order cart*/
          
            left join
            (
				select
					mla_purchase_cart.id as cart_item_id,
					ifnull(mla_purchase_cart.sparepart_id,0) as ordering_sp_id,
					mla_purchase_cart.article_id as ordering_artice_id,
					mla_purchase_cart.asset_id as ordering_asset_id
				from mla_purchase_cart
				where mla_purchase_cart.status is null
            )
            as mla_purchase_cart
            on mla_purchase_cart.ordering_sp_id = mla_spareparts.id
           
            WHERE 1
       