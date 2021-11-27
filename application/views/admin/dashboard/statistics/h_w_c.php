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
						<div class = "text-center" style = "display:inline-block;">
							<?php $frm_attr = array('id' => 'frmheat');
							echo form_open(base_url('admin/statistics/h_w_c/'.$lottery->id), $frm_attr);
							echo form_label("Hots:", "id => 'lb_hots'");
							$h_details = array( 'name'          => 'hots',
												'id'            => 'hots',
												'value'         => $lottery->H,
												'min'		    => '1',
												'max' 	        => '50',
												'step'			=> '1',
												'style'         => 'margin:10px 10px -5px; width:3em; height:30px;',
												'readonly'		=> 'true'
							);
							echo form_input($h_details);
							echo form_label("Warms:", "id => 'lb_warms'");
							$w_details = array( 'name'          => 'warms',
												'id'            => 'warms',
												'value'         => $lottery->W,
												'min'		    => '1',
												'max' 	        => '50',
												'step'			=> '1',
												'style'         => 'margin:10px 10px -5px; width:3em; height:30px;',
												'readonly'		=> 'true'
							);
							echo form_input($w_details);
							echo form_label("Colds:", "id => 'lb_colds'");
							$c_details = array( 'name'          => 'colds',
												'id'            => 'colds',
												'value'         => $lottery->C,
												'min'		    => '1',
												'max' 	        => '50',
												'step'			=> '1',
												'style'         => 'margin:10px 10px -5px; width:3em; height:30px;',
												'readonly'		=> 'true'
							);
							echo form_input($c_details);
							$attr = array('class'	=> 'btn btn-primary', 'style' => 'margin: 4px; margin-left:10px;');
							echo form_submit("heat", "Change Heat Levels", $attr);
							echo form_close(); ?>
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
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<script>
	$(document).ready(function(){
    $('#frmheat').on('submit', function(e){
        e.preventDefault();
        var hotValue = $('#hots').val();
		var warmValue = $('#warms').val();
		var coldValue = $('#colds').val();
		var totalValue = parseInt(hotValue)+parseInt(warmValue)+parseInt(coldValue);
		var maxValue = <?=$lottery->maximum_ball;?>;
		if(totalValue!=maxValue) {
			$('#error').html("<h3 class='bg-warning' style = 'margin: 15px; text-align:center;'>The hot Value: <strong>"+hotValue+"</strong> Warm Value: <strong>"+warmValue+"</strong> Cold Value: <strong>"+coldValue+"</strong> does not equal the maximum ball value of "+maxValue+". Please Re-enter values.");
		}
		else {
			$('#error').html("");
			this.submit();
		}
    	});
	});
    $("#hots").spinner();
	$("#warms").spinner();
	$("#colds").spinner();
	
</script>