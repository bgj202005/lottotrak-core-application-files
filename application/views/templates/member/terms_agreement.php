<!--  Main Content -->
<section id="content">
    <div class="content-inner  col-centered">
	   <div class="row">
            <div class="col-xs-12 col-md-8">
			<div class="row">	
				<h1 style="color: #333333;">Terms and Conditions</h1>
				<h2 style="color: #555555;">Please read and accept the terms of service to complete your registration</h2>	
			</div>
			
			<div class="row">
				<div class="col-md-12">
					<div class="terms-content" style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; height: 300px; overflow-y: scroll; border: 1px solid #dee2e6;">
						<h3 style="color: #333333;">Terms of Service Agreement</h3>
						<p><strong>By using this website, you acknowledge and agree to the following terms:</strong></p>
						
						<h4 style="color: #444444;">1. Age Requirement</h4>
						<p>You must be of legal age (18 years or older in most jurisdictions) to use this website. By proceeding, you confirm that you meet the minimum age requirement in your jurisdiction.</p>
						
						<h4 style="color: #444444;">2. Disclaimer of Liability</h4>
						<p>You understand and accept that:</p>
						<ul>
							<li>This website provides lottery prediction services for entertainment purposes only</li>
							<li>No guarantee is made regarding the accuracy of predictions</li>
							<li>You accept full responsibility for any losses incurred while using this website</li>
							<li>The website owners, operators, and affiliates are not liable for any financial losses</li>
							<li>Lottery predictions are based on statistical analysis and do not guarantee winning results</li>
						</ul>
						
						<h4 style="color: #444444;">3. User Responsibilities</h4>
						<p>As a user, you agree to:</p>
						<ul>
							<li>Use the website responsibly and at your own risk</li>
							<li>Not hold the website liable for any gambling losses</li>
							<li>Comply with all local laws regarding gambling and lottery participation</li>
							<li>Provide accurate information during registration</li>
						</ul>
						
						<h4 style="color: #444444;">4. No Warranty</h4>
						<p>This service is provided "as is" without any warranties, express or implied. We make no representations about the suitability, reliability, or accuracy of the information provided.</p>
						
						<h4 style="color: #444444;">5. Acceptance</h4>
						<p>By clicking "I Agree" below, you acknowledge that you have read, understood, and agree to be bound by these terms and conditions.</p>
					</div>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-12 text-center" style="margin: 30px 0;">
					<?php if (isset($error_message)): ?>
						<div class="alert alert-danger"><?php echo $error_message; ?></div>
					<?php endif; ?>
					
					<div class="terms-buttons">
						<form method="post" action="<?php echo site_url('member/process_terms'); ?>" style="display: inline-block;">
							<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
							<input type="hidden" name="terms_response" value="agree" />
							<button type="submit" class="btn btn-success btn-lg" style="margin: 0 15px; min-width: 150px;">
								<i class="glyphicon glyphicon-ok"></i> I Agree
							</button>
						</form>
						
						<form method="post" action="<?php echo site_url('member/process_terms'); ?>" style="display: inline-block;">
							<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
							<input type="hidden" name="terms_response" value="decline" />
							<button type="submit" class="btn btn-danger btn-lg" style="margin: 0 15px; min-width: 150px;">
								<i class="glyphicon glyphicon-remove"></i> I Decline
							</button>
						</form>
					</div>
					
					<p class="text-muted" style="margin-top: 20px; font-size: 12px;">
						<strong>Note:</strong> You must agree to these terms to complete your account registration. 
						Declining will cancel your registration process.
					</p>
				</div>
			</div>
			</div>
			<!--  Sidebar -->
			<div class="col-xs-12 col-md-4 sidebar">
				<?php $this->load->view('sidebar'); ?>
			</div>
          </div>
	</div>
</section>