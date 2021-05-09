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
}
</style>
	<h2><?php echo 'File Select For: '.$lottery->lottery_name; ?></h2>
	<h5 style = "text-align:left"><?php echo anchor('admin/predictions', 'Back to Predictions Dashboard', 'title="Back to Predictions"'); ?></h5>
	<section>
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="card mt-3 tab-card">
						<div class="card-header tab-card-header">
							<H1 style = "text-align:center;">Please Select the Combination File</H1>
						</div>
						<div class="tab-content" id="myTabContent">
							<?php if (!empty($message)) ?> <h3 class="bg-warning" style = "text-align:center;"><?=$message; ?></h3>
							<?php echo validation_errors('<H2><div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">','</div></H2>'); ?>
							<?php echo form_open(base_url().'admin/predictions/generate_select/'.$lottery->id); ?>
							<div class = "col-12" style = "margin-top:2em;">
								<div class="form-group form-group-lg row">
									<table class="table table-bordered" style = "margin:1em;">
										<thead>
											<tr>
												<th scope="col">#</th>
												<th scope="col">Filename</th>
												<th scope="col">N</th>
												<th scope="col">R</th>
												<th scope="col">Combinations</th>
											</tr>
										</thead>
										<tbody>
									<?php $row = 1; 
										foreach($lottery->generate as $file)
										{ ?>
											<tr>	
											<th scope="row"><?=$row;?></th>
											<div class="form-check">
												<?php $extra = array('class' => 'form-check-input', 'name' => 'Radio_'.$file->id,
												'id' => 'Radio_'.$file->id, 'style' => 'margin-left:1px; margin-right:5px; margin-top:10px;',
												'checked');
												echo '<td>'.form_radio('file', $file->file_name, $extra);
												$extra = array('class' => 'col-4 col-form-label col-form-label-md', 'style' => 'white-space: nowrap;'); 
												echo form_label($file->file_name.'.txt', 'file_name_lb_'.$file->id, $extra).'</td>';
												echo '<td>'.form_label($file->N, 'balls_predict_lb_'.$file->id, $extra).'</td>';
												echo '<td>'.form_label($file->R, 'pick_game_lb_'.$file->id, $extra).'</td>';
												echo '<td>'.form_label($file->CCCC, 'combinations_lb_'.$file->id, $extra).'</td>'; 
												echo '</tr>';?>
											</div>
											<?php $row++; 
											} ?>
											</tr>
										</tbody>
									</table>
								</div>
								<div class="form-group form-group-lg row">
									<?php $extra = array('class' => 'btn btn-primary btn-lg btn-info d-flex', 
														'style' => "padding:5px; display: block; margin:20px 30px;");
										echo form_submit('submit', 'Select File', $extra);
										$js = "javascript: form.action='".base_url()."/admin/predictions/combo_save/".$lottery->id."'";
										$js = "location.href='".base_url()."/admin/predictions/'";
										$attributes = array(
										'class' 	=> "btn btn-primary btn-lg btn-info",
										'onClick' 	=> "$js", 
										'style' 	=> "padding:5px; display: block; margin:20px 20px;"
									);
										echo form_button('prediction_list', 'Back to Prediction List', $attributes); 
										echo form_close(); ?> <!-- </form> -->
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>