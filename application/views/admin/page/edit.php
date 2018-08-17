<?php echo validation_errors(); ?>
<?php echo form_open(); ?>
	<h2><?php echo empty($page->id) ? 'Add a new page' : 'Edit page '.$page->title; ?></h2>
	
<table class = "table" style = "width:80%;">
	<tr>
		<td>Menu Locaton:</td>
		<td><?php echo form_dropdown('menu_id', array(0 => 'Header Menu',
				1 => 'Inside Footer Menu', 2 => 'Outside Footer Menu'), $this->input->post('menu_id') 
				? $this->input->post('menu_id') : $page->menu_id); ?></td>
	</tr>
	<tr>
		<td>Parent:</td>
		<td><?php echo form_dropdown('parent_id', $pages_no_parents, $this->input->post('parent_id') 
				? $this->input->post('parent_id') : $page->parent_id); ?></td>
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
		<td>Body:</td>
		<td><!--  <textarea class = "tinymce" name = "body"><?php // echo $page->body; ?></textarea> -->
		<?php echo form_textarea('body', $page->body, 'class="tinymce"'); ?></td>
	</tr>
	<tr>
	<td><?php echo form_submit('submit', 'Submit', '
			class="btn btn-primary"'); ?></td>
	</tr>
</table>