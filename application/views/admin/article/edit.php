<?php defined('BASEPATH') OR exit('No direct script access allowed');

echo validation_errors();

// Display success message if article was saved
if (isset($success_message)) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
    echo $success_message;
    echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
    echo '<span aria-hidden="true">&times;</span>';
    echo '</button>';
    echo '</div>';
}

// Display any errors
if (isset($errors) && !empty($errors)) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
    foreach ($errors as $error) {
        echo $error . '<br>';
    }
    echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
    echo '<span aria-hidden="true">&times;</span>';
    echo '</button>';
    echo '</div>';
}

echo form_open(base_url()."admin/article/edit/".(!empty($article->id) ? $article->id : '')); ?>
<h5 style = "text-align:left"><?php echo anchor('admin/article', 'Back to the Lottery News Article Dashboard', 'title="Back to Lottery News Article Dashboard"'); ?></h5>
<h2><?php echo empty($article->id) ? 'Add a new article' : 'Edit an Article '.$article->title; ?></h2>
<table class="table" style="width: 90%;">
	<tr>
		<td style="width: 20%;">Publication Date:</td>
		<td>
			<div class="form-group">
				<div class="input-group" style="width: 200px;"> 
					<?php $attr = array ('maxlength' => '10', 'class' => 'form-control datepicker-input', 'style' => 'width: 150px;', 'id' => 'pubdate', 'placeholder' => 'dd-mm-yyyy', 'readonly' => 'readonly');  
					echo form_input('pubdate', set_value('pubdate', date("d-m-Y",strtotime(str_replace('/','-',$article->pubdate)))), $attr); ?>  
					<div class="input-group-append">
						<span class="input-group-text calendar-trigger" data-target="#pubdate"><i class="fa fa-calendar"></i></span>
					</div>
				</div>
			</div> 
		</td>
	</tr>
	<tr>
		<td style="width: 20%;">Modification Date:</td>
		<td>
			<div class="form-group">
				<div class="input-group" style="width: 240px;">
					<?php $extra = array ('maxlength' => '19', 'class' => 'form-control datepicker-input', 'style' => 'width: 190px;', 'id' => 'modified', 'placeholder' => 'dd-mm-yyyy hh:mm:ss', 'readonly' => 'readonly');
					echo form_input('modified', set_value('modified', date("d-m-Y H:i:s")), $extra); ?>
					<div class="input-group-append">
						<span class="input-group-text calendar-trigger" data-target="#modified"><i class="fa fa-calendar"></i></span>
					</div>
				</div>
			</div>
		</td>
	</tr>
	<tr>
		<td style="width: 20%;">Title:</td>
		<td><?php echo form_input('title', set_value('title', $article->title), 'style = "width:100%;" id="title"'); ?></td>
	</tr>
	<tr>
		<td style="width: 20%;">Slug:</td>
		<td><?php echo form_input('slug', set_value('slug', $article->slug), 'style = "width:100%;" id="slug" placeholder="Leave blank to auto-generate from title"'); ?></td>
	</tr>
	<tr>
		<td style="width: 20%;">Lottery Article Content:</td>
		<td><?php echo form_textarea('body', strip_slashes($article->body), 'id="editarea"');
			?></td>
	</tr>
	<tr>
		<td>Raw Data (Advertising Snippets):</td>
		<td><?php // set_value MUST have html_escape set to FALSE to turn off HTML escaping of the raw text area.
			$data = array('name' => 'raw',
									 'value' => set_value('raw', stripslashes($article->raw), FALSE),
									 'rows'	=> '8',
									 'cols'	=> '10',
									 'style' => 'resize: none;',
									 'class' => 'form-control'
								); 
			echo form_textarea($data); ?>
		</td>
	</tr>
	<tr>
		<td colspan = "2">
			<div id="accordion">
    		<div class="card">
    			<div class="card-header" id="heading-1">
					<h5 class="mb-0">
        			<a role="button" data-toggle="collapse" href="#collapse-1" aria-expanded="true" aria-controls="collapse-1">
          			Optional SEO</a></h5>
    			</div>
    				<div id="collapse-1" class="collapse" data-parent="#accordion" aria-labelledby="heading-1">
      				<div class="card-body">
						Page Description (160 Character Maximum):<br />
						<?php $data = array('name' => 'description',
									 'value' => set_value('description', (!empty($article->description) ? $article->description : '')),
									 'rows'	=> '2',
									 'cols'	=> '10',
									 'style' => 'width:95%; resize: none; margin-left: 10px; margin-right:10px; margin-top:5px;',
									 'maxlength' => '160',
									 'class' => 'form-control'
								); 
						echo form_textarea($data); ?>
						<br /><br />Canonical Tag?
						<?php $extra = array('class' => 'col-form-label col-form-label-md', 'id' => 'defaultCheck2', 'style' => 'margin-left:10px; text-align:left;');
						 echo form_checkbox('canonical', set_value('canonical', '1'), set_checkbox('canonical', '1', (!empty($article->canonical))), $extra);  ?>
					</div>
    			</div>
  			</div>
		</div>
		</td><td></td>  
	</tr>
	<tr>	
		<td><?php echo form_submit('submit', 'Save and Exit', '
			class="btn btn-primary"'); ?></td>
		<td><?php 
		$js = (!empty($article->id) ? "javascript: form.action='".base_url()."admin/article/edit/".$article->id."/save'" : "javascript: form.action='".base_url()."admin/article/edit/save'");
		$class = "btn btn-primary";
		$attributes = array(
			'class' 	=> "$class",
			'onClick' 	=> "$js", 
			'style' 	=> "margin-right:20px; padding:5px;",
		);
		echo form_submit('article_edit', 'Save and No Exit', $attributes);  
		$js = "location.href='".base_url()."admin/article/'";
		$class = "btn btn-primary";
		$attributes = array(
			'class' 	=> "$class",
			'onClick' 	=> "$js", 
			'style' 	=> "margin-left:20px; padding:5px;"
		);
		echo form_button('article_cancel', 'Cancel Article Edit', $attributes); ?>
	</td>	
	</tr>
</table>

<script type="text/javascript">
$(document).ready(function() {
  console.log('Document ready - starting datepicker setup');
  
  // Enhanced datepicker initialization with proper view mode
  $('#pubdate, #modified').datepicker({
    format: 'dd-mm-yyyy',
    autoclose: true,
    todayHighlight: true,
    container: 'body', // Append to body to avoid z-index issues
    orientation: 'bottom auto', // Smart positioning
    startView: 0, // Start with days view (0=days, 1=months, 2=years)
    minViewMode: 0, // Allow drilling down to days
    maxViewMode: 2 // Allow going up to years
  });
  
  console.log('Datepickers initialized on:', $('#pubdate, #modified').length, 'elements');
  
  // Handle calendar icon clicks
  $('.calendar-trigger').click(function(e) {
    e.preventDefault();
    e.stopPropagation();
    
    var targetId = $(this).attr('data-target');
    var targetElement = $(targetId);
    
    console.log('Calendar trigger clicked for:', targetId);
    
    if (targetElement.length > 0) {
      // Close other datepickers first
      $('#pubdate, #modified').datepicker('hide');
      
      // Small delay to ensure proper positioning
      setTimeout(function() {
        targetElement.datepicker('show');
        console.log('Datepicker shown for:', targetId);
      }, 50);
    }
  });
  
  // Handle input field clicks
  $('#pubdate, #modified').click(function(e) {
    e.preventDefault();
    e.stopPropagation();
    
    console.log('Input field clicked:', this.id);
    
    // Close other datepickers first
    $('#pubdate, #modified').not(this).datepicker('hide');
    
    // Small delay to ensure proper positioning
    var self = this;
    setTimeout(function() {
      $(self).datepicker('show');
    }, 50);
  });
  
  // Auto-generate slug from title
  $('#title').on('input keyup', function() {
    var title = $(this).val();
    var slug = $('#slug').val();
    
    // Only auto-generate if slug field is empty
    if (slug.length === 0) {
      var autoSlug = title.toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '') // Remove special characters
        .replace(/[\s-]+/g, '-')      // Replace spaces and multiple hyphens with single hyphen
        .replace(/^-+|-+$/g, '');     // Remove leading/trailing hyphens
      $('#slug').val(autoSlug);
    }
  });
  
  // Debug info
  setTimeout(function() {
    console.log('Debug info:');
    console.log('- jQuery loaded:', typeof $ !== 'undefined');
    console.log('- Datepicker available:', typeof $.fn.datepicker !== 'undefined');
    console.log('- Calendar triggers found:', $('.calendar-trigger').length);
    console.log('- Date inputs found:', $('#pubdate, #modified').length);
  }, 1000);
});
</script>

<style>
/* Force datepicker to appear above all other elements with proper Bootstrap 3 styling */
.datepicker,
.datepicker-dropdown {
  z-index: 99999 !important;
  position: absolute !important;
  background-color: #fff !important;
  border: 1px solid #ccc !important;
  border-radius: 4px !important;
  box-shadow: 0 6px 12px rgba(0,0,0,.175) !important;
  padding: 4px !important;
  display: block !important;
}

/* Only show the active datepicker view, hide others */
.datepicker > div {
  display: none !important;
}

.datepicker > div.datepicker-days {
  display: block !important;
}

.datepicker.days .datepicker-days {
  display: block !important;
}

.datepicker.months .datepicker-months {
  display: block !important;
}

.datepicker.years .datepicker-years {
  display: block !important;
}

.datepicker table {
  background-color: #fff !important;
  margin: 0 !important;
  width: 100% !important;
  display: table !important;
}

.datepicker table tr {
  display: table-row !important;
}

.datepicker table tr td,
.datepicker table tr th {
  background-color: #fff !important;
  border: none !important;
  display: table-cell !important;
  text-align: center !important;
  width: 30px !important;
  height: 30px !important;
  padding: 0 !important;
  vertical-align: middle !important;
}

/* Make calendar trigger clickable */
.calendar-trigger {
  cursor: pointer !important;
  user-select: none !important;
  z-index: 1 !important;
  position: relative !important;
}

.calendar-trigger:hover {
  background-color: #e9ecef !important;
}

.calendar-trigger i {
  pointer-events: none !important;
}

/* Make input fields clickable to open datepicker */
.datepicker-input {
  cursor: pointer !important;
  position: relative !important;
  z-index: 1 !important;
}

/* Ensure input groups don't interfere with datepicker positioning */
.input-group {
  position: relative !important;
  z-index: 1 !important;
}

/* Bootstrap 3 datepicker specific fixes */
.datepicker-dropdown:before,
.datepicker-dropdown:after {
  display: inline-block !important;
}

.datepicker table tr td.day:hover,
.datepicker table tr td.focused {
  background: #eeeeee !important;
  cursor: pointer !important;
}

.datepicker table tr td.active,
.datepicker table tr td.active.highlighted {
  background-color: #428bca !important;
  color: #fff !important;
}

.datepicker table tr td.today {
  background-color: #ffdb99 !important;
  color: #000 !important;
}
</style>