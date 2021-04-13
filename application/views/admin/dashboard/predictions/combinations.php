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
}
</style>
	<h2><?php echo 'Calculate Combinations for: '.$lottery->lottery_name; ?></h2>
	<?php $max = $lottery->balls_drawn; 
	   $b = 1; 
	   ?>	
	<h5 style = "text-align:left"><?php echo anchor('admin/predictions', 'Back to Predictions Dashboard', 'title="Back to Predictions"'); ?></h5>
	<section>
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="card mt-3 tab-card">
						<div class="card-header tab-card-header">
							<H1 style = "text-align:center;">Combinations Calculator</H1>
						</div>
						<div class="tab-content" id="myTabContent">
							<?php echo form_open(base_url().'admin/dashboard/predictions/combinations/'.$lottery->id); ?>
							<div class = "col-12" style = "margin-top:2em;">
								<?php echo img(base_url().'images/C(N_R).png'); ?>
								<div class="form-group form-group-lg row"> 
								<?php $extra = array('class' => 'col-4 col-form-label col-form-label-md', 'style' => 'white-space: nowrap;');
									echo form_label('Number of Balls to Predict (6 to '.$lottery->maximum_ball.') (N)', 'ballpredict_lb', $extra); ?>
								<?php $extra = array('class' => 'col-4 form-control', 'id' => 'formGroupInputLarge',
											'maxlength' => '5', 'size' => '5', 'style'=> 'margin-left: 15px; width:50%');  
									echo form_input('ball_predict',set_value('ball_predict', $lottery->predict), $extra); 
									echo form_error('ball_predict', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>'); ?>
								</div>
								<!-- Lottery Pick Game -->
								<div class="form-group form-group-lg row"> 
									<?php $extra = array('class' => 'col-4 col-form-label col-form-label-md');
									echo form_label('Pick (R):', 'lottery_pick_lb', $extra);
									echo form_label($lottery->balls_drawn, 'lottery_balls_drawn_lb', $extra); ?>
								</div>
								<div class="form-group form-group-lg row">
									<?php $extra = array('class' => 'btn btn-primary btn-lg btn-info', 
														'style' => "padding:5px; margin: 0 auto; display: block; margin:20px 20px");
										echo form_submit('submit', 'Update Full Combination', $extra);
										$js = "location.href='".base_url()."admin/predictions'";
										$attributes = array(
										'class' 	=> "btn btn-primary btn-lg btn-info",
										'onClick' 	=> "$js", 
										'style' 	=> "padding:5px; margin: 0 auto; display: block; margin:20px 20px;"
									);
										echo form_button('prediction_list', 'Back to Prediction List', $attributes); 
										echo form_close(); ?> <!-- </form> -->
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>