<ul style="font-size: 10pt" class="dropdown-menu">
   <li>
      <a href="/procure/pr/add">
         <i class="fa fa-plus" aria-hidden="true"></i>&nbsp;<?php
        echo $this->translate('New Purchase Request (PR)');
        ?></a>
   </li>
   <li>
      <a href="/procure/pr/all/row_number=1/status=pending">
         <i class="fa fa-caret-right" aria-hidden="true"></i>
         &nbsp;&nbsp;&nbsp;PR list
      </a>
   </li>
   <li>
      <a href="/procure/pr-row/status-report">
         <i class="fa fa-caret-right" aria-hidden="true"></i>
         &nbsp;&nbsp;&nbsp;PR Row Status
      </a>
   </li>
   <li class="divider"></li>
   <li>
      <a href="/procure/qr/create">
         <i class="fa fa-plus" aria-hidden="true"></i>&nbsp;<?php

        echo $this->translate("New Quotation");
        ?>(QO)</a>
   </li>
   <li>
      <a href="/procure/qr/list">
         <i class="fa fa-caret-right" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;<?php

        echo $this->translate("Quotation List");
        ?></a>
   </li>
 
   <li class="divider"></li>
   <li>
      <a href="/procure/po/create">
         <i class="fa fa-plus" aria-hidden="true"></i>
         &nbsp;New Contract (PO)
      </a>
   </li>
   <li>
      <a href="/procure/po/list">
         <i class="fa fa-caret-right" aria-hidden="true"></i>
         &nbsp;&nbsp;&nbsp;PO /Contract list
      </a>
   </li>
   <li class="divider"></li>
   <li>
      <a href="/procure/gr/create">
         <i class="fa fa-plus" aria-hidden="true"></i>
         &nbsp;New Goods Receipt (GR)
         <i class="fa fa-truck" aria-hidden="true"></i>
      </a>
   </li>
   <li>
      <a href="/procure/gr/list">
         <i class="fa fa-caret-right" aria-hidden="true"></i>
         &nbsp;&nbsp;&nbsp;GR list
      </a>
   </li>
   <li class="divider"></li>
   <li>
      <a href="/procure/ap/create">
         <i class="fa fa-plus" aria-hidden="true"></i>
         &nbsp;&nbsp;New A/P Invoice (AP)
      </a>
   </li>
   <li>
      <a href="/procure/ap/list">
        <i class="fa fa-list" aria-hidden="true"></i>
         &nbsp;A/P Invoice List
      </a>
   </li>
   <li class="divider"></li>
   <li>
      <a href="/procure/return/add">
         <i class="fa fa-plus" aria-hidden="true"></i>&nbsp;<?php

        echo $this->translate("Goods Return");
        ?> (RE)</a>
   </li>
   <li>
      <a href="/procure/return/list">
         <i class="fa fa-caret-right" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;<?php

        echo $this->translate("Return list");
        ?></a>
   </li>
   <li class="divider"></li>
   <li>
      <a href="/procure/activity-log/list">
         <i class="fa fa-caret-right" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;<?php

        echo $this->translate("Log");
        ?></a>
   </li>
</ul>