<?php $this->load->view('components/page_head'); ?>
<body id = "page1">
	 <div class="bg">
			 <div class="container-fluid">
			      <!-- Static navbar -->
			      <!--  <nav class="navbar navbar-default"> -->
			    <header> 
					   <div class="row">
					   		<div class="col-lg-5">
								<section>
									<?php echo anchor('', strtoupper(config_item('site_name')), 'class="navbar-brand"'); ?>
								</section>
							</div> <!--  class="col-lg-6" -->
							<div class="col-lg-5">
						   		<nav>
									<?php echo get_menu($menu); ?>
								</nav>							  
						   </div>
						   
               <?php if ($this->session->userdata('logged_in')) 
              { ?>
              <div class = "col-2">
                <div id="login_container">
                  <span>Welcome <?=$this->session->userdata('first_name'); ?></span> 
                  <div class = "sm sm-clean"><?php echo anchor('member/logout', '<i class="fa fa-power-off"></i> logout', 'style="float: center" class ="btn btn-default btn-sm"')?></div>
                </div>
              </div> 
        <?php }     
              else
              { ?>
               <div class="col-lg-2">     
                   <button class="btn btn-dark" data-toggle="modal" data-target="#sem-login" style = "margin-top:15px;"/>Login</button>
                   <button class="btn btn-dark" data-toggle="modal" data-target="#sem-reg" style = "margin-top:15px;">Register</button>
               </div>   
        <?php  } ?>
						  <div class="clear"></div>
			    </header>
			  </div>
			</div><!--/.container-fluid -->
				<div class = "container-fluid">
			    	<section><?php $this->load->view('templates/'.$subview); ?></section>
				</div>
          <!-- The Modal -->
          <div class="modal fade seminor-login-modal" data-backdrop="static" id="sem-reg">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <!-- The Register Accound Modal -->
                <div class="modal-body seminor-login-modal-body">
                <h5 class="modal-title text-center">REGISTER</h5>
                  <button type="button" class="close" data-dismiss="modal">
                      <span><i class="fa fa-times-circle" aria-hidden="true"></i></span>
                  </button>
            <?php echo form_open('', 'class="seminor-login-form" id="register_form"');  ?>
              <div class="form-group">
                <?php $extra = array('type' => 'text', 'class' => 'form-control', 'id' => 'username', 'required autocomplete' => 'off'); 
                echo form_input('username','', $extra); 
                $extra = array('class' => 'form-control-placeholder', 'for' => 'username');
                echo form_label('User name', 'username', $extra); ?>
              </div>
              <div class="form-group">
                <?php $extra = array('type' => 'email', 'class' => 'form-control', 'id' => 'email', 'required autocomplete' => 'off'); 
                echo form_input('email','', $extra); 
                $extra = array('class' => 'form-control-placeholder', 'for' => 'email');
                echo form_label('Email', 'email', $extra); ?>
              </div>
              <div class="form-group forgot-pass-fau text-center">
                <?php echo anchor('/member/terms-conditions', '<span class="text-primary-fau">By Clicking "REGISTER NOW" you accept our<br>
                  Terms and Conditions</span>', 'class="text-secondary"'); ?>
              </div>
              
                <span id="validation_success_message"></span>
              
                <span id="validation_error" class="text-danger"></span>

              <div class="btn-check-log">
                    <?php $extra = array('class' => 'btn-check-login', 'id' => 'register_button');
                  echo form_submit('register', 'REGISTER NOW', $extra); ?>
                </div>
                <div class="create-new-fau text-center pt-3">
                <a href="#" class="text-primary-fau"><span data-toggle="modal" data-target="#sem-login" data-dismiss="modal">Already Have An Account</span></a>
              </div>
              <?php echo form_close(); ?> <!-- </form> -->
                </div>
              </div>
            </div>
          </div>
            <!-- The Login Modal -->
          <div class="modal fade seminor-login-modal" data-backdrop="static" id="sem-login">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                  <!-- Modal body -->
                  <div class="modal-body seminor-login-modal-body">
                  <h5 class="modal-title text-center">LOGIN TO MY ACCOUNT</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span><i class="fa fa-times-circle" aria-hidden="true"></i></span>
                    </button>
                <?php echo form_open('', 'class="seminor-login-form" id ="sem_login"'); ?>
              <div class="form-group">
                  <?php $extra = array('class' => 'form-control', 'id' => 'username_login', 'required autocomplete' => 'off');  
                echo form_input('username_login','', $extra); 
                $extra = array('class' => 'form-control-placeholder', 'for' => 'username');
                echo form_label('Username', 'username', $extra); ?>
                </div>
                <div class="form-group">
                  <?php $extra = array('class' => 'form-control', 'id' => 'password_login', 'required autocomplete' => 'off');  
                echo form_password('password_login','', $extra);  
                $extra = array('class' => 'form-control-placeholder', 'for' => 'password');
                echo form_label('Password', 'password', $extra); ?>
                </div>
                <div class="form-group">
                  <label class="container-checkbox">
                      Remember Me On This Computer
                    <?php echo form_checkbox('remember_me', 'rememberme', TRUE); ?>
                    <span class="checkmark-box"></span>
                  </label>
                </div>
              <span id="login_error_message"></span>
              <span id="login_success_message"></span>
              <div class="btn-check-log">
                <?php $extra = array('class' => 'btn-check-login', 'id' => 'login_button');
                  echo form_submit('login', 'LOGIN', $extra); ?>
                  </div>
                <div class="forgot-pass-fau text-center pt-3">
              <a href="#" class="text-primary-fau"><span data-toggle="modal" data-target="#sem-forgotpassword" data-dismiss="modal">Forgot Password</span></a>
                </div>
                <div class="create-new-fau text-center pt-3">
                <a href="#" class="text-primary-fau"><span data-toggle="modal" data-target="#sem-reg" data-dismiss="modal">Create A New Account</span></a></div>
                <?php echo form_close(); ?> <!-- </form> -->
                  </div>
                </div>
              </div>
            </div>
              <!-- The Forgot Password Modal -->
          <div class="modal fade seminor-login-modal" data-backdrop="static" id="sem-forgotpassword">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                  <!-- Modal body -->
                  <div class="modal-body seminor-login-modal-body">
                <h5 class="modal-title text-center">FORGOT PASSWORD</h5>
                <button type="button" class="close" data-dismiss="modal">
                  <span><i class="fa fa-times-circle" aria-hidden="true"></i></span>
                </button>
                <?php echo form_open('', 'class="seminor-forgotpassword-form" id ="forgot_password"'); ?>
                <div class="form-group">
                <?php $extra = array('class' => 'form-control', 'required autocomplete' => 'off');  
                  echo form_input('email_forgot','', $extra); 
                  $extra = array('class' => 'form-control-placeholder', 'for' => 'forgotpassword');
                  echo form_label('Email Address', 'forgotpassword', $extra); ?>
                </div>
                <span id="forgot_error_message"></span>
                <span id="forgot_success_message"></span>
                <div class="btn-check-log">
                <?php $extra = array('class' => 'btn-check-login', 'id' => 'forgotpassword');
                  echo form_submit('forgotpassword', 'RESET PASSWORD', $extra); ?>
                  </div>
              <div class="create-new-fau text-center pt-3">
                <a href="#" class="text-primary-fau"><span data-toggle="modal" data-target="#sem-login" data-dismiss="modal">Back to Sign In</span></a></div>
                    <?php echo form_close(); ?> <!-- </form> -->
                  </div>
              </div>
              </div>
            </div>
      <?php $this->load->view('components/page_tail'); ?>
 <script>
$(document).ready(function() {
	$('#register_form').on('submit', function(event){
  event.preventDefault();
  $.ajax({
   url:"<?php echo base_url(); ?>member/register",
   method:"POST",
   data:$(this).serialize(),
   dataType:"json",
   beforeSend:function(){
    $('#login').attr('disabled', 'disabled');
   },
   success:function(data)
   {
    console.log(data);
	if (data.error)
    {
     if (data.validation_error != '')
     {
      $('#validation_error').html(data.validation_error);
     }
     else
     {
      $('#validation_error').html('');
     }
    }
    if(data.success)
    {
	 $('#validation_success_message').html(data.success).fadeIn(1000);
    $('#validation_error').html('');
    window.location.href = "<?php echo base_url(); ?>member/validate_email";
     $('#register_form')[0].reset();
    }
    $('#register_button').attr('disabled', false);
   }
  })
 });
  $('#sem_login').on('submit', function(event){
  event.preventDefault();
   $.ajax({
   url:"<?php echo base_url(); ?>member/login",
   method:"POST",
   data:$(this).serialize(),
   dataType:"json",
   beforeSend:function(){
    $('#login').attr('disabled', 'disabled');
   },
   success:function(data)
   {
    console.log(data);
  	if (data.error)
    {
     if (data.login_error != '')
     {
      $('#login_error_message').html(data.validation_error);
     }
     else
     {
      $('#login_error_message').html('');
     }
    }
    if(data.success)
    {
     $('#login_success_message').html(data.success).fadeIn(1000);
     $('#login_error_message').html('');
      setTimeout(function() {
        window.location.href = "<?php echo base_url(); ?>member/dashboard";
        }, 3000);
     $('#sem_login')[0].reset();
    }
    $('#login_button').attr('disabled', false);
   }
  })
 });
 $('#forgot_password').on('submit', function(event){
  event.preventDefault();
   $.ajax({
   url:"<?php echo base_url(); ?>member/forgotpassword",
   method:"POST",
   data:$(this).serialize(),
   dataType:"json",
   beforeSend:function(){
    $('#forgotpassword').attr('disabled', 'disabled');
   },
   success:function(data)
   {
    console.log(data);
  	if (data.error)
    {
     if (data.forgot_error_message != '')
     {
      $('#forgot_error_message').html(data.validation_error);
     }
     else
     {
      $('#forgot_error_message').html('');
     }
    }
    if(data.success)
    {
      $('#forgot_error_message').html('');
      setTimeout(function() {
        window.location.href = "<?php echo base_url(); ?>member/validate_forgotpassword";
        }, 100);
     $('#forgot_password')[0].reset();
    }
    $('#forgotpassword').attr('disabled', false);
   }
  })
 });
});
</script>