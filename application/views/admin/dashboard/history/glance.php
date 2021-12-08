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
											<?php 
												$js = "location.href='".base_url()."admin/history/glance/".$lottery->id."/".(!$interval ? $sel_range : ($sel_range*100))."/extra'";
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
												$js = "location.href='".base_url()."admin/history/glance/".$lottery->id."/".(!$interval ? $sel_range : ($sel_range*100))."/draws'";
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
												<canvas height="20"></canvas>
												</div>
											</div>
										</div>
										<!-- /.card -->

										<div class="card mt-4 mb-4">
										<div class="card-header border-0">
											<h3 class="card-title">Sum of Winning Numbers & Digits</h3>
										</div>
										<div class="card-body table-responsive p-0">
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
											<p class="d-flex flex-column">
												<span class="text-bold text-lg">$18,230.00</span>
												<span>Sales Over Time</span>
											</p>
											<p class="ml-auto d-flex flex-column text-right">
												<span class="text-success">
												<i class="fas fa-arrow-up"></i> 33.1%
												</span>
												<span class="text-muted">Since last month</span>
											</p>
											</div>
											<!-- /.d-flex -->

											<div class="position-relative mb-4">
											<canvas id="sales-chart" height="200"></canvas>
											</div>

											<div class="d-flex flex-row justify-content-end">
											<span class="mr-2">
												<i class="fas fa-square text-primary"></i> This year
											</span>

											<span>
												<i class="fas fa-square text-gray"></i> Last year
											</span>
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