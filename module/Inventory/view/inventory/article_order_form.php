<!-- Modal -->
<div id="sp_order_modal" class="modal hide" role="dialog" s>
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-header" style="height: 25px">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h5 class="modal-title">Mascot International (Laos)</h5>
		</div>
		<div class="modal-body">
			<div id="_status"></div>

			<form class="form-horizontal"
				action="" method="post" enctype="multipart/form-data" id="">
				<input type="hidden" name="item_id" id="item_id" value="" />
				
				<div class="control-group">
					<label class="control-label" for="inputNameLocal">Priority:</label>
					<div class="controls">
						<select name="priority" id="item_priority">
							<option value="Low">Low</option>
							<option value="Medium" selected>Medium</option>
							<option value="High">High</option>
							<option value="Urgent">Urgent</option>
						</select>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="inputTag">Name:</label>
					<div class="controls">
						<input type="text" id="item_name" placeholder="" name="name" value="">
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="inputTag">Code:</label>
					<div class="controls">
						<input type="text" id="item_code" placeholder="" name="code"
							value="">
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="inputTag">Unit:</label>
					<div class="controls">
						<input type="text" id="item_unit" placeholder="" name="unit"
							value="">
					</div>
				</div>


				<div class="control-group">
					<label class="control-label" for="inputTag">Quantity:</label>
					<div class="controls">
						<input type="text" id="item_quantity" placeholder=""
							name="quantity" value="">
						<div style="color: gray;font-size 6pt" id="item_balance"></div>
					</div>

				</div>

				<div class="control-group">
					<label class="control-label" for="inputNameLocal">Expected
						Delivery Date:</label>
					<div class="controls">
						<input type="text" id="end_date" placeholder="click to select"
							name="EDT" value="">
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label" for="inputTag">Asset Name (if any):</label>
					<div class="controls">
						<input type="text" id="item_asset_name" placeholder="" name="asset_name"
							value="">
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label" for="inputDescription">Remarks:</label>
					<div class="controls">
						<textarea id="item_remarks" placeholder="" rows="2" cols="5"
							name="remarks"></textarea>
					</div>
				</div>
			</form>
		</div>

		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
			<button onclick="addItemToCart('ARTICLE');" type="button"
				class="btn btn-primary">Add To Cart</button>
		</div>
	</div>
</div>

<!-- Modal -->
<div id="info_modal" class="modal hide" role="dialog">
	<div class="modal-dialog">
		<div class="modal-body">
			<p>Working on it........Please Wait!</p>
		</div>
	</div>
</div>
