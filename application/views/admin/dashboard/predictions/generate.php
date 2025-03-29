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
	.progress {
    width: 150px;
    height: 150px !important;
    float: left;
    line-height: 150px;
    background: none;
    margin: 20px;
    box-shadow: none;
    position: relative;
}

.progress:after {
    content: "";
    width: 100%;
    height: 100%;
    border-radius: 50%;
    border: 12px solid #fff;
    position: absolute;
    top: 0;
    left: 0;
}

.progress>span {
    width: 50%;
    height: 100%;
    overflow: hidden;
    position: absolute;
    top: 0;
    z-index: 1;
}

.progress .progress-left {
    left: 0;
}

.progress .progress-bar {
    width: 100%;
    height: 100%;
    background: none;
    border-width: 12px;
    border-style: solid;
    position: absolute;
    top: 0
}

.progress .progress-left .progress-bar {
    left: 100%;
    border-top-right-radius: 80px;
    border-bottom-right-radius: 80px;
    border-left: 0;
    -webkit-transform-origin: center left;
    transform-origin: center left;
}

.progress .progress-right {
    right: 0;
}

.progress .progress-right .progress-bar {
    left: -100%;
    border-top-left-radius: 80px;
    border-bottom-left-radius: 80px;
    border-right: 0;
    -webkit-transform-origin: center right;
    transform-origin: center right;
    animation: loading-1 1.8s linear forwards;
}

.progress .progress-value {
    width: 90%;
    height: 90%;
    border-radius: 50%;
    background: #000;
    font-size: 24px;
    color: #fff;
    line-height: 135px;
    text-align: center;
    position: absolute;
    top: 5%;
    left: 5%;
}

.progress.blue .progress-bar {
    border-color: #049dff;
}

.progress.blue .progress-left .progress-bar {
    animation: loading-2 1.5s linear forwards 1.8s;
}

@keyframes loading-1 {
    0% {
        -webkit-transform: rotate(0deg);
        transform: rotate(0deg);
    }

    100% {
        -webkit-transform: rotate(180deg);
        transform: rotate(180deg);
    }
}
@keyframes loading-2 {
	0% {
		-webkit-transform: rotate(0deg);
		transform: rotate(0deg);
	}

	100% {
		-webkit-transform: rotate(144deg);
		transform: rotate(180deg);
	}
}
</style>
	<h5 style = "text-align:left"><?php echo anchor('admin/predictions', 'Back to Predictions Dashboard', 'title="Back to Predictions"'); ?></h5>
	<section>
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="card mt-3 tab-card">
						<div class="card-header tab-card-header">
							<H1 style = "text-align:center;">Generate Combinations for <?=$lottery->lottery_name;?></H1>
						</div>
						<div class="tab-content" id="myTabContent">
							<?php if (!empty($message)) ?> <span id="message"></span>
							<?php $attributes = array('id' => 'frmgenerate'); 
							echo form_open(base_url().'admin/predictions/combo_gen/'.$lottery->id, $attributes); ?>
							<div class = "col-12" style = "margin-top:2em;">
								<div class="form-group form-group-lg row"> 
									<?php $extra = array('class' => 'col-4 col-form-label col-form-label-md', 'style' => 'white-space: nowrap;');
										echo form_label('Number of Balls to Predict (N):', 'ballpredict_lb', $extra); ?>
									<?php $extra = array('class' => 'col-4 col-form-label col-form-label-md'); 
									echo form_label($predict,'predict_lb', $extra); ?> 
								</div>
								<!-- Lottery Pick Game -->
								<div class="form-group form-group-lg row"> 
									<?php $extra = array('class' => 'col-4 col-form-label col-form-label-md');
									echo form_label('Pick (R):', 'lottery_pick_lb', $extra);
									echo form_label($pick, 'lottery_pick_lb', $extra); 
									echo form_hidden('filename', $filename); ?> 
								</div>
								<!-- Total Combinations in the text file -->
								<div class="form-group form-group-lg row"> 
									<?php 
									// Combinations Label
									$extra = array('class' => 'col-4 col-form-label col-form-label-md');
									echo form_label('Combinations (Tickets):', 'combinations_lb', $extra);
									// Combinations Value
									echo form_label($combinations, 'combinations_lb', $extra); 
								?>
								</div>
								<!-- Combination Counter Display -->
								<div class="form-group form-group-lg row clearfix" style = "margin: 0 auto; display: block;"> 

									<div class="card bg-light mb-3 pull-left" style="width: 25em; max-width: 25rem;">
										<div class="card-header">Combination Counter</div>
										<div class="card-body">
											<h5 class="card-title"><div id="row_number">Combination File: <?=$filename;?>.txt</div></h5>
											<p class="card-text">
											<div id="result">
											<?php 
												$is_generated = isset($file_content) && trim($file_content) !== ''; // Check if file content exists and is not empty
												$data = array(
													'name' => 'combinations',
													'id' => 'combinations',
													'rows' => 10,
													'cols' => 40,
													'value' => isset($file_content) ? $file_content : '', // Display file content
													'style' => 'overflow-y: scroll; height: 300px; resize: none;',
													'readonly' => isset($file_content) && !empty($file_content) ? 'readonly' : '' // Make readonly if file exists
												);
											echo form_textarea($data); ?>
											</div></p>
										</div>
									</div>
										<div class="progress blue pull-right" style = "margin-left: 2em; margin-top: -2px;">
										<span class="progress-left"> <span class="progress-bar"></span></span>
										<span class="progress-right"> <span class="progress-bar"></span></span>
											<div class="progress-value">0%</div>
										</div>
								</div>
								 <div class="form-group form-group-lg row">
								  <?php 
								  $extra = array(
										'class' => 'btn btn-primary btn-lg btn-info',
										'style' => "display: block; margin:20px 20px",
										'id' => 'submit',
									);
								   // Add the 'disabled' attribute only if $is_generated is true
									if ($is_generated) {
										$extra['disabled'] = 'disabled';
									}
								   echo form_submit('submit', 'Generate Full Wheel Combination', $extra);
										$js = "location.href='".base_url()."admin/predictions/delete/$lottery->id/$filename";
										$attributes = array(
										'href' 		=> base_url()."admin/predictions/delete/'.$lottery->id.'/'.$filename",
										'class' 	=> "btn btn-primary btn-lg btn-info",
										'id'		=> 'delete',
										'style' 	=> "padding:5px; margin: 0 auto; display: block; margin:20px 20px;"
									);
										echo form_button('delete', 'Delete File: <strong>'.$filename.'.txt</strong>', $attributes); 
										$js = "location.href='".base_url()."admin/predictions/'";
										$attributes = array(
										'class' 	=> "btn btn-primary btn-lg btn-info",
										'onClick' 	=> "$js", 
										'style' 	=> "padding:5px; margin: 0 auto; display: block; margin:20px 20px;"
									);
										echo form_button('prediction_list', 'Back to Prediction List', $attributes); ?>
								</div>
							</div>	
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<script>
	// Do not Run until submit and combinations need to be created for the text file
	$('.progress.blue .progress-bar').css('border-color', '#FFFFFF');
	$('.progress .progress-right .progress-bar').css('animation', 'loading-1 0.0s linear forwards');
	$('.progress.blue .progress-left .progress-bar').css('animation', 'loading-2 0.0s linear forwards 0.0s');	

$(document).ready(function () {
    var progress = <?= $is_generated ? 100 : 0; ?>; // Set progress to 100% if combinations are already generated
    var URL_counter = "<?= base_url(); ?>admin/predictions/combo_counter/<?=$filename;?>/<?=$combinations;?>";
    var URL = "<?= base_url().'admin/predictions/combo_gen/'.$lottery->id; ?>";
    var clear_timer;

    // Initialize progress circle if combinations are already generated
    if (progress === 100) {
        updateProgressCircle(progress);
        $('.progress-value').html('<p>100%</p>'); // Set progress value to 100%
        $('#submit').prop('disabled', true); // Disable the Generate button
        $('#message').html('<h3 class="bg-warning" style="margin: 15px; text-align:center;">The combinations have already been generated and saved to the file.</h3>');
    }

    // Handle form submission for generating combinations
    $("#frmgenerate").submit(function (e) {
        e.preventDefault(); // Prevent default form submission

        $.ajax({
            type: "POST",
            url: URL,
            data: new FormData(this),
            dataType: "json",
            contentType: false,
            async: false,
            cache: false,
            processData: false,
            success: function (data) {
                if (data.success) {
                    clear_timer = setInterval(combination, 1000); // Start processing combinations
                    $('#submit').prop('disabled', true); // Disable the Generate button
                    $('.progress.blue .progress-bar').css('border-color', '#049dff');
                    $('#message').html('<h3 class="bg-warning" style="margin: 15px; text-align:center;">' + data.message + '</h3>');
                }
                if (data.error) {
                    $('#message').html('<h3 class="bg-warning" style="margin: 15px; text-align:center;">' + data.error + '</h3>');
                }
            }
        });
    });

    // Function to process combinations in chunks
    function combination() {
        $.ajax({
            url: URL_counter,
            dataType: "json",
            success: function (data) {
                if (data.success) {
                     progress = Math.min(data.percent, 100); // Cap progress at 100%
                    $('.progress-value').html('<p>' + Math.round(progress) + '%</p>');
                    $("#combinations").append(data.combotext + '\r\n'); // Append new combinations

                    updateProgressCircle(progress);

                    if (progress >= 100) {
                        $('#message').html('<h3 class="bg-warning" style="margin: 15px; text-align:center;">The Data File has ADDED the Combinations to the <?=$filename;?>.txt file.</h3>');
                        clearInterval(clear_timer); // Stop the timer
                        $('#submit').prop('disabled', true); // Disable the Generate button
                    }
                } else if (data.error) {
                    $('#message').html('<h3 class="bg-warning" style="margin: 15px; text-align:center;">' + data.error + '</h3>');
                }
            }
        });
    }

    // Function to update the progress circle visually
    function updateProgressCircle(progress) {
        if (progress >= 100) {
            $('.progress .progress-right .progress-bar').css('transform', 'rotate(180deg)');
            $('.progress .progress-left .progress-bar').css('transform', 'rotate(180deg)');
            $('.progress.blue .progress-bar').css('border-color', '#049dff'); // Ensure blue border is applied
        } else {
            var angle = (progress / 100) * 360;
            if (angle <= 180) {
                $('.progress .progress-right .progress-bar').css('transform', 'rotate(' + angle + 'deg)');
                $('.progress .progress-left .progress-bar').css('transform', 'rotate(0deg)');
            } else {
                $('.progress .progress-right .progress-bar').css('transform', 'rotate(180deg)');
                $('.progress .progress-left .progress-bar').css('transform', 'rotate(' + (angle - 180) + 'deg)');
            }
        }
    }

    // Handle delete button click
    $('#delete').on('click', function () {
        if (confirm("You are about to make a permanent deletion. Both the Filename and the Database Record will be deleted. This can not be UNDONE. Are you sure Y/N?")) {
            window.location.href = "<?= base_url(); ?>admin/predictions/delete/<?=$lottery->id;?>/<?=$filename;?>";
            return true;
        } else {
            return false;
        }
    });
});
</script>