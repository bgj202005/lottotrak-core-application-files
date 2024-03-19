<link href="https://unpkg.com/bootstrap-table@1.18.0/dist/bootstrap-table.min.css" rel="stylesheet">
<link href="https://unpkg.com/bootstrap-table@1.18.0/dist/extensions/reorder-rows/bootstrap-table-reorder-rows.css" rel="stylesheet">
<style>
	.card {
    background-color: #ffffff;
    border: 1px solid rgba(0, 34, 51, 0.1);
    box-shadow: 2px 4px 10px 0 rgba(0, 34, 51, 0.05), 2px 4px 10px 0 rgba(0, 34, 51, 0.05);
    border-radius: 0.15rem;
	}
	/* Tabs Card */
	.tab-card {
	border:1px solid #eee;
	}
	.tab-card-header {
	background:none;
	}
	/* Default mode */
	.tab-card-header > .nav-tabs {
	border: none;
	margin: 0px;
	}
	.tab-card-header > .nav-tabs > li {
	margin-right: 2px;
	}
	.tab-card-header > .nav-tabs > li > a {
	border: 0;
	border-bottom:2px solid transparent;
	margin-right: 0;
	color: #737373;
	padding: 2px 15px;
	}
	.tab-card-header > .nav-tabs > li > a.show {
		border-bottom:2px solid #007bff;
		color: #007bff;
	}
	.tab-card-header > .nav-tabs > li > a:hover {
		color: #007bff;
	}
	.tab-card-header > .tab-content {
	padding-bottom: 0;
	}
	.card-title {
		color:#000000;
	}
	.card-text {
		color:steelblue; 
	}
	table{
  		border:1px solid black;
  		display:inline-block;
  		max-width: 178px;
  		margin:20px;
	}
	/* hwc */
	table hwc{
    	width:100%;
	}
	tr hwc{
		font-size: 0.90em;
	}
	th hwc{
		text-align:center;
		font-size: 0.90em;
	}
	table.hwc tr{
		font-size: 0.65em;
	}
	
	th.datafont{
		text-align:center;
		font-size: 0.95em;
	}
	td.datafont { 
		font-size: 	0.95em;
		text-align: center; 
		white-space: nowrap;
	}
	#hwc_paginate{
    float:right;
	}	  
</style>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
	<script src="//code.jquery.com/jquery-1.12.4.js"></script>
  	<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<h2><?php echo 'View the Hot, Warm and Cold numbers for: '.$lottery->lottery_name; ?></h2>
	<?php $max = $lottery->balls_drawn; 
	   $b = 1; 
	   ?>	
	<h5 style = "text-align:left"><?php echo anchor('admin/history', 'Back to the Win History Dashboard', 'title="Back to History"'); ?></h5>
	<section>
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="card mt-3 tab-card">
					<div id = "error"></div>
						<div class="card-header tab-card-header">
							<div class="d-flex flex-row-reverse">
								<div style = "margin-left: 50px;" class=".col-md-4">
								<h4 class="text-dark header-title">Draw Range: <?=$lottery->last_drawn['range'];?></h4>
								</div>
									<div class=".col-md-4">	
										<?php
											$extra = array('for' => 'extra_lb');
											echo form_label('Extra (Bonus) Ball Included?', 'extra_lb', $extra);
											echo (!empty($lottery->extra_included)) ? form_label(' YES', 'extra_lb', $extra) : form_label(' NO', 'extra_lb', $extra);
										?>
										</div>
									</div>
									<div class=".col-md-4">
										<?php
											$extra = array('for' => 'extra_draw_lb');
											echo form_label('Extra Draw(s) Included?', 'extra_draw_lb', $extra);
											echo (!empty($lottery->extra_draws)) ? form_label('YES', 'extra_lb', $extra) : form_label('NO', 'extra_lb', $extra); 
										?>
									</div>
								<div class = "text-center" style = "display:inline-block;">
									<?php 
										echo form_label("Hots:", "id => 'lb_hots'");
										echo form_label($lottery->H, "id => 'lb_hots'");
										echo form_label("Warms:", "id => 'lb_warms'");
										echo form_label($lottery->W, "id => 'lb_warms'");
										echo form_label("Colds:", "id => 'lb_colds'");
										echo form_label($lottery->C, "id => 'lb_colds'");
									?>
								</div>
							</div>
						</div>		
						
						<div class="container" id="content" style = "margin:20px;">
							<div class = "row justify-content-center">
								<table class="table">
									<thead>
										<tr>
											<th class="text-center" colspan="2">Hots</th>
										</tr>
										<tr>
											<th class="text-center">Ball</th>
											<th class="text-center">Occurrences</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach($lottery->hots as $ball => $count):	
											if($ball) :
												echo "<tr class='table-danger'>";
												echo "<td class='text-center'>".$ball."</td>";
												echo "<td class='text-center'>".$count."</td>";
												echo "</tr>";
											else:
												echo "<tr class='table-danger'><td colspan = '2'>No Hots</td></tr>";
											endif;
										endforeach; ?>
									</tbody>
								</table>
								<table class="table">
									<thead>
										<tr>
											<th class="text-center" colspan="2">Warms</th>
										</tr>
										<tr>
											<th class="text-text-center">Ball</th>
											<th class="text-text-center">Occurrences</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach($lottery->warms as $ball => $count):	
											if($ball) :
												echo "<tr class='table-warning'>";
												echo "<td class='text-center'>".$ball."</td>";
												echo "<td class='text-center'>".$count."</td>";
												echo "</tr>";
											else:
												echo "<tr class='table-warning'><td colspan = '2'>No Warms</td></tr>";
											endif;
										endforeach; ?>
									</tbody>
								</table>
								<table class="table">
									<thead>
										<tr>
											<th class="text-center" colspan="2">Colds</th>
										</tr>
										<tr>
											<th class="text-center">Ball</th>
											<th class="text-center">Occurrences</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach($lottery->colds as $ball => $count):	
											if($ball) :
												echo "<tr class='table-primary'>";
												echo "<td class='text-center'>".$ball."</td>";
												echo "<td class='text-center'>".$count."</td>";
												echo "</tr>";
											else:
												echo "<tr class='table-primary'><td colspan = '2'>No Colds</td></tr>";
											endif;
										endforeach; ?>
									</tbody>
								</table>
								<!-- Extra Ball if it outside of the balls being drawn and can have a duplicate drawn ball -->
								<?php if(isset($lottery->dupextra)) : ?>
								<table class="table">
									<thead>
										<tr>
											<th class="text-center" colspan="2">Extra Ball</th>
										</tr>
										<tr>
											<th class="text-center">Ball</th>
											<th class="text-center">Occurrences</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach($lottery->dupextra as $ball => $count):	
												echo "<tr class='table-success'>";
												echo "<td class='text-center'>".$ball."</td>";
												echo "<td class='text-center'>".$count."</td>";
												echo "</tr>";
										endforeach; ?>
									</tbody>
								</table>
									<?php endif; ?>

								<table class="table" style = "max-width: 229px;">
									<thead>
										<tr>
											<th class="text-center" colspan="2">Overdue</th>
										</tr>
										<tr>
											<th class="text-center">Ball</th>
											<th class="text-center">Draws Skipped</th>
											<th class="text-center">Draw Average</th>
										</tr>
									</thead>
									<tbody>
									<?php foreach($lottery->overdue as $ball => $count):	
												$skips = explode('|', $count); // break the actual overdue draws and the average that the ball is drawn
												if($ball) :
													echo "<tr class='table-info'>";
													echo "<td class='text-center'>".$ball."</td>";
													echo "<td class='text-center'>".$skips[0]."</td>";
													echo "<td class='text-center'>".$skips[1]."</td>";
													echo "</tr>";
												else:
													echo "<tr class='table-info'><td colspan = '3'>No Overdue Balls</td></tr>";
												endif;			
											endforeach; ?>
									</tbody>
								</table>
								<table style = "max-width: 85px; max-height: 100px;" class="table table-sm">
								<thead>
									<tr>
										<th colspan = "2" class = "datafont">Last Draw</th>
									</tr>
									<tr>
										<th class = "datafont">H - W - C</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td class="text-center datafont"><?=str_replace('-',' - ',$lottery->last_hwc);?></td>
									</tr>
								</tbody>	
								</table>
								<table style = "max-width: 130px;" class="table table-striped table-sm">
									<thead>
										<tr>
											<th colspan = "2" class = "datafont">Last 10 Draws</th>
										</tr>
										<tr>
											<th class = "datafont">H - W - C</th>
											<th class = "datafont">Count</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($lottery->last10 as $last10 => $total): ?>
										<tr>
											<td class="text-center datafont"><?=str_replace('-',' - ',$last10);?></td>
											<td class="text-center datafont"><?=$total;?></td>
										</tr>
										<?php endforeach; ?>
									</tbody>	
								</table>
								<table style = "max-width: 130px;" class="table table-striped table-sm">
									<thead>
									<tr>
											<th colspan = "2" class = "datafont">Last <?=$lottery->last_drawn['range'];?> Draws</th>
									</tr>	
									<tr>
											<th class = "datafont">H - W - C</th>
											<th class = "datafont">Count</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($lottery->hwc as $h_w_c => $total): ?>
										<tr>
											<td class="text-center datafont"><?=str_replace('-',' - ',$h_w_c);?></td>
											<td class="text-center datafont"><?=$total;?></td>
										</tr>
										<?php endforeach; ?>
									</tbody>	
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>	