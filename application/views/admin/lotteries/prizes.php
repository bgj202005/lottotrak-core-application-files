
<!-- Bootstrap Form Helpers -->
<link href="<?php echo site_url('css/bootstrap-formhelpers.min.css');?>" rel="stylesheet" media="screen">
<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	  <script src="js/html5shiv.js"></script>
	  <script src="js/respond.min.js"></script>
	<![endif]-->
<!-- Bootstrap Form Helpers -->	
<script src="<?php echo site_url('js/bootstrap-formhelpers.min.js');?>"></script>
<section>
	<div class="container">
			<?php echo form_open(base_url().'admin/lotteries/prizes/'.$lottery->id); ?>
			<h2><?php echo 'Current Lottery: '.$lottery->lottery_name; ?></h2>
			<?php if (!empty($message)) ?> <h3 class="bg-warning" style = "text-align:center;"><?=$message; ?></h3>
			<div class="row">
				<div class="col-9" style ="width:100%;">
					<div class = "card">
						<div class = "card-body">
							<div class="form-group">
							<!-- Lottery Description  -->
							<div class="form-group form-group-lg row"> 
									<?php $extra = array('class' => 'col-4 col-form-label col-form-label-md', 'style' => 'white-space: nowrap;');
									echo form_label('Lottery Description:', 'lottery_descriptiuon_lb', $extra); ?>
								<div class="col-8">
									<?php echo form_label(wordwrap($lottery->lottery_description, 64, '<br />', FALSE), 'lottery_description_lb', $extra); ?>
								</div>
							</div>

							<!-- Current State or Province Field -->
							<div class="form-group form-group-lg row"> 
								<?php $extra = array('class' => 'col-4 col-form-label col-form-label-md');
								echo form_label('Current State / Province:', 'state_province', $extra); ?>
								<div class="col-8" style="margin-top:10px;">
									<span class="bfh-states col-form-label-lg" data-country="<?=$lottery->lottery_country_id; ?>" data-state="<?=$lottery->lottery_state_prov; ?>"></span></label>
								</div>
							</div>
							<!-- Country Field -->
							<div class="form-group form-group-lg row"> 
								<?php $extra = array('class' => 'col-4 col-form-label col-form-label-md');
								echo form_label('Country', 'country_lb', $extra); ?>
								<div class="col-8">
									<span id="countries_states2" class="bfh-selectbox bfh-countries col-form-label-lg" data-flags="true" data-country=<?=$lottery->lottery_country_id; ?> data-name="lottery_country_id">
								</div>
							</div>
							<!-- Lottery Pick Game -->
							<div class="form-group form-group-lg row"> 
									<?php $extra = array('class' => 'col-4 col-form-label col-form-label-md');
									echo form_label('Pick:', 'lottery_pick_lb', $extra); ?>
								<div class="col-8">
									<?php echo form_label($lottery->balls_drawn, 'lottery_balls_drawn_lb', $extra); ?>
								</div>
							</div>
							<!-- Lottery Range  -->
							<div class="form-group form-group-lg row"> 
									<?php $extra = array('class' => 'col-4 col-form-label col-form-label-md');
									echo form_label('Lottery Range:', 'lottery_Range_lb', $extra); ?>
								<div class="col-8">
									<?php echo form_label('From '.$lottery->minimum_ball.' To '.$lottery->maximum_ball, 'lottery_range_lb', $extra); ?>
								</div>
							</div>
							<!-- Extra / Bonus Ball  -->
							<div class="form-group form-group-lg row"> 
									<?php $extra = array('class' => 'col-4 col-form-label col-form-label-md');
									echo form_label('Extra / Bonus Ball?', 'extra_ball_lb', $extra); ?>
								<div class="col-8">
									<?php echo form_label((!empty($lottery->extra_ball) ? 'YES' : 'NO'), 'extra_ball_lb', $extra); ?>
								</div>
							</div>
							<!-- Draw Days  -->
							<div class="form-group form-group-lg row"> 
									<?php $extra = array('class' => 'col-4 col-form-label col-form-label-md');
									echo form_label('Days of Draw:', 'draw_days_lb', $extra); ?>
								<div class="col-8">
									<?php $s = '';
										if ($lottery->monday) $s .= 'Mondays<br />';
										if ($lottery->tuesday) $s .= 'Tuesdays<br />';
										if ($lottery->wednesday) $s .= 'Wednesdays<br />';
										if ($lottery->thursday) $s .= 'Thursdays<br />';
										if ($lottery->friday) $s .= 'Fridays<br />';
										if ($lottery->saturday) $s .= 'Saturdays<br />';
										if ($lottery->sunday) $s .= 'Sundays';
										echo form_label($s, 'draw_days_lb', $extra); ?>
								</div>
							</div>
							<!-- Allow Duplicates? / Extra / Bonus Balls  -->
							<div class="form-group form-group-lg row"> 
									<?php $extra = array('class' => 'col-4 col-form-label col-form-label-md');
									echo form_label('Allow Duplicate Extra / Bonus Ball?', 'duplicate_extra_lb', $extra); ?>
								<div class="col-8">
									<?php echo form_label((!empty($lottery->duplicate_extra_ball) ? 'YES' : 'NO'), 'duplicate_extra_ball_lb', $extra); ?>
								</div>
							</div>
							<!-- Extra Ball Range  -->
							<div class="form-group form-group-lg row"> 
									<?php $extra = array('class' => 'col-4 col-form-label col-form-label-md');
									echo form_label('Lottery Range:', 'Extra_Ball_Range_lb', $extra); ?>
								<div class="col-8">
									<?php echo form_label('From '.$lottery->minimum_extra_ball.' To '.$lottery->maximum_extra_ball, 'lottery_extra_ball_range_lb', $extra); ?>
								</div>
							</div>
								<div class="card border-dark" style="max-width: 50rem; margin-bottom: 2rem;">
									<div class="card-header"><?php echo $lottery->lottery_name; ?> Prizes</div>
									<div class="card-body text-dark">
										<h5 class="card-title">Winning Prize Breakdown</h5>
										<p class="card-text">
											<div class="container">
												<div class="row">
													<div class="col-md-6">
														<?php // $row = 0; 
														echo '<p class="h5" style = "margin-bottom:10px;">'.form_checkbox('checkall', '', '', 'id = "checkall"').' Check All / Check None</p>'; 
														foreach($lottery->prizes as $prize): 
														// if($row%4==0): echo "<div class='col-md-6>"; endif; ?>
														<div class="form-check" style = "word-wrap: none; margin-bottom:5px;">
															<?php $extra = array('class' => 'checkbox');
															//var_dump($lottery->set_prizes[$prize]);
															echo form_checkbox($prize, set_value($prize, '1'), set_checkbox($prize, '1', (isset($lottery->set_prizes[$prize]))), $extra); ?>
															<label>
															<?php if ($prize=='extra'): echo "Extra / Bonus Ball Only"; endif;
															if ($prize=='9_win'): echo "9 / ".$lottery->balls_drawn." Balls"; endif;
															if ($prize=='8_win_extra'): echo "8 / ".$lottery->balls_drawn." plus the Extra (Bonus)"; endif;
															if ($prize=='8_win'): echo "8 / ".$lottery->balls_drawn." Balls"; endif;
															if ($prize=='7_win_extra'): echo "7 / ".$lottery->balls_drawn." plus the Extra (Bonus)"; endif;
															if ($prize=='7_win'): echo "7 / ".$lottery->balls_drawn." Balls"; endif;
															if ($prize=='6_win_extra'): echo "6 / ".$lottery->balls_drawn." plus the Extra (Bonus)"; endif;
															if ($prize=='6_win'): echo "6 / ".$lottery->balls_drawn." Balls"; endif;
															if ($prize=='5_win_extra'): echo "5 / ".$lottery->balls_drawn." plus the Extra (Bonus)"; endif;
															if ($prize=='5_win'): echo "5 / ".$lottery->balls_drawn." Balls"; endif;
															if ($prize=='4_win_extra'): echo "4 / ".$lottery->balls_drawn." plus the Extra (Bonus)"; endif;
															if ($prize=='4_win'): echo "4 / ".$lottery->balls_drawn." Balls"; endif;
															if ($prize=='3_win_extra'): echo "3 / ".$lottery->balls_drawn." plus the Extra (Bonus)"; endif;
															if ($prize=='3_win'): echo "3 / ".$lottery->balls_drawn." Balls"; endif;
															if ($prize=='2_win_extra'): echo "2 / ".$lottery->balls_drawn." plus the Extra (Bonus)"; endif;
															if ($prize=='2_win'): echo "2 / ".$lottery->balls_drawn." Balls"; endif;
															if ($prize=='1_win_extra'): echo "1 / ".$lottery->balls_drawn." plus the Extra (Bonus)"; endif;
															if ($prize=='1_win'): echo "1 / ".$lottery->balls_drawn." Balls"; endif; ?>
															</label>
														</div>
														<?php // $row++;
														// if($row%4==0): echo "</div>"; endif;	 
														endforeach; ?>
													</div> 
												</div>
											</div>
										</p>
									</div>
								</div>
								<div style = "text-align: center;">
									<?php echo form_submit('prizes', 'Save Prizes', 'class="btn btn-primary btn-lg btn-info" id = "prizes"');
									$js = "location.href='".base_url()."admin/lotteries'";
									$attributes = array(
										'class' 	=> "btn btn-primary btn-lg btn-info", 
										'onClick' 	=> "$js", 
										'style' 	=> "margin-left:20px;"
									);
									echo form_button('lottery_list', 'Back to Lotteries List', $attributes);
									echo form_close(); ?> <!-- </form> -->
								</div>
							</div>
						</div>
					</div>
				</div>
		</div>
	</div>
</section>
<script type='text/javascript'>
 $(document).ready(function(){
   // Check or Uncheck All checkboxes
   $("#checkall").change(function(){
     var checked = $(this).is(':checked');
     if(checked){
       $(".checkbox").each(function(){
         $(this).prop("checked",true);
       });
     }else{
       $(".checkbox").each(function(){
         $(this).prop("checked",false);
       });
     }
   });
 
  // Changing state of CheckAll checkbox 
  $(".checkbox").click(function(){
 
    if($(".checkbox").length == $(".checkbox:checked").length) {
      $("#checkall").prop("checked", true);
    } else {
      $("#checkall").removeAttr("checked");
    }

  });
});
</script>