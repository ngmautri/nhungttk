 <div class="btn-group">
    <button type="button" class="btn btn-defaultdropdown-toggle" data-toggle="dropdown">
    Spare-Part Management <span class="caret"></span></button>
    <ul class="dropdown-menu" role="menu">
 				<li><a href="/inventory/spareparts/category">Spare-part category</a>
						</li>
						<li><a href="/inventory/spareparts/list">Show all spare-parts</a>
						</li>
						<li class="divider"></li>
 						<li><a href="/inventory/report/index">Reporting</a>
						</li>
					<li class="divider"></li>
 						<li><a href="/inventory/spareparts/suggest">Order Suggestion</a>
						</li>
    </ul>
  </div>
 <hr>

<form class="form-search" action="<?php echo $this->baseUrl ?>/inventory/search/sparepart"	method="get" enctype="multipart/form-data">
      <input type="text" name = "query" class="">
      <button type="submit" class="btn"><i class="icon-search">  </i>Search</button>
</form>
