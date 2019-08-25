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
						   <div class="col-lg-2">
							     <button class="btn btn-dark" data-toggle="modal" data-target="#sem-login" style = "margin-top:15px;"/>Login</button>
							     <button class="btn btn-dark" data-toggle="modal" data-target="#sem-reg" style = "margin-top:15px;">Register</button>
						   </div>   
						  <div class="clear"></div>
			          </header>
			          </div>
			    </div><!--/.container-fluid -->
				<div class = "container-fluid">
			    	<section><?php $this->load->view('templates/'.$subview); ?></section>
				</div>
		    </div>
<!-- The Modal -->
<div class="modal fade seminor-login-modal" data-backdrop="static" id="sem-reg">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <!-- Modal body -->
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
    <div class="form-group">
      <?php $extra = array('type' => 'password', 'class' => 'form-control', 'id' => 'password', 'required autocomplete' => 'off'); 
			echo form_password('password','', $extra); 
			$extra = array('class' => 'form-control-placeholder', 'for' => 'password');
			echo form_label('Password', 'password', $extra); ?>
    </div>
    <div class="form-group">
       <?php $extra = array('type' => 'password', 'class' => 'form-control', 'id' => 'password_confirm', 'required autocomplete' => 'off'); 
			echo form_password('Confirm Password','', $extra); 
			$extra = array('class' => 'form-control-placeholder', 'for' => 'confirm_password');
			echo form_label('Confirm Password', 'confirm_password', $extra); ?>
    </div>
    <div class="form-group forgot-pass-fau text-center">
			<?php echo anchor('/member/terms-conditions', '<span class="text-primary-fau">By Clicking "REGISTER NOW" you accept our<br>
				Terms and Conditions</span>', 'class="text-secondary"'); ?>
		</div>
		  <!-- <div class = "text-primary"><h2 class = "Display-2"><div class = "js-reg-error"></div></h2></div>  -->
		  <div class="alert alert-success" style="display: none;"></div>
    	  <div class="alert alert-danger alert-errors" style="display: none;"></div>
    <div class="btn-check-log">
          <?php $extra = array('class' => 'btn-check-login');
			   echo form_submit('register', 'REGISTER NOW', $extra); ?>
      </div>
      <div class="create-new-fau text-center pt-3">
      <a href="#" class="text-primary-fau"><span data-toggle="modal" data-target="#sem-login" data-dismiss="modal">Already Have An Account</span></a>
	  </div>
	<input type="hidden" name="_token" id="login_token" class="token-field" value="<?php echo $this->session->set_userdata('token') ? $this->session->set_userdata("token") : ""; ?>" />
     <?php echo form_close(); ?> <!-- </form> -->
      </div>
    </div>
  </div>
</div>
  <!-- The Modal -->
 <div class="modal fade seminor-login-modal" data-backdrop="static" id="sem-login">
   <div class="modal-dialog modal-dialog-centered">
     <div class="modal-content">
        <!-- Modal body -->
        <div class="modal-body seminor-login-modal-body">
         <h5 class="modal-title text-center">LOGIN TO MY ACCOUNT</h5>
          <button type="button" class="close" data-dismiss="modal">
              <span><i class="fa fa-times-circle" aria-hidden="true"></i></span>
          </button>
      <?php echo form_open('#', 'class="seminor-login-form" id ="sem_login"'); ?>
		<div class="form-group">
        <?php $extra = array('class' => 'form-control', 'id' => 'log_username', 'required autocomplete' => 'off');  
			echo form_input('username','', $extra); 
			$extra = array('class' => 'form-control-placeholder', 'for' => 'username');
			echo form_label('Username', 'username', $extra); ?>
      </div>
      <div class="form-group">
        <?php $extra = array('class' => 'form-control', 'id' => 'log_password', 'required autocomplete' => 'off');  
	 	  echo form_password('password','', $extra);  
		  $extra = array('class' => 'form-control-placeholder', 'for' => 'username');
		  echo form_label('Password', 'password', $extra); ?>
      </div>
      <div class="form-group">
         <label class="container-checkbox">
        		Remember Me On This Computer
			  <?php echo form_checkbox('remember_me', 'rememberme', TRUE, 'required=""'); ?>
			  <span class="checkmark-box"></span>
		  </label>
        </div>
		<!-- <div class = "text-primary"><h2 class = "Display-2"><div class = "js-login-error"></div></h2></div>  -->
		<div class="alert alert-danger alert-errors" style="display: none;"></div>
		<div class="btn-check-log">
			<?php $extra = array('class' => 'btn-check-login', 'id' => 'login');
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
    <!-- The Modal -->
 <div class="modal fade seminor-login-modal" data-backdrop="static" id="sem-forgotpassword">
   <div class="modal-dialog modal-dialog-centered">
     <div class="modal-content">
        <!-- Modal body -->
        <div class="modal-body seminor-login-modal-body">
			<h5 class="modal-title text-center">FORGOT PASSWORD</h5>
			<button type="button" class="close" data-dismiss="modal">
				<span><i class="fa fa-times-circle" aria-hidden="true"></i></span>
			</button>
			<?php echo form_open('member/forgotpassword', 'class="seminor-forgotpassword-form" id ="forgot_password"'); ?>
			<div class="form-group">
			<?php $extra = array('class' => 'form-control', 'required autocomplete' => 'off');  
				echo form_input('forgotpassword','', $extra); 
				$extra = array('class' => 'form-control-placeholder', 'for' => 'forgotpassword');
				echo form_label('Email Address', 'forgotpassword', $extra); ?>
			</div>
        <div class = "text-primary"><h2 class = "Display-2"><div class = "js-forgotpassword-error"></div></h2></div>  
		<div class="btn-check-log">
			<?php $extra = array('class' => 'btn-check-login', 'id' => 'forgotpassword');
			   echo form_submit('forgotpassword', 'RESET PASSWORD', $extra); ?>
        </div>
		<input type="hidden" name="_token" id="signup_token" class="token-field" value="<?php echo $this->session->set_userdata('token') ? $this->session->set_userdata("token") : ""; ?>" />
		<div class="create-new-fau text-center pt-3">
       <a href="#" class="text-primary-fau"><span data-toggle="modal" data-target="#sem-login" data-dismiss="modal">Back to Sign In</span></a></div>
      		<?php echo form_close(); ?> <!-- </form> -->
        </div>
	  </div>
    </div>
  </div>
 <script>
/* $('#sem_login').on("submit", function(event) {
	event.defaultPrevented;
	var _form = $(this);
	var _error = $(".js-login-error", _form);
	var dataObj = {
		username: $("input[id='log_username']", _form).val(),
		password: $("input[id='log_password']", _form).val(),
	} 
	if (!dataObj.username || !dataObj.password) {
		_error
			.text("No Field(s) can be left blank.")
			.show();
		return false;
	}
	_error.hide(); 
	
	 $('#sem_login').ajax({
		type: 'POST',
		url: '<?php // echo site_url(); ?>member/login',
		data: dataObj,
		dataType: 'json',
	}) 
	
	$('#sem_login').done(function (data) {
		  // whatever the data is
		console.log(data);
		if (data.redirect !== undefined) {
			window.location = data.redirect;
		} 
	})
    
	$('#sem_login').fail(function (e) {
		// This failed
		console.log(e);
	})
	
	$('#sem_login').always(function (data) {
		// This will alway do
		console.log('Always');
	})
	return false;
})
$('#sem_reg').on("submit", function(event) {
	event.defaultPrevented;
	var _form = $(this);
	var _error = $(".js-reg-error", _form);
	var dataObj = {
		username: $("input[id='username']", _form).val(),
		email: $("input[id='email']", _form).val(),
		password: $("input[id='password']", _form).val(),
		password_confirm: $("input[id='password_confirm']", _form).val()
	} 
	if (!dataObj.username || !dataObj.email || !dataObj.password || !dataObj.password_confirm) {
		_error
			.text("No Field(s) can be left blank.")
			.show();
		return false;
	} else if (dataObj.email.length < 16) {
		_error
			.text("Please enter a longer email address.")
			.show();
		return false;
	} else if (dataObj.password.length < 8) {
		_error
			.text("Please enter a passphrase at least 8 characters for the password.")
			.show();
		return false;
	} else if (dataObj.password != dataObj.password_confirm) {
		_error
		.text("Password and the confirmed password don't match.")
		.show();
		return false;
	}
	_error.hide(); 
	
	 $('#sem_reg').ajax({
		type: 'POST',
		url: '<?php // echo site_url(); ?>member/register',
		data: dataObj,
		dataType: 'json',
		async: true,
	}) 
	$('#sem_reg').done(function ajaxDone(data) {
		  // whatever the data is
		console.log(data);
		if (data.redirect !== undefined) {
			window.location = data.redirect;
		} 
	})
 	$('#sem_reg').fail(function ajaxFailed(e) {
		// This failed
		console.log(e);
	})
	
	$('#sem_reg').always(function ajaxAlwaysDoThis(data) {
		// This will alway do
		console.log('Always');
	})
	return false;
}) */

$(document).ready(function() {

/* $("#sign-in-link").click(function() {
	$("#sign-up-form-container").hide();
	$("#sign-in-form-container").fadeIn(500);
});

$("#sign-up-link").click(function() {
	$("#sign-in-form-container").hide();
	$("#sign-up-form-container").fadeIn(500);
}); */

$("#sem-login").submit(function(e) {
	e.preventDefault();
	$.post("member/login", { formData:$(this).serialize() }, function(data) {
		console.log(data);
		if(data.success === true) {
			window.location.reload();
		} else {
			errors_data = '<ul>';
			for(var i = 0; i<data.errors.length; i++)
			{
				errors_data += '<li>' + data.errors[i] + '</li>';
			}
			errors_data += '</ul>';
			$("#sem-login .alert-errors").html(errors_data).hide().fadeIn(1000);
		}
	}, 'json');
});

$("#register_form").submit(function(e) {
	e.preventDefault();
	$.post("member/register", { formData:$(this).serialize() }, function(data) {
		console.log(data);
		if(data.success === true) {
			$("#register_form .alert-errors").html("").hide();
			$("#register_form .alert-success").html("Successful registration. Redirecting to dashboard in 3 seconds. Please wait...").fadeIn(1000);
			setTimeout(function() {
				window.location.reload();
			}, 3000);
		}
		else {
			errors_data = '<ul>';
			for(var i = 0; i<data.errors.length; i++)
			{
				errors_data += '<li>' + data.errors[i] + '</li>';
			}
			errors_data += '</ul>';
			$("#register_form .alert-errors").html(errors_data).hide().fadeIn(1000);
		}
	}, 'json');
});

});
</script>

<?php $this->load->view('components/page_tail'); ?>