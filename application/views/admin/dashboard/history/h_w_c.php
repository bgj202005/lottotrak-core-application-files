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
	/* pos */
	table.pos{
 		border:1px solid black;
  		display:inline-block;
		max-width: 160px;
		margin:20px;
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
	.last-draws {
		margin-top:3em;
		text-align: center;
	}
	.h1, .h2, .h3, .h4, .h5, .h6, h1, h2, h3, h4, h5, h6 {
    color: #000000;
	}	
.shadow-sm {
    box-shadow: 0 .125rem .25rem rgba(0,0,0,.075)!important;
	}

</style>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
	<script src="//code.jquery.com/jquery-1.12.4.js"></script>
  	<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<h2><?php echo 'H (Hots), W (Warms and C (Colds) winners for: '.$lottery->lottery_name; ?></h2>
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
									<div class="col-md-6">
										<div class="bg-white card last-draws mb-4 shadow-sm">
											<div class="p-4">
												<h4 class="mb-1">Draw Range: <?=$lottery->last_drawn['range'];?> Draws</h4>
											</div>
										</div>
										<div class="bg-white card last-draws mb-4 shadow-sm">
											<div class="p-4">
												<h4 class="mb-1">
												<?php $extra = array('for' => 'extra_lb', 'style' =>'margin-right:10px;');
												echo form_label('Extra (Bonus) Ball Included?', 'extra_lb', $extra);
												echo (!empty($lottery->extra_included)) ? form_label(' YES', 'extra_lb', $extra) : form_label(' NO', 'extra_lb', $extra);
												?></h4>
											</div>
										</div>
										<div class="bg-white card last-draws mb-4 shadow-sm">
											<div class="p-4">
												<h4 class="mb-1">
												<?php $format = array('for' => 'extra_lb', 'style' =>'margin-right:10px;');
												echo form_label('Last Draw Date:', 'extra_lb', $format);
												echo "<br />";
												echo date("l, M-d-Y",strtotime(str_replace('/','-',$lottery->last_drawn['draw_date']))); 
												echo "<br /><br />";
												$mx = 1;
												$extra_ball = 0;	// set extra ball to something, even if not used.
												$xtr = FALSE;
												foreach($lottery->draw as $count => $drawn):
													echo $drawn." ";
													if($xtr) $extra_ball = $drawn; // capture the extra / bonus ball
													$mx++;
													if(($mx>$lottery->balls_drawn)&&($lottery->extra_ball)&&(!$xtr)):
														echo " + ";
														$xtr = TRUE;
													endif;
												endforeach;
												?></h4>
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="bg-white card last-draws mb-4 shadow-sm">
											<div class="p-4">
												<h4 class="mb-1">
												<div class = "text-center" style = "display:inline-block;">
												<?php 	$hot = array('style' =>'margin-right:10px; color:red;');
														$warm = array('style' =>'margin-right:10px; color:orange;');
														$cold = array('style' =>'color:blue;');
														echo form_label("Hots:", "id => 'lb_hots'", $extra);
														echo form_label($lottery->H, "id => 'hots'", $hot);
														echo form_label("Warms:", "id => 'lb_warms'", $extra);
														echo form_label($lottery->W, "id => 'warms'", $warm);
														echo form_label("Colds:", "id => 'lb_colds'", $extra);
														echo form_label($lottery->C, "id => 'colds'", $cold);
													?>
												</div></h4>
											</div>
										</div>
										<div class="bg-white card last-draws mb-4 shadow-sm">
											<div class="p-4">
												<h4 class="mb-1">
												<?php $extra = array('for' => 'extra_lb', 'style' =>'margin-right:10px;');
												echo form_label('Extra Draws Included?', 'extra_lb', $extra);
												echo (!empty($lottery->extra_draws)) ? form_label(' YES', 'extra_lb', $extra) : form_label(' NO', 'extra_lb', $extra);
												?></h4>
											</div>
										</div>
										<div class="bg-white card last-draws mb-4 shadow-sm">
											<div class="p-4">
												<h4 class="mb-1">
												<?php $extra = array('for' => 'extra_lb', 'style' =>'margin-right:10px;');
												echo form_label('Last Drawn HWC:', 'extra_lb', $extra);
												$hot = array('style' =>'margin-right:10px; color:red;');
														$warm = array('style' =>'margin-right:10px; color:orange;');
														$cold = array('style' =>'color:blue;');
														echo form_label("H:", "id => 'lb_hots'", $extra);
														echo form_label($lottery->hwc[0], "id => 'hots'", $hot);
														echo form_label("W:", "id => 'lb_warms'", $extra);
														echo form_label($lottery->hwc[1], "id => 'warms'", $warm);
														echo form_label("C:", "id => 'lb_colds'", $extra);
														echo form_label($lottery->hwc[2], "id => 'colds'", $cold);
												?></h4>
											</div>
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
													$sym = substr($ball, -1);  // extract only the asterisk, '*'symbol
													$ball = rtrim($ball,'*');  // Remove the special '*' symbol
													if($sym=='*'&&$ball!=$extra_ball) :
														echo "<td class='text-center bg-danger text-white'>".$ball."</td>";
														echo "<td class='text-center bg-danger text-white'>".$count."</td>";
													elseif($sym=='*'&&$ball==$extra_ball&&!$lottery->duplicate_extra_ball):
														echo "<td class='text-center bg-info text-white'>".$ball."</td>";
														echo "<td class='text-center bg-info text-white'>".$count."</td>";
													else:
														echo "<td class='text-center'>".$ball."</td>";
														echo "<td class='text-center'>".$count."</td>";
													endif;
													echo "</tr>";
												else:
													echo "<tr class='table-danger'><td colspan = '2'>No Hots</td></tr>";
												endif;
											endforeach; ?>
										</tbody>
									</table>
									<table class="table pos">
										<thead>
											<tr>
												<th class="text-center" colspan="2">Hot Positions</th>
											</tr>
											<tr>
												<th class="text-center">Position</th>
												<th class="text-center">Count</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach($lottery->hots_pos as $position => $count):	
													$exists = FALSE;
													echo "<tr class='table-light'>";
													$exists = array_key_exists($position, $lottery->positions);
													$position = rtrim($position,'h');  // Remove the special '*' symbol
													if($exists) :
														echo "<td class='text-center bg-danger text-white'>".$position."</td>";
													 	echo "<td class='text-center bg-danger text-white'>".$count."</td>";
													else:
														echo "<td class='text-center'>".$position."</td>";
														echo "<td class='text-center'>".$count."</td>";
													endif;
													echo "</tr>";
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
													$sym = substr($ball, -1);  // extract only the asterisk, '*'symbol
													$ball = rtrim($ball,'*');  // Remove the special '*' symbol
													if($sym=='*'&&$ball!=$extra_ball) :
														echo "<td class='text-center bg-danger text-white'>".$ball."</td>";
														echo "<td class='text-center bg-danger text-white'>".$count."</td>";
													elseif($sym=='*'&&$ball==$extra_ball&&!$lottery->duplicate_extra_ball):
														echo "<td class='text-center bg-info text-white'>".$ball."</td>";
														echo "<td class='text-center bg-info text-white'>".$count."</td>";
													else:
														echo "<td class='text-center'>".$ball."</td>";
														echo "<td class='text-center'>".$count."</td>";
													endif;
													echo "</tr>";
												else:
													echo "<tr class='table-warning'><td colspan = '2'>No Warms</td></tr>";
												endif;
											endforeach; ?>
										</tbody>
									</table>
									<table class="table pos">
										<thead>
											<tr>
												<th class="text-center" colspan="2">Warm Positions</th>
											</tr>
											<tr>
												<th class="text-center">Position</th>
												<th class="text-center">Count</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach($lottery->warms_pos as $position => $count):	
													$exists = FALSE;
													echo "<tr class='table-light'>";
													$exists = array_key_exists($position, $lottery->positions);
													$position = rtrim($position,'w');  // Remove the special '*' symbol
													if($exists) :
														echo "<td class='text-center bg-danger text-white'>".$position."</td>";
													 	echo "<td class='text-center bg-danger text-white'>".$count."</td>";
													else:
														echo "<td class='text-center'>".$position."</td>";
														echo "<td class='text-center'>".$count."</td>";
													endif;
													echo "</tr>";
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
													$sym = substr($ball, -1);  // extract only the asterisk, '*'symbol
													$ball = rtrim($ball,'*');  // Remove the special '*' symbol
													if($sym=='*'&&$ball!=$extra_ball) :
														$ball = rtrim($ball,'*'); // Remove the special '*' symbol
														echo "<td class='text-center bg-danger text-white'>".$ball."</td>";
														echo "<td class='text-center bg-danger text-white'>".$count."</td>";
													elseif($sym=='*'&&$ball==$extra_ball&&!$lottery->duplicate_extra_ball):
														echo "<td class='text-center bg-info text-white'>".$ball."</td>";
														echo "<td class='text-center bg-info text-white'>".$count."</td>";
													else:
														echo "<td class='text-center'>".$ball."</td>";
														echo "<td class='text-center'>".$count."</td>";
													endif;
													echo "</tr>";
												else:
													echo "<tr class='table-primary'><td colspan = '2'>No Colds</td></tr>";
												endif;
											endforeach; ?>
										</tbody>
									</table>
									<table class="table pos">
										<thead>
											<tr>
												<th class="text-center" colspan="2">Cold Positions</th>
											</tr>
											<tr>
												<th class="text-center">Position</th>
												<th class="text-center">Count</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach($lottery->colds_pos as $position => $count):	
													$exists = FALSE;
													echo "<tr class='table-light'>";
													$exists = array_key_exists($position, $lottery->positions);
													$position = rtrim($position,'c');  // Remove the special '*' symbol
													if($exists) :
														echo "<td class='text-center bg-danger text-white'>".$position."</td>";
													 	echo "<td class='text-center bg-danger text-white'>".$count."</td>";
													else:
														echo "<td class='text-center'>".$position."</td>";
														echo "<td class='text-center'>".$count."</td>";
													endif;
													echo "</tr>";
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
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>	