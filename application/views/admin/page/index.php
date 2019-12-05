<section>
	<h2>Pages</h2>
	<?php echo anchor('admin/page/edit', '<i class = "icon-plus"></i> Add a Page'); ?>
	
	<table class="table table-striped">
		<thead>
			<tr> 
				<td>Title</td>
				<td>Parent</td>
				<td>Location</td>
				<th>Edit</th>
				<td>Delete</td>
			</tr>
		</thead>
		<tbody>
	<?php if (count($pages)): foreach($pages as $page): ?>
	<tr> 
		<td><?php echo anchor('admin/page/edit/'.$page->id, $page->title);?></td>
		<td><?php echo empty($page->parent_id) ? "Top Level" : $page->parent_title; ?></td>
		<td><?php switch ($page->menu_id) {
			case 1:
			    echo "Inside Footer";
			    break;
			case 2:
			    echo "Outside Footer";
			    break;
			default:
			     echo "Header Menu";
		}?></td>
	    <td><?php echo btn_edit('admin/page/edit/'.$page->id); ?></td>
	    <td><?php echo btn_delete('admin/page/delete/'.$page->id); ?></td>
	</tr>
	<?php endforeach; ?> 
	
	<?php else: ?>
		<tr>
			<td colspan="3">We could not find any pages.</td>
		</tr>
<?php endif; ?>
		</tbody>
	</table>
</section>