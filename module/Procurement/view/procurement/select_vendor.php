
<div id="dialog" style="display: none; padding-top: 4pt">

	<form style="padding-top: 10pt" class="form-search" action=""
		method="get" enctype="multipart/form-data">
		<input id="search_term" type="text" name="query" class="">
		<button type="button" class="btn" onclick="searchVendor();">
			<i class="icon-search"> </i> Search
		</button>
	</form>

	<ul class="nav nav-pills">
		<li><a style="color: #0080ff;" href="/procurement/vendor/add"><i
				class="icon-plus"> </i> CREATE NEW VENDOR </a>
		
		<li><a style="color: #0080ff;" href="javascript:;"
			onclick="loadVendorList();"> <i class="icon-list"> </i> SHOW ALL
				VENDORS
		</a>
		</ul>
	<hr>
	<div id="search_result"></div>
</div>
