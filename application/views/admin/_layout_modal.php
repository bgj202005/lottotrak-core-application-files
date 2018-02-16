<?php $this->load->view('admin/components/page_head'); ?>

<body style = "background: #555;">

<div class = "model show" role = "dialog">
  <div class="modal-dialog">
    <div class="modal-content">
		<div class = "modal-header">
		<h4><?php echo $title; ?></h4>
		<p><?php echo $message; ?></p>
		</div>
			<div class = "modal-body">
				<?php $this->load->view($subview); // subview is set in the controller ?>
			</div> 
			<div class = "modal-footer">
			</div>
		</div>
	</div>
</div>

<?php $this->load->view('admin/components/page_tail'); ?>