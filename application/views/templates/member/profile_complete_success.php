<!--  Main Content -->
<section id="content">
    <div class="content-inner col-centered">
        <div class="row">
            <div class="col-xs-12 col-md-8">
                <div class="row">    
                    <h1 style="color: #333333;">Account Activated!</h1>
                    <h2 style="color: #555555;">Your profile has been completed and account is now active</h2>    
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-success">
                            <h4><i class="fa fa-check-circle"></i> Registration Complete</h4>
                            <?php if ($this->session->userdata('member_logged_in')): ?>
                                <p><strong>Congratulations <?php echo htmlspecialchars($this->session->userdata('member_first_name')); ?>!</strong> Your Lottotrak account has been successfully activated and you are now logged in.</p>
                            <?php else: ?>
                                <p><strong>Congratulations!</strong> Your Lottotrak account has been successfully activated.</p>
                            <?php endif; ?>
                        </div>

                        <div class="alert alert-info">
                            <h4><i class="fa fa-envelope"></i> Confirmation Email Sent</h4>
                            <p>A confirmation email has been sent to your registered email address with your account details.</p>
                        </div>

                        <div class="panel panel-default">
                            <div class="panel-header" style="padding: 15px; background: #f5f5f5; border-bottom: 1px solid #ddd;">
                                <h4><i class="fa fa-info-circle"></i> What's Next?</h4>
                            </div>
                            <div class="panel-body">
                                <ul class="list-unstyled" style="margin: 15px;">
                                    <li style="margin-bottom: 10px;"><i class="fa fa-check text-success"></i> <strong>Account Status:</strong> Fully activated and ready to use</li>
                                    <li style="margin-bottom: 10px;"><i class="fa fa-check text-success"></i> <strong>Profile:</strong> Location and lottery preferences saved</li>
                                    <?php if ($this->session->userdata('member_logged_in')): ?>
                                        <li style="margin-bottom: 10px;"><i class="fa fa-check text-success"></i> <strong>Login Status:</strong> You are now logged in and can access all features</li>
                                    <?php else: ?>
                                        <li style="margin-bottom: 10px;"><i class="fa fa-exclamation-triangle text-warning"></i> <strong>Next Step:</strong> Please log in to access all features</li>
                                    <?php endif; ?>
                                    <li style="margin-bottom: 10px;"><i class="fa fa-star text-warning"></i> <strong>Predictions:</strong> Lottery predictions available for your selected games</li>
                                </ul>
                            </div>
                        </div>

                        <div class="text-center" style="margin: 30px 0;">
                            <a href="<?php echo site_url('home'); ?>" class="btn btn-primary btn-lg" style="margin-right: 15px;">
                                <i class="fa fa-home"></i> Go to Homepage
                            </a>
                            <?php if ($this->session->userdata('member_logged_in')): ?>
                                <a href="<?php echo site_url('member/dashboard'); ?>" class="btn btn-success btn-lg">
                                    <i class="fa fa-user"></i> View My Dashboard
                                </a>
                            <?php else: ?>
                                <a href="<?php echo site_url('home'); ?>#login" class="btn btn-warning btn-lg">
                                    <i class="fa fa-sign-in"></i> Login Now
                                </a>
                            <?php endif; ?>
                        </div>

                        <div class="text-center">
                            <p class="text-muted">
                                <small>
                                    Need help? <a href="<?php echo site_url('help'); ?>">Visit our Help Center</a> or 
                                    <a href="<?php echo site_url('contact'); ?>">contact support</a>.
                                </small>
                            </p>
                        </div>
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

<!-- Auto-login functionality handled in controller -->