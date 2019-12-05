<section>
	<h2>Lottery Profiles</h2>
	<?php echo anchor('admin/lotteries/edit', '<i class = "icon-plus"></i> Add a Lottery'); ?>
	
	<table class="table table-striped">
		<thead>
			<tr> 
				<td>Lottery Name</td>
				<td>Country</td>
				<td>State / Province</td>
				<td>Pick</td>
				<td>Lowest Ball Drawn</td>
				<td>Highest Ball Drawn</td>
				<td>Extra / Bonus Ball?</td>
				<td>Allow Duplicate Extra / Bonus?</td>
				<td>Lowest Extra / Bonus Ball</td>
				<td>Highest Extra Ball</td>
				<th>Edit</th>
				<td>Delete</td>
			</tr>
		</thead>
		<tbody>
	<?php if (count($pages)): foreach($pages as $page): ?>
	<tr> 
		<td><?php echo anchor('admin/page/edit/'.$page->id, $page->title);?></td>
		<td><?php echo empty($page->parent_id) ? "No Parent" : $page->parent_title; ?></td>
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