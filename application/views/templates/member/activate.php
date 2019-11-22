<!-- Bootstrap Form Helpers -->
<link href="<?php echo site_url('css/bootstrap-formhelpers.min.css');?>" rel="stylesheet" media="screen">
<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	  <script src="js/html5shiv.js"></script>
	  <script src="js/respond.min.js"></script>
	<![endif]-->
<!-- Bootstrap Form Helpers -->	
<script src="<?php echo site_url('js/bootstrap-formhelpers.min.js');?>"></script>	
<!--  Main Content -->
<section id="content">
    <div class="content-inner  col-centered">
	   <div class="row">
            <div class="col-xs-12 col-sm-6 col-md-8">
				<div class="row" style = "text-align:center;">	
					<?php if (!$status) 
					{ ?>
						<h1 class="bg-success" style = "color:#FFFFFF"> <?php echo $alert_message; ?></h1>
						<h2><?php echo $second_message; ?></h2>
						<div class="row">
						<div class="col-xs-10 col-md-8"></div></div></div>		
					<?php 
					}
					else 
					{ ?> <!--  if ($status=='active') -->
					<h1 class="bg-success" style = "color:#FFFFFF">
					<?php $errors = validation_errors(); 
					echo (!empty($errors) ? 'There are errors to correct before updating your profile.' : $alert_message); ?></h1>
					<h3>Please Update your account information below:</h3>	
				</div>
					<div class="row">
						<div class="col-xs-12 col-md-12"> <!-- col-xs-10 col-md-8 -->
							<?php $hidden = array('id' => $member->id); 
							echo form_open(base_url().'activate', 'class="form-horizontal"', $hidden); ?>
							<!-- Username Field -->
							<div class="form-group form-group-lg row"> 
                    		<?php $extra = array('class' => 'col-4 col-form-label col-form-label-lg');
                    		echo form_label('Username', 'username', $extra); ?>
                    		<div class="col-8">
								   <?php $extra = array('class' => 'form-control', 'id' => 'formGroupInputLarge',
								   'maxlength' => '50', 'size' => '50', 'style'=> 'width:100%');  
								   echo form_input('username',set_value('username', $member->username), $extra); 
								   echo form_error('username', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>'); ?>
								</div>
							</div>
							<!-- Email Field -->
							<div class="form-group form-group-lg row"> 
                    			<?php $extra = array('class' => 'col-4 col-form-label-lg');
                    			echo form_label('Email:', 'email', $extra); ?>
                    			<div class="col-8">
								<?php $extra = array('class' => 'form-control', 'id' => 'formGroupInputLarge',
								   'maxlength' => '50', 'size' => '50', 'style'=> 'width:100%');    
									echo form_input('email',set_value('email', $member->email), $extra); 
									echo form_error('email', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>'); ?>
								</div>
							</div>
							<p class="bg-info" style = "padding: 1em; text-align: center; color:snow;">Please enter a new password and confirm the new password.</p>
							<!-- Password Field -->
							<div class="form-group form-group-lg row"> 
                    			<?php $extra = array('class' => 'col-4 col-form-label-lg');
                    			echo form_label('Password', 'password', $extra); ?>
                    			<div class="col-8">
								<?php $extra = array('class' => 'form-control', 'id' => 'formGroupInputLarge',
								   'maxlength' => '50', 'size' => '50', 'style'=> 'width:100%');
									echo form_password('password',set_value('password', ''), $extra); 
									echo form_error('password', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>'); ?>
								</div>
							</div>
							<!-- Confirm Password -->
							<div class="form-group form-group-lg row"> 
                    			<?php $extra = array('class' => 'col-4 col-form-label col-form-label-lg');
                    			echo form_label('Confirm Password', 'confirm_password', $extra); ?>
                    			<div class="col-8">
								<?php $extra = array('class' => 'form-control', 'id' => 'formGroupInputLarge',
								   'maxlength' => '50', 'size' => '50', 'style'=> 'width:100%');
									echo form_password('confirm_password',set_value('confirm_password', ''), $extra); 
									echo form_error('confirm_password', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>'); ?>
								</div>
							</div>
							<!-- Firstname Field -->
							<div class="form-group form-group-lg row"> 
								<?php $extra = array('class' => 'col-4 control-label col-form-label-lg');
								echo form_label('First Name', 'first_name', $extra); ?>
								<div class="col-8">
								<?php $extra = array('class' => 'form-control', 'id' => 'formGroupInputLarge',
								   'maxlength' => '50', 'size' => '50', 'style'=> 'width:100%');
									echo form_input('first_name',set_value('first_name', $member->first_name), $extra); 
									echo form_error('first_name', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>'); ?>
								</div>
							</div>
							<!-- Lastname Field -->
							<div class="form-group form-group-lg row"> 
								<?php $extra = array('class' => 'col-4 col-form-label col-form-label-lg');
								echo form_label('Last Name', 'last_name', $extra); ?>
								<div class="col-8">
								<?php $extra = array('class' => 'form-control', 'id' => 'formGroupInputLarge',
								   'maxlength' => '50', 'size' => '50', 'style'=> 'width:100%');
									echo form_input('last_name',set_value('last_name', $member->last_name), $extra); ?>
								</div>
							</div>
							<!-- City Field -->
							<div class="form-group form-group-lg row"> 
								<?php $extra = array('class' => 'col-4 col-form-label col-form-label-lg');
								echo form_label('City', 'city', $extra); ?>
								<div class="col-8">
								<?php $extra = array('class' => 'form-control', 'id' => 'formGroupInputLarge',
								   'maxlength' => '50', 'size' => '50', 'style'=> 'width:100%');
									echo form_input('city',set_value('city', $member->city), $extra); ?>
								</div>
							</div>
							<!-- State or Province Field -->
							<div class="form-group form-group-lg row"> 
								<?php $extra = array('class' => 'col-4 col-form-label col-form-label-lg');
								echo form_label('State / Province:', 'state_province', $extra); ?>
								<div class="col-8">
									<?php // $extra = array('class' => 'form-control', 'id' => 'formGroupInputLarge', 'maxlength' => '50', 'size' => '50', 'style'=> 'width:100%');
									// echo form_input('state_prov',set_value('state_prov', $member->state_prov), $extra); ?>
									<div class="bfh-selectbox bfh-states" data-country="countries_states2" data-name="state_prov"></div>
									<?php echo form_error('state_prov', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>'); ?>
								</div>
							</div>
							<!-- Country Field -->
								<div class="form-group form-group-lg row"> 
									<?php $extra = array('class' => 'col-4 col-form-label col-form-label-lg');
									echo form_label('Country', 'country', $extra); ?>
								<div class="col-8">
									<?php //$extra = array('class' => 'btn btn-secondary btn-lg dropdown-toggle'); 
									//echo form_dropdown('country_id', array('0' => 'No Country Listed','1' => 'Canada', '2' => 'United States'), $member->country_id, $extra); ?>
									<div id="countries_states2" class="bfh-selectbox bfh-countries" data-flags="true" data-country="CA" data-name="country_id"></div>
									<?php echo form_error('country_id', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>'); ?>
								</div>
							</div><!-- Current Lottery Field -->
							<div class="form-group form-group-lg row"> 
								<?php $extra = array('class' => 'col-4 col-form-label col-form-label-lg');
								echo form_label('Lottery', 'lottery', $extra); ?>
								<div class="col-8">
									<?php $extra = array('class' => 'btn btn-secondary btn-lg dropdown-toggle');  
									echo form_dropdown('lottery_id', array('0' => 'No Lottery Listed',
								'1' => 'Canada 649', '2' => 'Power Ball'), $member->lottery_id, $extra); 
								echo form_error('lottery_id', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>'); ?>	
								</div>
							</div>
							<div style = "text-align: center;"><?php echo form_submit('submit', 'Update Profile', 'class="btn btn-primary btn-lg btn-info"');
								echo form_close(); ?> <!-- </form> -->
							</div>
						</div>
					</div>
				</div>
				<?php } ?> <!-- if ($status=='expired') -->
			<!--  Sidebar -->
			<div class="col-xs-6 col-md-4 sidebar">
				<section>
					<h2>Recent Lottery News</h2>
							<?php // echo anchor($news_article_link, '+ News archive'); ?>
							<?php $this->load->view('sidebar'); ?>
				</section>
			</div>
			<div class="row">
				<div class="col-md-4">
					<div class="wrapper">
						<div class="wrapper indent-bot4">
								 <div class="left-pad">
									<h3 class="indent-bot">best software &amp; game <strong>selection</strong></h3>
									<figure class="img-pos indent-bot float-sw2"><img src="<?php echo base_url(); ?>images/page1-img1.jpg" alt=""></figure>
									<p class="bot-indent">Lorem ipsum dolor sit amet, consec tetuer adipiscing elit. Praesent vestibulum molestie lacus. Aenean nonummy hendrerit mauris. Phasellus porta. Fusce suscipit varius mi.</p>
									<a class="link" href="#">Read more</a>
								</div>
							</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="left-pad">
						<h3 class="indent-bot">free download &amp; play <strong>start today!</strong></h3>
						<figure class="img-pos indent-bot float-sw2"><img src="<?php echo base_url(); ?>images/page1-img2.jpg" alt=""></figure>
						<p class="bot-indent">Lorem ipsum dolor sit amet, consec tetuer adipiscing elit. Praesent vestibulum molestie lacus. Aenean nonummy hendrerit mauris. Phasellus porta. Fusce suscipit varius mi.</p>
						<a class="link" href="#">Read more</a>
					</div>
				</div>
				<div class="col-md-4">	
					<div class="left-pad top-pad" style = "margin-right: 30px;">
						<div class="wrapper">
							<a href="#"><img class = "home" src="<?php echo base_url(); ?>images/banner-1.jpg" alt=""></a>
							<a href="#"><img class = "home" src="<?php echo base_url(); ?>images/banner-2.jpg" alt=""></a>
						</div>
						<a class="link" href="#">More propositions</a>
						</div>
					</div>
			    </div>
			<div class="row">
				<div class="col-xs-12 col-md-8">
					<div class="wrapper">
						<div class="content-menu">
							<?php echo get_footer_menu($footer_menu_inside); ?>
							<div class="clear"></div>
					  </div>
				   </div>
				</div>
			</div>	
		</div>
    </div>
</section>