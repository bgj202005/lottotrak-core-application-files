<!--  Main Content -->
<section id="content">
    <div class="content-inner  col-centered">
	   <div class="row">
            <div class="col-xs-12 col-sm-6 col-md-8">
				<div class="row" style = "text-align:center;">	
					<?php if (!$verified) 
					{ ?>
						<h1 class="bg-danger" style = "color:#FFFFFF">The Link Could Not Be Validated Or Is Expired.</h1>
						<div class="row">
						<div class="col-xs-10 col-md-8"></div></div></div>
					<?php } 
					else 
					{ ?> <!--  if ($verified=='active') -->
						<h3 class="bg-default" style = "color:#FFFFFF">Enter a new password and type the password in again to confirm it is correct</h3>	
						<?php echo validation_errors('<div class="alert alert-warning" role="alert">', '</div>'); 
						echo $this->session->flashdata('error'); ?>
						
				</div>
					<div class="row">
						<div class="col-xs-10 col-md-8">
						<?php $hidden = array('id' => $id);
							echo form_hidden('verified', $verified); 
							echo form_open(base_url().'member/update_password', 'class="form-horizontal"', $hidden); 
							if (isset($email_hash, $email_code)) { 
								echo form_hidden('email_hash', $email_hash); 
								echo form_hidden('email_code', $email_code);
							}
							if (!isset($email)) $email = '';
							echo form_hidden('email', $email);
							?>
							<!-- Password Field -->
							<div class="form-group form-group-lg row"> 
                    			<?php $extra = array('class' => 'col-sm-2 control-label');
                    			echo form_label('Password', 'password', $extra); ?>
                    			<div class="col-sm-10">
								<?php $extra = array('class' => 'form-control', 'id' => 'formGroupInputLarge',
								   'maxlength' => '50', 'size' => '50', 'style'=> 'width:100%');
									echo form_password('password',set_value('password', ''), $extra); 
									echo form_error('password', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>'); ?>
								</div>
							</div>
							<!-- Confirm Password -->
							<div class="form-group form-group-lg row"> 
                    			<?php $extra = array('class' => 'col-sm-2 control-label');
                    			echo form_label('Confirm Password', 'confirm_password', $extra); ?>
                    			<div class="col-sm-10">
								<?php $extra = array('class' => 'form-control', 'id' => 'formGroupInputLarge',
								   'maxlength' => '50', 'size' => '50', 'style'=> 'width:100%');
									echo form_password('confirm_password',set_value('confirm_password', ''), $extra); 
									echo form_error('confirm_password', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>'); ?>
								</div>
							</div>
							<div style = "text-align: center;">
								<?php echo form_submit('submit', 'Update Password', 'class="btn btn-primary btn-lg btn-info"');
								echo form_close(); ?> <!-- </form> -->
							</div>
						</div>
					</div>
				</div>
				<?php } ?> <!-- if ($verified=='expired') -->
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
</section>