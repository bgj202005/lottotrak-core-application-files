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
  		max-width: 168px;
  		margin:20px;
  		overflow: hidden;
	}
	/* Ensure background colors stay within table borders */
	table tbody tr td {
		background-clip: padding-box;
	}
	table tbody tr.table-danger td {
		background-color: #f8d7da !important;
	}
	table tbody tr.table-warning td {
		background-color: #fff3cd !important;
	}
	table tbody tr.table-primary td {
		background-color: #cfe2ff !important;
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
	<h5 style = "text-align:left"><?php echo anchor('admin/statistics', 'Back to Statistics Dashboard', 'title="Back to Statistics"'); ?></h5>
	<section>
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="card mt-3 tab-card">
					<div id = "error"></div>
					<?php if($this->session->flashdata('hwc_prediction_message')): ?>
						<div class="alert alert-info" style="margin: 15px;"><?=$this->session->flashdata('hwc_prediction_message');?></div>
					<?php endif; ?>
					<?php if($this->session->flashdata('heat_message')): ?>
						<div class="alert alert-info" style="margin: 15px;"><?=$this->session->flashdata('heat_message');?></div>
					<?php endif; ?>
					<?php if($this->session->flashdata('pool_message')): ?>
						<div class="alert alert-success" style="margin: 15px;"><?=$this->session->flashdata('pool_message');?></div>
					<?php endif; ?>
					<?php if($this->session->flashdata('error_message')): ?>
						<div class="alert alert-danger" style="margin: 15px;"><?=$this->session->flashdata('error_message');?></div>
					<?php endif; ?>
						<div class="card-header tab-card-header">
							<div class="d-flex flex-row-reverse">
								<div class="p-1">
								<div class="dropdown" style = "margin-left: 50px;">
										<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											Draw Range
										</button>
											<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
												<?php $interval = (integer) $lottery->last_drawn['interval'];
												if(!$interval) : 
													$sel_range = $lottery->last_drawn['range']; ?>
													<a class="dropdown-item active" href="<?=base_url('admin/statistics/h_w_c/'.$lottery->id)?>">All Draws (<?=$lottery->last_drawn['range'];?>) </a>
												<?php else:
													$sel_range = (integer) $lottery->last_drawn['sel_range']; // Selected a different range from the complete range of draws?
													for($i = 1; $i <= $interval; $i++):
														$step = $i * 100;	// in multiples of 100
														if($i!=$interval): ?>
															<a class="dropdown-item <?php if($i==$sel_range) echo 'active'; ?>" href="<?=base_url('admin/statistics/h_w_c/'.$lottery->id.'/'.$step);?>">Last <?=$step;?></a>
														<?php else : ?>
															<a class="dropdown-item <?php if($i==$sel_range) echo 'active'; ?>" href="<?=base_url('admin/statistics/h_w_c/'.$lottery->id.'/'.$lottery->last_drawn['all']);?>">All Draws (<?=$lottery->last_drawn['all'];?>)</a>
														<?php endif;
													endfor; ?> 
													<?php endif;?>
											</div>
										</div>
									</div>
									<div class="p-2">	
										<div class="form-check">
										<?php 
											$js = "location.href='".base_url()."admin/statistics/h_w_c/".$lottery->id."/".(!$interval ? $sel_range : ($sel_range*100))."/extra'";
											$attr = array(
												'onClick' 	=> "$js", 
												'class'		=> "form-check-input",
												'id'		=>	"extra_included"
											);
											$extra = array('for' => 'extra_lb');
											echo form_checkbox('extra_included', set_value('extra_included', '1'), set_checkbox('extra_included', '1', (!empty($lottery->extra_included))), $attr);
											echo form_label('Extra (Bonus) Ball Included?', 'extra_lb', $extra);
										?>
										</div>
									</div>
									<div class="p-3">
										<div class="form-check" style = "margin-top:-8px;">
										<?php
											$js = "location.href='".base_url()."admin/statistics/h_w_c/".$lottery->id."/".(!$interval ? $sel_range : ($sel_range*100))."/draws'";
											$attr = array(
												'onClick' 	=> "$js", 
												'class'		=> "form-check-input",
												'id'		=>	"extra_draws"
											);
										$extra = array('for' => 'extra_draw_lb');
											echo form_checkbox('extra_draws', '1', set_checkbox('extra_draws', '1', (!empty($lottery->extra_draws))), $attr);
											echo form_label('Extra Draw(s) Included?', 'extra_draw_lb', $extra); 
										?>
										</div>
									</div>
								</div>		
							</div>
						<div style="padding-top: 20px;">
							<!-- First Row: Hots, Warms, Colds with Change Heat Levels button on same line -->
							<div style="margin-bottom: 15px; white-space: nowrap; text-align: center;">
								<div style="display: inline-block; width: 400px; text-align: center;">
									<?php echo form_label("Hots:", "id => 'lb_hots'");
									$h_details = array( 'name'          => 'hots_spinner',
														'id'            => 'hots_spinner',
														'value'         => $lottery->H,
														'min'		    => '1',
														'max' 	        => '50',
														'step'			=> '1',
														'style'         => 'margin:5px 8px -5px 5px; width:3em; height:30px;'
									);
									echo form_input($h_details);
									echo form_label("Warms:", "id => 'lb_warms'");
									$w_details = array( 'name'          => 'warms_spinner',
														'id'            => 'warms_spinner',
														'value'         => $lottery->W,
														'min'		    => '1',
														'max' 	        => '50',
														'step'			=> '1',
														'style'         => 'margin:5px 8px -5px 5px; width:3em; height:30px;'
									);
									echo form_input($w_details);
									echo form_label("Colds:", "id => 'lb_colds'");
									$c_details = array( 'name'          => 'colds_spinner',
														'id'            => 'colds_spinner',
														'value'         => $lottery->C,
														'min'		    => '1',
														'max' 	        => '50',
														'step'			=> '1',
														'style'         => 'margin:5px 8px -5px 5px; width:3em; height:30px;'
									);
									echo form_input($c_details); ?>
								</div>
								<div style="display: inline-block; margin-left: 100px; vertical-align: top;">
									<?php $frm_attr = array('id' => 'frmheat_submit', 'style' => 'display:inline-block;');
									echo form_open(base_url('admin/statistics/h_w_c/'.$lottery->id), $frm_attr);
									?><input type="hidden" name="hots" id="hots_hidden" value="">
									<input type="hidden" name="warms" id="warms_hidden" value="">
									<input type="hidden" name="colds" id="colds_hidden" value="">
									<input type="hidden" name="heat" id="heat_hidden" value="">
									<?php
									echo form_hidden('original_hots', $lottery->H);
									echo form_hidden('original_warms', $lottery->W);
									echo form_hidden('original_colds', $lottery->C);
									$attr = array('class'	=> 'btn btn-primary', 'style' => 'vertical-align: top;');
									echo form_submit("heat", "Change Heat Levels", $attr);
									echo form_close(); ?>
								</div>
							</div>
							
							<!-- Second Row: Prediction Number Pool with Change Number Pool button on same line -->
							<div style="white-space: nowrap; text-align: center;">
								<div style="display: inline-block; width: 400px; text-align: center;">
									<?php echo form_label("Prediction Number Pool:", "id => 'lb_numberpool'");
									$min_pool = $lottery->balls_drawn; // Minimum is the pick number (e.g., 6 for pick 6)
									$max_pool = intval($lottery->maximum_ball / 2); // Maximum is half of total numbers
									$current_pool = isset($lottery->prediction_pool) ? $lottery->prediction_pool : 18; // Default to 18 if not set
									$pool_details = array( 'name'          => 'prediction_pool_spinner',
														'id'            => 'prediction_pool_spinner',
														'value'         => $current_pool,
														'min'		    => $min_pool,
														'max' 	        => $max_pool,
														'step'			=> '1',
														'style'         => 'margin:5px 8px -5px 5px; width:4em; height:30px;'
									);
									echo form_input($pool_details); ?>
								</div>
								<div style="display: inline-block; margin-left: 100px; vertical-align: top;">
									<?php $frm_attr = array('id' => 'frmnumberpool_submit', 'style' => 'display:inline-block;');
									echo form_open(base_url('admin/statistics/h_w_c/'.$lottery->id), $frm_attr);
									?><input type="hidden" name="prediction_pool" id="prediction_pool_hidden" value="">
									<input type="hidden" name="original_prediction_pool" value="<?php echo $current_pool; ?>">
									<input type="hidden" name="change_pool" id="change_pool_hidden" value="">
									<?php
									$attr = array('class'	=> 'btn btn-success', 'style' => 'vertical-align: top;');
									echo form_submit("change_pool", "Change Number Pool", $attr);
									echo form_close(); ?>
								</div>
							</div>
						</div>
						
						<!-- Third Row: H-W-C Prediction Option -->
						<div style="margin-top: 15px; white-space: nowrap; text-align: center; border-top: 1px solid #dee2e6; padding-top: 15px;">
							<div style="display: inline-block; text-align: left; vertical-align: top;">
								<strong>Prediction Option:</strong><br>
								<div class="form-check form-check-inline" style="margin-top: 6px;">
									<input class="form-check-input" type="radio" name="hwc_option_display" id="hwc_option_1" value="1" <?=($hwc_option==1 ? 'checked' : '');?>>
									<label class="form-check-label" for="hwc_option_1">Top Ranked / Count H-W-C</label>
								</div>
								<div class="form-check form-check-inline" style="margin-top: 6px;">
									<input class="form-check-input" type="radio" name="hwc_option_display" id="hwc_option_2" value="2" <?=($hwc_option==2 ? 'checked' : '');?>>
									<label class="form-check-label" for="hwc_option_2">Manual Selected H-W-C</label>
								</div>
								<div id="hwc_manual_select" style="margin-top: 8px; display:<?=($hwc_option==2 ? 'block' : 'none');?>;">
									<label for="hwc_select_dropdown">Select H-W-C Group:</label>
									<select id="hwc_select_dropdown" name="hwc_select_display" class="form-control" style="display:inline-block; width:auto; margin-left:5px;">
										<?php if(!empty($h_w_c_group)): $rank_idx = 1; foreach($h_w_c_group as $pattern => $display): ?>
										<option value="<?=$rank_idx;?>" <?=($hwc_select==$rank_idx ? 'selected' : '');?>><?=htmlspecialchars($display);?></option>
										<?php $rank_idx++; endforeach; endif; ?>
									</select>
								</div>
							</div>
							<div style="display: inline-block; margin-left: 80px; vertical-align: top; margin-top: 22px;">
								<?php $frm_attr = array('id' => 'frmhwcoption_submit', 'style' => 'display:inline-block;');
								echo form_open(base_url('admin/statistics/h_w_c/'.$lottery->id), $frm_attr); ?>
								<input type="hidden" name="hwc_option" id="hwc_option_hidden" value="<?=$hwc_option;?>">
								<input type="hidden" name="hwc_select" id="hwc_select_hidden" value="<?=$hwc_select;?>">
								<?php $attr = array('class' => 'btn btn-primary', 'style' => 'vertical-align: top;');
								echo form_submit("change_hwc_option", "Change H-W-C Option", $attr);
								echo form_close(); ?>
							</div>
						</div>
						
						<!-- Predicted Numbers Display -->
						<?php if(!empty($hwc_predictions)): ?>
						<div style="margin: 15px; padding: 12px 15px; background-color: #e8f5e9; border-left: 4px solid #28a745; border-radius: 4px;">
							<strong>Predicted Numbers for the Next Draw</strong>
							<?php
							$option_label = ($hwc_option == 2) ? 'Manual Selected' : 'Top Ranked';
							if(!empty($h_w_c_group)):
								$group_patterns = array_keys($h_w_c_group);
								$idx = $hwc_select - 1;
								$used_pattern = isset($group_patterns[$idx]) ? $group_patterns[$idx] : (isset($group_patterns[0]) ? $group_patterns[0] : '');
								$used_display  = isset($h_w_c_group[$used_pattern]) ? $h_w_c_group[$used_pattern] : $used_pattern;
							else:
								$used_display = '';
							endif;
							?>
							<span class="text-muted" style="font-size:0.85em; margin-left:8px;">(<?=$option_label;?><?=($used_display ? ' &mdash; ' . htmlspecialchars($used_display) : '');?>)</span><br>
							<div style="margin-top: 8px;">
								<?php $pred_numbers = explode(',', $hwc_predictions);
								foreach($pred_numbers as $num): ?>
								<span style="display:inline-block; background:#28a745; color:#fff; border-radius:50%; width:36px; height:36px; line-height:36px; text-align:center; margin:3px; font-weight:bold;"><?=trim($num);?></span>
								<?php endforeach; ?>
							</div>
						</div>
						<?php endif; ?>
						
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
											<th colspan = "2" class = "datafont">Last <?=$sel_range*100; ?> Draws</th>
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
	<script>
	// Function to update heat level hidden fields when button is clicked
	function updateHeatValues() {
		var hotValue = $('#hots_spinner').val();
		var warmValue = $('#warms_spinner').val();
		var coldValue = $('#colds_spinner').val();
		
		$('#hots_hidden').val(hotValue);
		$('#warms_hidden').val(warmValue);
		$('#colds_hidden').val(coldValue);
	}
	
	// Function to update prediction pool hidden field when button is clicked
	function updatePoolValue() {
		var poolValue = $('#prediction_pool_spinner').val();
		$('#prediction_pool_hidden').val(poolValue);
	}

	$(document).ready(function(){
    	$('#frmheat_submit').on('submit', function(e){
        	e.preventDefault();
        	var hotValue = $('#hots_spinner').val();
			var warmValue = $('#warms_spinner').val();
			var coldValue = $('#colds_spinner').val();
			var totalValue = parseInt(hotValue)+parseInt(warmValue)+parseInt(coldValue);
			var maxValue = <?=$lottery->maximum_ball;?>;
			if(totalValue!=maxValue) {
				$('#error').html("<h3 class='bg-warning' style = 'margin: 15px; text-align:center;'>The hot Value: <strong>"+hotValue+"</strong> Warm Value: <strong>"+warmValue+"</strong> Cold Value: <strong>"+coldValue+"</strong> does not equal the maximum ball value of "+maxValue+". Please Re-enter values.");
			}
			else {
				// Update hidden fields with current values before submit
				updateHeatValues();
				$('#heat_hidden').val('Change Heat Levels'); // Set button value
				$('#error').html("");
				this.submit();
			}
    	});
    	
    	$('#frmnumberpool_submit').on('submit', function(e){
        	e.preventDefault();
        	var poolValue = parseInt($('#prediction_pool_spinner').val());
			var minPool = <?=$lottery->balls_drawn;?>;
			var maxPool = <?=intval($lottery->maximum_ball / 2);?>;
			if(poolValue < minPool || poolValue > maxPool) {
				$('#error').html("<h3 class='bg-warning' style = 'margin: 15px; text-align:center;'>The Prediction Number Pool value <strong>"+poolValue+"</strong> must be between "+minPool+" and "+maxPool+". Please Re-enter value.");
			}
			else {
				// Update hidden field with current value before submit
				updatePoolValue();
				$('#change_pool_hidden').val('Change Number Pool'); // Set button value
				$('#error').html("");
				this.submit();
			}
    	});
	});
	
	// H-W-C Option radio buttons: show/hide manual select dropdown
	$('input[name="hwc_option_display"]').on('change', function() {
		var val = $(this).val();
		$('#hwc_option_hidden').val(val);
		if(val === '2') {
			$('#hwc_manual_select').show();
		} else {
			$('#hwc_manual_select').hide();
		}
	});
	// Sync the manual select dropdown value to the hidden field
	$('#hwc_select_dropdown').on('change', function() {
		$('#hwc_select_hidden').val($(this).val());
	});
	// On form submit, ensure hidden fields are up to date
	$('#frmhwcoption_submit').on('submit', function() {
		$('#hwc_option_hidden').val($('input[name="hwc_option_display"]:checked').val());
		$('#hwc_select_hidden').val($('#hwc_select_dropdown').val());
	});

	// Initialize jQuery UI spinners with proper IDs
    $("#hots_spinner").spinner({
    	min: 1,
    	max: 50
    });
	$("#warms_spinner").spinner({
    	min: 1,
    	max: 50
    });
	$("#colds_spinner").spinner({
    	min: 1,
    	max: 50
    });
	$("#prediction_pool_spinner").spinner({
    	min: <?=$lottery->balls_drawn;?>,
    	max: <?=intval($lottery->maximum_ball / 2);?>
    });
</script>