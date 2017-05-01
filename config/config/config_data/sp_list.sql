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
                mla_sparepart_pics.sp_picture_id,
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
					mla_sparepart_pics.sparepart_id,
					MAX(mla_sparepart_pics.id) AS sp_picture_id
				FROM mla_sparepart_pics
				GROUP BY mla_sparepart_pics.sparepart_id
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
          
            LEFT JOIN
            (
				SELECT
					mla_purchase_cart.id AS cart_item_id,
					IFNULL(mla_purchase_cart.sparepart_id,0) AS ordering_sp_id,
					mla_purchase_cart.article_id AS ordering_artice_id,
					mla_purchase_cart.asset_id AS ordering_asset_id
				FROM mla_purchase_cart
				WHERE mla_purchase_cart.status IS NULL
            )
            AS mla_purchase_cart
            ON mla_purchase_cart.ordering_sp_id = mla_spareparts.id
           
            WHERE 1
       