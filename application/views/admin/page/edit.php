<?php echo validation_errors(); ?>
<?php echo form_open(base_url()."admin/page/edit/".$page->id); ?>
	<h2><?php echo empty($page->id) ? 'Add a new page' : 'Edit page '.$page->title; ?></h2>
	
<table class = "table" style = "width:80%;">
	<tr>
		<td>Menu Locaton:</td>
		<td><?php echo form_dropdown('menu_id', array(0 => 'Header Menu',
				1 => 'Inside Footer Menu', 2 => 'Outside Footer Menu'), $this->input->post('menu_id') 
				? $this->input->post('menu_id') : $page->menu_id, 'id="menu_id" onchange="disable()"'); ?></td>
	</tr>
	<tr>
		<td>Parent:</td>
		<td><?php echo form_dropdown('parent_id', $pages_no_parents, $this->input->post('parent_id') 
				? $this->input->post('parent_id') : $page->parent_id, 'id=parent_id'); ?></td>
	</tr>
	<tr>
		<td>Template:</td>
		<td><?php echo form_dropdown('template', array('newsarticle' => 'News Article',
				'homepage' => 'Home Page', 'page' => 'Page'), $this->input->post('template') 
				? $this->input->post('template') : $page->template); ?></td>
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
		<td><?php echo form_textarea('body',  strip_slashes($page->body), 'id="editarea"'); ?>
	</td>
	</tr>
	<tr>
	<td><?php echo form_submit('page_save', 'Save and Exit', '
			class="btn btn-primary"'); ?></td>
	<td><?php 
		$js = "javascript: form.action='".base_url()."admin/page/edit/".$page->id."/save'";
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
function disable() {
  if (document.getElementById("menu_id").value > 0) { 
     document.getElementById("parent_id").value = 0;  
	 document.getElementById("parent_id").disabled=true;
  } else document.getElementById("parent_id").disabled=false;
}
</script>