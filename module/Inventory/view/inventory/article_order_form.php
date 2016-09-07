<!-- Modal -->
<div id="myModal" class="modal hide" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h5 class="modal-title">Mascot International (Laos)</h5>
		</div>
		<div class="modal-body">
			<span id="_status"></span>

			<form class="form-horizontal"
				action="<?php echo $this->baseUrl ?>/procurement/pr/select-item-2"
				method="post" enctype="multipart/form-data" id="NewSP">
				<input type="hidden" name="redirectUrl"
					value="<?php echo $redirectUrl ?>" /> <input type="hidden"
					name="article_id" id="item_id" value="" />

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
						<input type="text" id="item_name" placeholder="" name="name"
							value="article">
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
					<label class="control-label" for="inputNameLocal">Requested
						Delivery Date:</label>
					<div class="controls">
						<input type="text" id="end_date" placeholder="click to select"
							name="EDT" value="">
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="inputDescription">Reason /Note:</label>
					<div class="controls">
						<textarea id="item_remarks" placeholder="" rows="2" cols="5"
							name="remarks"></textarea>
					</div>
				</div>
			</form>
		</div>

		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
			<button onclick="addItemToCart('SPARE-PART');" type="button"
				class="btn btn-primary">Add To Cart</button>
		</div>
	</div>
</div>

<!-- Modal -->
<div id="myModal1" class="modal hide" role="dialog">
	<div class="modal-dialog ">
		<div class="modal-body">
			<p>Working on it........Please Wait!</p>
		</div>
	</div>
</div>