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
	<h2><?php echo 'AT A GLANCE Highlights for the '.$lottery->lottery_name.' Lottery'; ?></h2>
	<?php $max = $lottery->balls_drawn; 
	   $b = 1; 
	   ?>	
	<h5 style = "text-align:left"><?php echo anchor('admin/history', 'Back to Lottery Win History Dashboard', 'title="Back to Lottery Win History"'); ?></h5>
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
														<a class="dropdown-item active" href="<?=base_url('admin/history/glance/'.$lottery->id)?>">All Draws (<?=$lottery->last_drawn['range'];?>) </a>
													<?php else:
														$sel_range = (integer) $lottery->last_drawn['sel_range']; // Selected a different range from the complete range of draws?
														for($i = 1; $i <= $interval; $i++):
															$step = $i * 100;	// in multiples of 100
															if($i!=$interval): ?>
																<a class="dropdown-item <?php if($i==$sel_range) echo 'active'; ?>" href="<?=base_url('admin/history/glance/'.$lottery->id.'/'.$step);?>">Last <?=$step;?></a>
															<?php else : ?>
																<a class="dropdown-item <?php if($i==$sel_range) echo 'active'; ?>" href="<?=base_url('admin/history/glance/'.$lottery->id.'/'.$lottery->last_drawn['all']);?>">All Draws (<?=$lottery->last_drawn['all'];?>)</a>
															<?php endif;
														endfor; ?> 
														<?php endif;?>
												</div>
											</div>
										</div>
										<div class="p-2">	
											<div class="form-check">
											<?php // Removed $js = "location.href='".base_url()."admin/history/glance/".$lottery->id."/".(!$interval ? $sel_range : ($sel_range*100))."/extra'";
												$js = "location.href='".base_url()."admin/history/glance/".$lottery->id."/0/extra'";
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
											<?php // Removed $js = "location.href='".base_url()."admin/history/glance/".$lottery->id."/".(!$interval ? $sel_range : ($sel_range*100))."/draws'";
												$js = "location.href='".base_url()."admin/history/glance/".$lottery->id."/0/draws'";
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
								<div class="content">
								<div class="container-fluid">
									<div class="row">
									<div class="col-lg-6 mt-4">
										<div class="card">
											<div class="card-header border-0">
												<div class="d-flex justify-content-between">
												<h3 class="card-title">Current Lottery Trends</h3>
												</div>
											</div>
											<div class="card-body">
												<div class="d-flex">
												<h5 class="d-flex flex-column ">
													<span class="text-bold text-lg">Over the last <strong><?= $lottery->last_drawn['range']; ?> </strong> Draws.</span>
													<br />
													<span>This is the current number of UP / DOWN Trends:</span>
												<?php $trends = $lottery->last_drawn['trends']; // Iterate into an array
													$trend_arr = explode (",", $trends);
													echo "<br />";
													foreach($trend_arr as $c => $t):
														echo "<br />";
														if (substr($t, 0, 3)== 'up=') echo "<style='display: inline'>UP Trends: <strong>".(int) filter_var($t, FILTER_SANITIZE_NUMBER_INT)."</strong></style>";
														if (substr($t, 0, 5)== 'down=') echo "<style='display: inline'> DOWN Trends: <strong>".(int) filter_var($t, FILTER_SANITIZE_NUMBER_INT)."</strong></style>";  
														if (strtotime($t)) echo "<style='display: inline'> Last Occurrence Date: <strong>".date("l, F j, Y",strtotime(str_replace('/','-',$t)))."</strong>";
													endforeach;
													if($trend_arr[4]=='up') echo "<br /> The current Trend over this range is: <strong>UP</strong><br />The number of occurrences at: <strong>".$trend_arr[3]."</strong>";
													if($trend_arr[4]=='down') echo "<br /> The current Trend over this range is: <strong>DOWN</strong><br /> The number of occurrences at: <strong>".$trend_arr[3]."</strong>";
												?>
												</h5>
												</div>
												<!-- /.d-flex -->
												<div class="position-relative mb-2">
												<canvas height="140"></canvas>
												</div>
											</div>
										</div>
										<!-- /.card -->

										<div class="card mt-4 mb-4">
										<div class="card-header border-0">
											<div class="d-flex justify-content-between">	
											<h3 class="card-title">Sum of Winning Numbers & Digits</h3>
											</div>
										</div>
										<div class="card-body">
											<div class="d-flex">
												<h5 class="d-flex flex-column">
												<span>This is the top SUMS and DIGIT SUMS over the last <strong><?= $lottery->last_drawn['range']; ?> </strong> Draws.</span>
												<?php $sums_history = $lottery->last_drawn['sums_history']; // Total Sums
													$sums_string = explode ("|",$sums_history);
													$sums_counts = explode(",",$sums_string[0]); // These are number of sums with the occurrences. e.g. 152=3, .
													$sums_range = explode(",",$sums_string[1]); // These are the top numbers that are known to repeat during a given range
													$percents = array(5=>"0 to 5",10=>'10 to 14',15=>'15 to 19',20=>'20 to 24',25=>'25 to 29',30=>'30 to 34',35=>'35 to 39',40=>'40 to 44',45=>'45 to 49',50=>'51 to 54');
													$s = '';
													foreach($sums_counts as $c => $t):
														$sums = explode('=', $t);
														if(($sums[0]!='0')&&($sums[1]!='0')) $s .= 'Sum: <strong>'.$sums[0].'</strong> has occurred <strong>'.$sums[1]."</strong> times.<br />";
													endforeach;
													$s = (!empty($s) ? substr($s, 0, -2).'.' : 'There are no sums for this lottery.<br />');
													echo "<br />";
													echo "<style='display: inline'>".$s."</style>";
													$s = '';
													$top = explode('=', $sums_range[0]);
													foreach($percents as $k => $v):
														if(intval($top[0])>=intval($k)):
														$s = "The total sum had an <strong>".$sums_range[1]."</strong> of <strong>".$v."</strong>% from the previous draw and has occurred <strong>".$top[1]."</strong> times.<br />";
														break; 
														endif;			
													endforeach;
													echo "<br />";
													echo "<style='display: inline'>".$s."</style>";		
												 	$digits_history = $lottery->last_drawn['digits_history']; // Digit Sums
													$digits_string = explode ("|",$digits_history);
													$digits_counts = explode(",",$digits_string[0]); // These are number of sums with the occurrences. e.g. 152=3, .
													$digits_range = explode(",",$digits_string[1]); // These are the top numbers that are known to repeat during a given range
													$percents = array(5=>"0 to 5",10=>'10 to 14',15=>'15 to 19',20=>'20 to 24',25=>'25 to 29',30=>'30 to 34',35=>'35 to 39',40=>'40 to 44',45=>'45 to 49',50=>'51 to 54');
													$s = '';
													foreach($digits_counts as $c => $t):
														$digits = explode('=', $t);
														if(($digits[0]!='0')&&($digits[1]!='0')) $s .= 'Digits Sum: <strong>'.$digits[0].'</strong> has occurred <strong>'.$digits[1]."</strong> times.<br />";
													endforeach;
													$s = (!empty($s) ? substr($s, 0, -2).'.' : 'There are no digit sums for this lottery.<br />');
													echo "<br />";
													echo "<style='display: inline'>".$s."</style>";
													$s = '';
													$top = explode('=', $digits_range[0]);
													foreach($percents as $k => $v):
														if(intval($top[0])>=intval($k)):
														$s = "The digits sum had an <strong>".$digits_range[1]."</strong> of <strong>".$v."</strong>% from the previous draw and has occurred <strong>".$top[1]."</strong> times.";
														break; 
														endif;			
													endforeach;
													echo "<br />";
													echo "<style='display: inline'>".$s."</style>";		
													?>				
												</h5>
											</div>
										</div>
										</div>
										<!-- /.card -->
									</div>
									<!-- /.col-md-6 -->
									<div class="col-lg-6  mt-4">
										<div class="card">
										<div class="card-header border-0">
											<div class="d-flex justify-content-between">
											<h3 class="card-title">Repeats and Consecutive Numbers</h3>
											</div>
										</div>
										<div class="card-body">
											<div class="d-flex">
											<h5 class="d-flex flex-column">
												<span>This is the current Repeaters over the last <strong><?= $lottery->last_drawn['range']; ?> </strong> Draws.</span>
												<?php $repeats = $lottery->last_drawn['repeats']; // Iterate into an array
													$repeat_string = explode ("|",$repeats);
													$repeat_counts = explode(",",$repeat_string[0]); // These are number of repeats after a range. e.g. 0 = 75, 75 draws.
													$repeat_tops = explode(",",$repeat_string[1]); // These are the top numbers that are known to repeat during a given range
													$s = " ";
													foreach($repeat_counts as $c => $t):
														$repeaters = explode('=', $t);
														if(!empty($repeaters[1])):
															 $s .= '<strong>'.$repeaters[0].'</strong> Repeaters in <strong>'.$repeaters[1]."</strong> draws ("
															.intval((($repeaters[1]/$lottery->last_drawn['range'])*100))."% of the time).<br />";
														endif;
													endforeach;
													echo "<br />";
													echo "<style='display: inline'>".$s."</style>";
													$s = '';
													foreach($repeat_tops as $c => $t):
														$tops = explode('=', $t);
														if(($tops[0]!='0')&&($tops[1]!='0')) $s .= '<strong>'.$tops[0].'</strong> has occurred <strong>'.$tops[1]."</strong> times, ";
													endforeach;
													$s = (!empty($s) ? substr($s, 0, -2).'.' : 'There are no top repeating numbers.<br />');
													echo "<br />";
													echo "<style='display: inline'>".$s."</style>";
												 	$consecs = $lottery->last_drawn['consecutives']; // Iterate into an array
													$consecs_string = explode ("|",$consecs);
													$consecs_counts = explode(",",$consecs_string[0]); // These are number of consecutives a range. e.g. 0=75, no consecutives for 75 draws.
													$consecs_last = explode("=",$consecs_string[1]); // This is the last consecutive and the date
													$s = " ";
													foreach($consecs_counts as $c => $t):
														$consecutives = explode('=', $t);
															if($consecutives[1]):
																$s .= '<strong>'.$consecutives[0].'</strong> consecutives drawn, <strong> '.$consecutives[1].'</strong> times (' 
																.intval((($consecutives[1]/$lottery->last_drawn['range'])*100))."% of the time).<br />";
															endif;
													endforeach;
													echo "<br />";
													echo "<style='display: inline'>".$s."</style>";
													$s = '';
													if(($consecs_last[0]!='0')&&($consecs_last[1]!='0')) $s .= '<strong>'.$consecs_last[0].'</strong> consecutive number(s) were last drawn on: <strong>'.date("l, F j, Y",strtotime(str_replace('/','-',$consecs_last[1]))).".";
													echo "<br />";
													echo "<style='display: inline'>".$s."</style>";
											?>
											</h5>
											</div>
											<!-- /.d-flex -->
											<div class="position-relative mb-2">
												<canvas height="20"></canvas>
												</div>
										</div>
										</div>
										<!-- /.card -->
										<div class="card mt-4 mb-4">
										<div class="card-header border-0">
											<h3 class="card-title">Odd / Even Winners & Number Range</h3>
										</div>
										<div class="card-body">
											<div class="d-flex justify-content-between align-items-center border-bottom mb-3">
											<p class="text-success text-xl">
												<i class="ion ion-ios-refresh-empty"></i>
											</p>
											<p class="d-flex flex-column text-right">
												<span class="font-weight-bold">
												<i class="ion ion-android-arrow-up text-success"></i> 12%
												</span>
												<span class="text-muted">CONVERSION RATE</span>
											</p>
											</div>
											<!-- /.d-flex -->
											<div class="d-flex justify-content-between align-items-center border-bottom mb-3">
											<p class="text-warning text-xl">
												<i class="ion ion-ios-cart-outline"></i>
											</p>
											<p class="d-flex flex-column text-right">
												<span class="font-weight-bold">
												<i class="ion ion-android-arrow-up text-warning"></i> 0.8%
												</span>
												<span class="text-muted">SALES RATE</span>
											</p>
											</div>
											<!-- /.d-flex -->
											<div class="d-flex justify-content-between align-items-center mb-0">
											<p class="text-danger text-xl">
												<i class="ion ion-ios-people-outline"></i>
											</p>
											<p class="d-flex flex-column text-right">
												<span class="font-weight-bold">
												<i class="ion ion-android-arrow-down text-danger"></i> 1%
												</span>
												<span class="text-muted">REGISTRATION RATE</span>
											</p>
											</div>
											<!-- /.d-flex -->
										</div>
										</div>
									</div>
									<!-- /.col-md-6 -->
									</div>
									<!-- /.row -->
								</div>
								<!-- /.container-fluid -->
								</div>
								<!-- /.content -->
							</div>
					</div>
				</div>
			</div>
	</section>