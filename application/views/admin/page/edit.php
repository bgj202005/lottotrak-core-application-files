<?php defined('BASEPATH') OR exit('No direct script access allowed');
echo validation_errors();
echo form_open(base_url()."admin/page/edit/".(!empty($page->id) ? $page->id : '')); ?>
	<h2><?php echo empty($page->id) ? 'Add a new page' : 'Edit page '.$page->title; ?></h2>
	
<table class = "table" style = "width:90%;">
	<tr>
		<td>Menu Locaton:</td>
		<td><?php echo form_dropdown('menu_id', array(0 => 'Header Menu',
				1 => 'Inside Footer Menu', 2 => 'Outside Footer Menu'), $this->input->post('menu_id') 
				? $this->input->post('menu_id') : $page->menu_id, 'id="menu_id" onchange="disable()"'); ?>
		<?php 	$extra = array('class' => 'col-4 col-form-label col-form-label-md', 'style' => 'text-align:right;');
				echo form_label('Visible On Menu', 'visible_menu_lb', $extra);
				$extra = array('id' => 'defaultCheck1', 'style' => 'text-align:left;'); 
				echo form_checkbox('menu_item', set_value('menu_item', '1'), set_checkbox('menu_item', '1', (!empty($page->menu_item)), $extra));  ?>
		</td>
	</tr>
	<tr>
		<td>Parent:</td>
		<td><?php echo form_dropdown('parent_id', $pages_no_parents, $this->input->post('parent_id') 
				? $this->input->post('parent_id') : $page->parent_id, 'id=parent_id'); ?></td>
	</tr>
	<tr>
		<td>Template:</td>
		<td><?php $extra = array('id' => 'sel_template'); 
			echo form_dropdown('template', array('homepage' => 'Home Page', 'page' => 'Page',
			'newsarticle' => 'News Article', 'sidebar' => 'Right Sidebar'), $this->input->post('template') 
				? $this->input->post('template') : $page->template, $extra); ?></td>
	</tr>
	<tr>
		<td>Position / Section:</td>
		<td><?php $extra = array('id' => 'sel_position'); 
				echo form_dropdown('position', $position_options, $this->input->post('position') 
				? $this->input->post('position') : $page->position, $extra); ?></td>
	</tr>
	<tr>
		<td>Title:</td>
		<td><?php echo form_input('title', set_value('title', $page->title)); ?></td>
	</tr>
	<tr>
		<td>Slug:</td>
		<td><?php echo form_input('slug', set_value('slug', $page->slug)); ?></td>
	</tr>
	<tr>
		<td>Page Content:</td>
		<td><?php echo form_textarea('body',  stripslashes($page->body), 'id="editarea"'); ?>
	</td>
	</tr>
	<tr>
		<td>Raw Data (Advertising Snippets):</td>
		<td><?php // set_value MUST have html_escape set to FALSE to turn off HTML escaping of the raw text area.
			$data = array('name' => 'raw',
									 'value' => set_value('raw', stripslashes($page->raw), FALSE),
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
						Page Description (150-160 Character Maximum):<br />
						<?php $data = array('name' => 'description',
									 'value' => set_value('description', (!empty($page->description) ? $page->description : '')),
									 'rows'	=> '2',
									 'cols'	=> '10',
									 'style' => 'width:95%; resize: none; margin-left: 10px; margin-right:10px; margin-top:5px;',
									 'maxlength' => '160',
									 'class' => 'form-control'
								); 
						echo form_textarea($data); ?>
						<br /><br />Canonical Tag?
						<?php $extra = array('class' => 'col-form-label col-form-label-md', 'id' => 'defaultCheck2', 'style' => 'margin-left:10px; text-align:left;');
						 echo form_checkbox('canonical', set_value('canonical', '1'), set_checkbox('canonical', '1', (!empty($page->canonical))), $extra);  ?>
					</div>
    			</div>
  			</div>
		</div>
		</td><td></td>  
	</tr>
	<tr>
	<td><?php echo form_submit('page_save', 'Save and Exit', '
			class="btn btn-primary"'); ?></td>
	<td><?php 
		$js = (!empty($page->id) ? "javascript: form.action='".base_url()."admin/page/edit/".$page->id."/save'" : "javascript: form.action='".base_url()."admin/page/edit/save'");
		$class = "btn btn-primary";
		$attributes = array(
			'class' 	=> "$class",
			'onClick' 	=> "$js", 
			'style' 	=> "margin-right:20px; padding:5px;",
		);
		echo form_submit('page_edit', 'Save and No Exit', $attributes);  
		$js = "location.href='".base_url()."admin/page/'";
		$class = "btn btn-primary";
		$attributes = array(
			'class' 	=> "$class",
			'onClick' 	=> "$js", 
			'style' 	=> "margin-left:20px; padding:5px;"
		);
		echo form_button('page_cancel', 'Cancel Page Edit', $attributes); ?>
	</td>		
	</tr>
		<?php echo form_close(); ?>
</table>
<script>
$(document).ready(function(){

$("#sel_template").change(function(){
	var type = $(this).val();
	$.ajax({
		url: '<?php echo base_url(); ?>admin/page/get_position/',
		type: 'POST',
		data: { template:type },
		dataType: 'json',
		success:function(result){
			var len = result[0].length;
			$("#sel_position").empty();
			for( var i = 0; i<len; i++){
				var position = result[0][i]['id'];
				var name = result[0][i]['name'];
				$("#sel_position").append("<option value='"+position+"'>"+name+"</option>");
			}
		}
	});
});
});

function disable() {
  if (document.getElementById("menu_id").value > 0) { 
     document.getElementById("parent_id").value = 0;  
	 document.getElementById("parent_id").disabled=true;
  } else document.getElementById("parent_id").disabled=false;
}
</script>