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
    width: 350px;
    height: 350px !important;
    float: none;
    line-height: 350px;
    background: none;
    margin: 0;
    box-shadow: none;
    position: relative;
}

.progress:after {
    content: "";
    width: 100%;
    height: 100%;
    border-radius: 50%;
    border: 24px solid #fff;
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
    border-width: 24px;
    border-style: solid;
    position: absolute;
    top: 0
}

.progress .progress-left .progress-bar {
    left: 100%;
    border-top-right-radius: 175px;
    border-bottom-right-radius: 175px;
    border-left: 0;
    -webkit-transform-origin: center left;
    transform-origin: center left;
}

.progress .progress-right {
    right: 0;
}

.progress .progress-right .progress-bar {
    left: -100%;
    border-top-left-radius: 175px;
    border-bottom-left-radius: 175px;
    border-right: 0;
    -webkit-transform-origin: center right;
    transform-origin: center right;
    transform: rotate(0deg);
}

.progress .progress-value {
    width: 90%;
    height: 90%;
    border-radius: 50%;
    background: #000;
    font-size: 48px;
    color: #fff;
    line-height: 1;
    text-align: center;
    position: absolute;
    top: 5%;
    left: 5%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.progress .progress-value p {
    margin: 0;
    line-height: 1;
}

.progress.blue .progress-bar {
    border-color: #049dff;
}

.progress.blue .progress-left .progress-bar {
    transform: rotate(0deg);
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
							<?php if($lottery->duplicate_extra_ball && $lottery->extra_ball): ?>
								<div class="alert alert-info" style="text-align: center; margin: 15px 0;">
									<strong>Independent Extra / Bonus (duplicate) numbers:</strong> <?php echo $lottery->minimum_extra_ball; ?> - <?php echo $lottery->maximum_extra_ball; ?>
								</div>
							<?php endif; ?>
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
								<div class="form-group form-group-lg clearfix" style="display: flex; align-items: center; gap: 2em;"> 

									<div class="card bg-light mb-3" style="flex: 0 0 auto; width: 25em; max-width: 25rem;">
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
											<?php if (isset($combo_page) && $is_generated):
												$combo_total_pages = max(1, (int)ceil($combinations / $page_size)); ?>
											<div class="d-flex justify-content-between align-items-center mt-2">
												<button type="button" class="btn btn-outline-secondary btn-sm" id="combo_prev_page" disabled>&laquo; Prev</button>
												<small id="combo_page_info" class="text-muted">Page <?= $combo_page; ?> of <?= $combo_total_pages; ?></small>
												<button type="button" class="btn btn-outline-secondary btn-sm" id="combo_next_page" <?= $combo_total_pages <= 1 ? 'disabled' : ''; ?>>Next &raquo;</button>
											</div>
											<?php endif; ?>
										</div>
									</div>
									<div style="flex: 1; display: flex; justify-content: center; align-items: center; min-height: 350px;">
										<div class="progress blue">
											<span class="progress-left"> <span class="progress-bar"></span></span>
											<span class="progress-right"> <span class="progress-bar"></span></span>
											<div class="progress-value">0%</div>
										</div>
									</div>
								</div>
								 <div class="form-group form-group-lg row">
								  <div class="d-flex flex-wrap justify-content-center align-items-center" style="width: 100%;">
								  <?php 
								  // Add "Back to Combinations List" button
								  $back_attributes = array(
										'class' => 'btn btn-secondary btn-lg',
										'id'    => 'back_to_combinations_btn',
										'style' => "margin:10px 5px; white-space: nowrap;",
									);
								  echo form_button('back_to_combinations', 'Back to Combinations List', $back_attributes);
								  
								  $extra = array(
										'class' => 'btn btn-primary btn-lg btn-info',
										'style' => "margin:10px 5px; white-space: nowrap;",
										'id' => 'submit',
									);
								   // Add the 'disabled' attribute only if $is_generated is true
									if ($is_generated) {
										$extra['disabled'] = 'disabled';
									}
								   echo form_submit('submit', 'Generate Full Wheel Combination', $extra);

								   // "Verify Full Wheeling Table" button — enabled only when the file is already generated
								   $verify_attrs = array(
										'class' => 'btn btn-success btn-lg',
										'style' => "margin:10px 5px; white-space: nowrap;",
										'id'    => 'verify',
										'type'  => 'button',
									);
								   if (!$is_generated) {
										$verify_attrs['disabled'] = 'disabled';
									}
								   echo form_button('verify', 'Verify Full Wheeling Table', $verify_attrs);

										$js = "location.href='".base_url()."admin/predictions/delete/$lottery->id/$filename";
										$attributes = array(
										'href' 		=> base_url()."admin/predictions/delete/'.$lottery->id.'/'.$filename",
										'class' 	=> "btn btn-danger btn-lg",
										'id'		=> 'delete',
										'style' 	=> "margin:10px 5px; white-space: nowrap;"
									);
										echo form_button('delete', 'Delete File: <strong>'.$filename.'.txt</strong>', $attributes); 
										
										$js = "location.href='".base_url()."admin/predictions/'";
										$attributes = array(
										'class' 	=> "btn btn-primary btn-lg btn-info",
										'onClick' 	=> "$js", 
										'style' 	=> "margin:10px 5px; white-space: nowrap;"
									);
										echo form_button('prediction_list', 'Back to Prediction List', $attributes); 
										echo form_close();?>
								  </div>
								</div>
							</div>	
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

<!-- Cancel-generation confirmation modal -->
<div class="modal fade" id="cancelGenModal" tabindex="-1" role="dialog" aria-labelledby="cancelGenModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header bg-warning">
				<h5 class="modal-title" id="cancelGenModalLabel"><strong>Generation In Progress</strong></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<p>Generation is still in progress.</p>
				<p><strong>Do you want to exit early before the combinations are generated?</strong></p>
				<p class="text-muted">Choosing <em>Yes</em> will clear the partial file and the status will revert to <span class="badge badge-warning">Not Generated</span>.</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger btn-lg" id="cancelGenConfirm">Yes</button>
				<button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">No</button>
			</div>
		</div>
	</div>
</div>
	<script>
	// Reset both progress-bar segments to 0° so the circle starts completely black
	$('.progress .progress-right .progress-bar').css({'animation': 'none', '-webkit-transform': 'rotate(0deg)', 'transform': 'rotate(0deg)'});
	$('.progress .progress-left .progress-bar').css({'animation': 'none', '-webkit-transform': 'rotate(0deg)', 'transform': 'rotate(0deg)'});

$(document).ready(function () {
    var progress = <?= $is_generated ? 100 : 0; ?>; // Set progress to 100% if combinations are already generated
    var URL_counter = "<?= base_url(); ?>admin/predictions/combo_counter/<?=$filename;?>/<?=$combinations;?>";
    var URL = "<?= base_url().'admin/predictions/combo_gen/'.$lottery->id; ?>";
    var URL_verify = "<?= base_url().'admin/predictions/verify_combinations/'.$lottery->id; ?>";
    var URL_cancel = "<?= base_url().'admin/predictions/cancel_generation/'.$lottery->id; ?>";
    var URL_back   = "<?= base_url().'admin/predictions/generate/'.$lottery->id; ?>";
    <?php if (isset($combo_page)): ?>
    var URL_chunk      = "<?= base_url().'admin/predictions/get_combinations_chunk/'.$lottery->id; ?>";
    var combo_page     = <?= $combo_page; ?>;
    var combo_per_page = <?= $page_size; ?>;
    var combo_pages    = Math.ceil(<?= $combinations; ?> / <?= $page_size; ?>);
    <?php endif; ?>
    var clear_timer = null; // Declare clear_timer globally and initialize to null
	var is_complete = false; // Add a flag to track completion
	var is_fetching = false; // Prevent overlapping counter requests

    // Initialize progress circle if combinations are already generated
    if (progress === 100) {
        updateProgressCircle(progress);
        $('.progress-value').html('<p>100%</p>'); // Set progress value to 100%
        $('#submit').prop('disabled', true); // Disable the Generate button
        $('#message').html('<h3 class="bg-warning" style="margin: 15px; text-align:center;">The combinations have already been generated and saved to the file.</h3>');
		is_complete = true; // Mark as complete
	}

    // Handle form submission for generating combinations
    $("#frmgenerate").submit(function (e) {
        e.preventDefault();

        // Clear any existing counter timer
        if (clear_timer !== null) {
            clearInterval(clear_timer);
            clear_timer = null;
        }

        // Reset state for a fresh generation
        is_complete  = false;
        is_fetching  = false;
        progress     = 0;
        $("#combinations").val('');
        updateProgressCircle(0);
        $('.progress-value').html('<p>0%</p>');

        // Immediately lock the button and show a "working" message
        $('#submit').prop('disabled', true);
        $('#verify').prop('disabled', true);
        $('#message').html('<h3 class="bg-info" style="margin:15px; text-align:center; color:#fff;">Generating combinations, please wait&hellip;</h3>');

        $.ajax({
            type:        "POST",
            url:         URL,
            data:        new FormData(this),
            dataType:    "json",
            contentType: false,
            cache:       false,
            processData: false,
            success: function (data) {
                if (data.success) {
                    // File is written — start the progress counter
                    $('.progress.blue .progress-bar').css('border-color', '#049dff');
                    $('#message').html('<h3 class="bg-warning" style="margin:15px; text-align:center;">Reading progress&hellip;</h3>');
                    if (clear_timer === null) {
                        clear_timer = setInterval(combination, 100);
                    }
                } else if (data.error) {
                    $('#submit').prop('disabled', false);
                    $('#verify').prop('disabled', true);
                    $('#message').html('<h3 class="bg-danger" style="margin:15px; text-align:center; color:#fff;">' + data.error + '</h3>');
                }
            },
            error: function (xhr, status, err) {
                $('#submit').prop('disabled', false);
                $('#verify').prop('disabled', true);
                $('#message').html('<h3 class="bg-danger" style="margin:15px; text-align:center; color:#fff;">Request failed: ' + err + '. Please try again.</h3>');
            }
        });

        return false; // belt-and-suspenders — stops normal form navigation
    });

    // Function to process combinations in chunks
    function combination() {
		if (is_complete || is_fetching) {
			return; // Already done, or a request is still in-flight — skip this tick
		}
		is_fetching = true;

		$.ajax({
			url: URL_counter,
			dataType: "json",
			success: function (data) {
				is_fetching = false;
				if (data.success) {
					progress = Math.min(data.percent, 100); // Cap progress at 100%
					$('.progress-value').html('<p>' + Math.round(progress) + '%</p>');
					
					// Append new combinations without extra spaces; on the final batch
					// replace the textarea so we see the true last combinations in the file.
					if (data.combotext && data.combotext.trim() !== '') {
						if (progress >= 100) {
							$("#combinations").val(data.combotext.trim());						// Scroll to bottom so the last combination is immediately visible
						var ta = document.getElementById('combinations');
						ta.scrollTop = ta.scrollHeight;						} else {
							$("#combinations").val(function (index, value) {
								return value + data.combotext.trim() + '\n';
							});
						}
					}
					
					updateProgressCircle(progress);
					
					if (progress >= 100) {
						clearInterval(clear_timer); // Stop the timer
                        clear_timer = null;
						$('#message').html('<h3 class="bg-warning" style="margin: 15px; text-align:center;">The Combinations have been generated and saved to the <?=$filename;?>.txt file.</h3>');
						$('#submit').prop('disabled', true);
						$('#verify').prop('disabled', false); // Enable Verify now that generation is complete
						is_complete = true;
					}
				} else if (data.error) {
					// Show the error but keep the timer running so subsequent ticks can retry
					console.log("Counter error:", data.error);
					$('#message').html('<h3 class="bg-warning" style="margin: 15px; text-align:center;">' + data.error + '</h3>');
				}
			},
			error: function(xhr, status, error) {
				is_fetching = false;
				// Transient network/server error — log it and let the next tick retry
				console.log("AJAX counter error:", error);
			}
		});
	}
    // Function to update the progress circle visually
    function updateProgressCircle(progress) {
        var angle = (progress / 100) * 360; // Calculate the angle based on the progress percentage
        if (angle <= 180) {
            $('.progress .progress-right .progress-bar').css('transform', 'rotate(' + angle + 'deg)');
            $('.progress .progress-left .progress-bar').css('transform', 'rotate(0deg)');
        } else {
            $('.progress .progress-right .progress-bar').css('transform', 'rotate(180deg)');
            $('.progress .progress-left .progress-bar').css('transform', 'rotate(' + (angle - 180) + 'deg)');
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

    // Handle "Verify Full Wheeling Table" button click
    $('#verify').on('click', function () {
        var $btn = $(this);
        $btn.prop('disabled', true).text('Verifying...');

        $.ajax({
            type: 'POST',
            url: URL_verify,
            data: {
                filename: '<?= $filename; ?>',
                combinations: <?= $combinations; ?>
            },
            dataType: 'json',
            success: function (data) {
                if (data.verified) {
                    $('#message').html('<h3 class="bg-success" style="margin:15px; text-align:center; color:#fff;">The Combinations have all been generated and Verified.</h3>');
                    $btn.prop('disabled', false).text('Verify Full Wheeling Table');
                } else {
                    $('#message').html('<h3 class="bg-danger" style="margin:15px; text-align:center; color:#fff;">The Combinations are not complete. Click Full Wheeling Table button to regenerated.</h3>');
                    // Re-enable Generate button and reset progress so admin can regenerate
                    $('#submit').prop('disabled', false);
                    $('#combinations').val('');
                    progress = 0;
                    is_complete = false;
                    is_fetching = false;
                    updateProgressCircle(0);
                    $('.progress-value').html('<p>0%</p>');
                    $btn.prop('disabled', false).text('Verify Full Wheeling Table');
                }
            },
            error: function () {
                $('#message').html('<h3 class="bg-danger" style="margin:15px; text-align:center; color:#fff;">An error occurred during verification. Please try again.</h3>');
                $btn.prop('disabled', false).text('Verify Full Wheeling Table');
            }
        });
    });

    <?php if (isset($combo_page)): ?>
    // Paginated combination file browsing (only available when file is pre-generated)
    function loadComboPage(page) {
        if (page < 1 || page > combo_pages) return;
        $('#combo_prev_page, #combo_next_page').prop('disabled', true);
        $.ajax({
            type:     'POST',
            url:      URL_chunk,
            data:     { filename: '<?= $filename; ?>', page: page },
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    combo_page = data.page;
                    $('#combinations').val(data.content.trimEnd());
                    document.getElementById('combinations').scrollTop = 0;
                    $('#combo_page_info').text('Page ' + combo_page + ' of ' + combo_pages);
                    $('#combo_prev_page').prop('disabled', combo_page <= 1);
                    $('#combo_next_page').prop('disabled', combo_page >= combo_pages);
                }
            },
            error: function () {
                $('#combo_prev_page').prop('disabled', combo_page <= 1);
                $('#combo_next_page').prop('disabled', combo_page >= combo_pages);
            }
        });
    }
    $('#combo_prev_page').on('click', function () { loadComboPage(combo_page - 1); });
    $('#combo_next_page').on('click', function () { loadComboPage(combo_page + 1); });
    <?php endif; ?>

    // Back to Combinations List — warn if a generation is actively in progress
    $('#back_to_combinations_btn').on('click', function () {
        if (clear_timer !== null) {
            $('#cancelGenModal').modal('show');
        } else {
            window.location.href = URL_back;
        }
    });

    // "Yes, Exit Early" inside the modal
    $('#cancelGenConfirm').on('click', function () {
        $('#cancelGenModal').modal('hide');
        clearInterval(clear_timer);
        clear_timer = null;
        $.ajax({
            type:     'POST',
            url:      URL_cancel,
            data:     { filename: '<?= $filename; ?>' },
            dataType: 'json',
            complete: function () {
                // Navigate regardless of AJAX success/failure
                window.location.href = URL_back;
            }
        });
    });
});
</script>