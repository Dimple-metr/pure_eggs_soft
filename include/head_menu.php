<?
	$arr=explode("/",$_SERVER['PHP_SELF']);
	$page_name=end($arr);
	$page_name=basename($page_name, '.php');
?>
<style>
ul.summary-list > li {
	width:15%;
}
</style>
<div style="text-align:right" class="hidden-phone">
						<ul class="summary-list" >
							<? if($page_name!="invoice")
							{?>
							<li class="">
								<a href="<?=ROOT.'invoice'?>">
									<i class="fa fa-pencil text-primary"></i>
										Create Invoice
								</a>
                            </li>
                            <? }
							if($page_name!="invoice_list")
							{?>
							<li>
								<a href="<?=ROOT.'invoice_list'?>">
									<i class="fa fa-envelope text-info"></i>
										Invoice List
                                </a>
                            </li>
							<? }
							if($page_name!="invoicepaymentreceipt_list")
							{?>
                            <li>
								<a href="<?=ROOT.'invoicepaymentreceipt_list'?>">
									<i class="fa fa-money text-info"></i>
                                      Payment List
                                </a>
                            </li>
                            <?}
							if($page_name!="invoicepayment")
							{?>
							<li>
                               <a href="<?=ROOT.'invoicepayment'?>">
                                 <i class="fa fa-pencil text-info"></i>
										Add Payment
                               </a>
                            </li>
							<? }?>
                     </ul>
		</div>
						