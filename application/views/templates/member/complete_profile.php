<!-- Bootstrap Form Helpers CSS -->
<link rel="stylesheet" href="<?php echo base_url(); ?>css/bootstrap-formhelpers.min.css">

<!--  Main Content -->
<section id="content">
    <div class="content-inner col-centered">
        <div class="row">
            <div class="col-xs-12 col-md-8">
                <div class="row">    
                    <h1 style="color: #333333;">Complete Your Profile</h1>
                    <h2 style="color: #555555;">Please select your location and preferred lotteries to activate your account</h2>    
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-success">
                            <h4><i class="fa fa-check"></i> Email Validated</h4>
                            <p><strong>✓ Username:</strong> <?php echo htmlspecialchars($member_username); ?></p>
                            <p><strong>✓ Email:</strong> <?php echo htmlspecialchars($member_email); ?> (Validated)</p>
                        </div>

                        <div class="alert alert-warning">
                            <h4><i class="fa fa-exclamation-triangle"></i> Final Step - Complete Your Profile</h4>
                            <p>Your account will be <strong>activated</strong> after selecting your location and lottery preferences.</p>
                        </div>

                        <!-- Security Information Section -->
                        <div class="panel panel-info" style="margin-bottom: 20px;">
                            <div class="panel-header" style="padding: 12px 15px; background: #d9edf7; border-bottom: 1px solid #bce8f1;">
                                <h4 style="margin: 0; color: #31708f;"><i class="fa fa-shield"></i> Security Information</h4>
                            </div>
                            <div class="panel-body" style="padding: 15px; background: #f4f8fa;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Your IP Address:</strong> 
                                        <span style="font-family: monospace; color: #c7254e; background: #f9f2f4; padding: 2px 6px; border-radius: 3px;">
                                            <?php echo isset($user_location['ip']) ? $user_location['ip'] : 'Unknown'; ?>
                                        </span>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Detected Location:</strong> 
                                        <span style="color: #31708f;">
                                            <?php 
                                            if (isset($user_location)) {
                                                echo $user_location['city'] . ', ' . $user_location['region'] . ', ' . $user_location['country'];
                                            } else {
                                                echo 'Unknown';
                                            }
                                            ?>
                                        </span>
                                    </div>
                                </div>
                                <div style="margin-top: 10px; font-size: 12px; color: #8a6d3b;">
                                    <i class="fa fa-info-circle"></i> This information is recorded for security purposes and account verification.
                                </div>
                            </div>
                        </div>

                        <!-- Profile Completion Form -->
                        <form id="profile_form" method="post" action="<?php echo site_url('member/process_profile'); ?>">
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="first_name"><strong>First Name *</strong></label>
                                        <input type="text" class="form-control" id="first_name" name="first_name" required placeholder="Enter your first name">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="last_name"><strong>Last Name *</strong></label>
                                        <input type="text" class="form-control" id="last_name" name="last_name" required placeholder="Enter your last name">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="city"><strong>City *</strong></label>
                                <input type="text" class="form-control" id="city" name="city" required placeholder="Enter your city">
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password"><strong>Password *</strong></label>
                                        <input type="password" class="form-control" id="password" name="password" required placeholder="Create a password" minlength="6">
                                        <small class="help-block">Minimum 6 characters</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="confirm_password"><strong>Confirm Password *</strong></label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required placeholder="Confirm your password">
                                    </div>
                                </div>
                            </div>

                            <hr style="margin: 30px 0;">
                            <h4 style="color: #000000 !important; font-weight: bold !important; margin-bottom: 20px;"><i class="fa fa-map-marker"></i> Location & Lottery Preferences</h4>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="bfh-country"><strong>Country *</strong></label>
                                        <select class="form-control bfh-countries" id="bfh-country" name="country_id" data-country="" data-flags="true" required>
                                            <option value="">Select Country</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="bfh-state"><strong>Province/State *</strong></label>
                                        <select class="form-control bfh-states" id="bfh-state" name="state_province" data-country="bfh-country" required>
                                            <option value="">Select Country First</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label><strong>Available Lotteries *</strong></label>
                                <div id="lottery_selection" class="well" style="min-height: 120px; background: #f9f9f9; font-size: 16px; line-height: 1.6;">
                                    <p class="text-muted" style="font-size: 14px;">Please select country first</p>
                                </div>
                                <small class="help-block">Select at least one lottery to activate your account</small>
                            </div>

                            <div id="validation_error" class="d-none"></div>
                            <div id="validation_success_message" class="d-none"></div>

                            <div class="form-group text-center">
                                <button type="submit" id="profile_button" class="btn btn-success btn-lg">
                                    <i class="fa fa-check"></i> Complete Profile & Activate Account
                                </button>
                            </div>
                        </form>
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

<script>
$(document).ready(function() {
    
    // Bootstrap Form Helpers country selection change
    $('#bfh-country').on('change', function() {
        var country_code = $(this).val();
        
        if (country_code) {
            $('#lottery_selection').html('<p class="text-muted">Loading lotteries...</p>');
            
            // Load country lotteries immediately (without state filter)
            loadLotteries(country_code, '');
        } else {
            $('#lottery_selection').html('<p class="text-muted">Please select a country first</p>');
        }
    });

    // Bootstrap Form Helpers state/province selection change
    $('#bfh-state').on('change', function() {
        var country_code = $('#bfh-country').val();
        var state_code = $(this).val();
        
        if (country_code) {
            loadLotteries(country_code, state_code);
        }
    });

    // Function to load lotteries based on country and optional province
    function loadLotteries(country_id, province_code) {
        $('#lottery_selection').html('<p class="text-muted">Loading lotteries...</p>');
        
        $.post('<?php echo site_url("member/get_lotteries_by_location"); ?>', {
            country_id: country_id,
            province_code: province_code || '',
            '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
        }, function(data) {
            var response = JSON.parse(data);
            var html = '';
            
            if (response.length > 0) {
                var countryLotteries = [];
                var provinceLotteries = [];
                
                // Separate country and province lotteries
                $.each(response, function(index, lottery) {
                    if (lottery.lottery_type === 'country') {
                        countryLotteries.push(lottery);
                    } else {
                        provinceLotteries.push(lottery);
                    }
                });
                
                // Display country lotteries first
                if (countryLotteries.length > 0) {
                    html += '<h4 style="color: #333; margin-bottom: 15px; font-size: 16px;">National Lotteries:</h4>';
                    $.each(countryLotteries, function(index, lottery) {
                        html += '<div class="checkbox" style="margin-bottom: 12px;">';
                        html += '<label style="font-size: 16px; font-weight: 500; cursor: pointer;">';
                        html += '<input type="checkbox" class="lottery-checkbox" name="lottery_ids[]" value="' + lottery.id + '" style="margin-right: 8px;"> ';
                        html += lottery.lottery_name;
                        html += '</label>';
                        html += '</div>';
                    });
                }
                
                // Display province lotteries if any
                if (provinceLotteries.length > 0) {
                    html += '<h4 style="color: #333; margin: 20px 0 15px 0; font-size: 16px;">Regional Lotteries:</h4>';
                    $.each(provinceLotteries, function(index, lottery) {
                        html += '<div class="checkbox" style="margin-bottom: 12px;">';
                        html += '<label style="font-size: 16px; font-weight: 500; cursor: pointer;">';
                        html += '<input type="checkbox" class="lottery-checkbox" name="lottery_ids[]" value="' + lottery.id + '" style="margin-right: 8px;"> ';
                        html += lottery.lottery_name;
                        html += '</label>';
                        html += '</div>';
                    });
                }
                
                // Add lottery selection info
                html += '<div class="alert alert-info" style="margin-top: 20px; font-size: 14px;">';
                html += '<i class="fa fa-info-circle"></i> You can select up to <strong>5 lotteries</strong>. Selected lotteries: <span id="lottery-count">0</span>/5';
                html += '</div>';
            } else {
                html = '<p class="text-muted" style="font-size: 15px;">Please select your state/province to see available lotteries.</p>';
            }
            
            $('#lottery_selection').html(html);
            
            // Add lottery selection limit handler
            setupLotteryLimitHandler();
        }).fail(function() {
            $('#lottery_selection').html('<p class="text-danger"><i class="fa fa-exclamation-circle"></i> Error loading lotteries. Please try again.</p>');
        });
    }
    
    // Function to handle lottery selection limits
    function setupLotteryLimitHandler() {
        $(document).off('change', '.lottery-checkbox').on('change', '.lottery-checkbox', function() {
            var selectedCount = $('.lottery-checkbox:checked').length;
            $('#lottery-count').text(selectedCount);
            
            if (selectedCount >= 5) {
                // Disable unchecked checkboxes when limit reached
                $('.lottery-checkbox:not(:checked)').prop('disabled', true);
                $('#lottery-selection .alert-info').removeClass('alert-info').addClass('alert-warning')
                    .html('<i class="fa fa-exclamation-triangle"></i> <strong>Maximum reached!</strong> You have selected the maximum of 5 lotteries. Uncheck some to select different ones.');
            } else {
                // Re-enable all checkboxes when under limit
                $('.lottery-checkbox').prop('disabled', false);
                $('#lottery-selection .alert-warning').removeClass('alert-warning').addClass('alert-info')
                    .html('<i class="fa fa-info-circle"></i> You can select up to <strong>5 lotteries</strong>. Selected lotteries: <span id="lottery-count">' + selectedCount + '</span>/5');
            }
        });
    }

    // Password confirmation validation
    $('#confirm_password').on('keyup blur', function() {
        var password = $('#password').val();
        var confirmPassword = $(this).val();
        
        if (confirmPassword && password !== confirmPassword) {
            $(this).addClass('error');
            if (!$('.password-error').length) {
                $(this).after('<small class="password-error text-danger">Passwords do not match</small>');
            }
        } else {
            $(this).removeClass('error');
            $('.password-error').remove();
        }
    });

    // Form submission
    $('#profile_form').submit(function(e) {
        e.preventDefault();
        
        // Check password confirmation
        var password = $('#password').val();
        var confirmPassword = $('#confirm_password').val();
        if (password !== confirmPassword) {
            $('#validation_error').html('<div class="alert alert-danger">Passwords do not match.</div>').removeClass('d-none');
            return false;
        }
        
        // Check BFH form validation
        var country = $('#bfh-country').val();
        var state = $('#bfh-state').val();
        
        if (!country) {
            $('#validation_error').html('<div class="alert alert-danger">Please select your country.</div>').removeClass('d-none');
            return false;
        }
        
        if (!state) {
            $('#validation_error').html('<div class="alert alert-danger">Please select your state/province.</div>').removeClass('d-none');
            return false;
        }
        
        // Check if at least one lottery is selected
        var selectedLotteries = $('input[name="lottery_ids[]"]:checked').length;
        if (selectedLotteries === 0) {
            $('#validation_error').html('<div class="alert alert-danger">Please select at least one lottery to continue.</div>').removeClass('d-none');
            return false;
        }
        
        // Check if more than 5 lotteries are selected
        if (selectedLotteries > 5) {
            $('#validation_error').html('<div class="alert alert-danger">You can select maximum 5 lotteries. Please uncheck some selections.</div>').removeClass('d-none');
            return false;
        }
        
        $('#profile_button').attr('disabled', true).text('Processing...');
        $('#validation_error').addClass('d-none');
        $('#validation_success_message').addClass('d-none');
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(data) {
                if (data.error) {
                    $('#validation_error').html(data.validation_error).removeClass('d-none');
                } else if (data.success) {
                    $('#validation_success_message').html(data.success).removeClass('d-none');
                    $('#validation_error').addClass('d-none');
                    
                    if (data.redirect_url) {
                        setTimeout(function() {
                            window.location.href = data.redirect_url;
                        }, 2000);
                    }
                }
            },
            complete: function() {
                $('#profile_button').attr('disabled', false).html('<i class="fa fa-check"></i> Complete Profile & Activate Account');
            }
        });
    });

    // Helper function to get province names
    function getProvinceName(code) {
        var provinces = {
            'AB': 'Alberta', 'BC': 'British Columbia', 'MB': 'Manitoba', 'NB': 'New Brunswick',
            'NL': 'Newfoundland and Labrador', 'NS': 'Nova Scotia', 'ON': 'Ontario', 
            'PE': 'Prince Edward Island', 'QC': 'Quebec', 'SK': 'Saskatchewan',
            'NT': 'Northwest Territories', 'NU': 'Nunavut', 'YT': 'Yukon',
            'AL': 'Alabama', 'AK': 'Alaska', 'AZ': 'Arizona', 'AR': 'Arkansas', 'CA': 'California',
            'CO': 'Colorado', 'CT': 'Connecticut', 'DE': 'Delaware', 'FL': 'Florida', 'GA': 'Georgia',
            'HI': 'Hawaii', 'ID': 'Idaho', 'IL': 'Illinois', 'IN': 'Indiana', 'IA': 'Iowa',
            'KS': 'Kansas', 'KY': 'Kentucky', 'LA': 'Louisiana', 'ME': 'Maine', 'MD': 'Maryland',
            'MA': 'Massachusetts', 'MI': 'Michigan', 'MN': 'Minnesota', 'MS': 'Mississippi',
            'MO': 'Missouri', 'MT': 'Montana', 'NE': 'Nebraska', 'NV': 'Nevada', 'NH': 'New Hampshire',
            'NJ': 'New Jersey', 'NM': 'New Mexico', 'NY': 'New York', 'NC': 'North Carolina',
            'ND': 'North Dakota', 'OH': 'Ohio', 'OK': 'Oklahoma', 'OR': 'Oregon', 'PA': 'Pennsylvania',
            'RI': 'Rhode Island', 'SC': 'South Carolina', 'SD': 'South Dakota', 'TN': 'Tennessee',
            'TX': 'Texas', 'UT': 'Utah', 'VT': 'Vermont', 'VA': 'Virginia', 'WA': 'Washington',
            'WV': 'West Virginia', 'WI': 'Wisconsin', 'WY': 'Wyoming'
        };
        return provinces[code] || code;
    }
});
</script>

<!-- Bootstrap Form Helpers JavaScript -->
<script src="<?php echo base_url(); ?>js/bootstrap-formhelpers.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Bootstrap Form Helpers
    $('.bfh-countries').bfhcountries();
    $('.bfh-states').bfhstates();
    
    // Set default country to Canada if desired
    // $('#bfh-country').val('CA').trigger('change');
});
</script>