<section>
	<h2>Pages</h2>
	<?php echo anchor('admin/page/edit', '<i class = "icon-plus"></i> Add a Page'); ?>
	
	<table class="table table-striped">
		<thead>
			<tr> 
				<td>Title</td>
				<td>Parent</td>
				<td>Location</td>
				<td>Template</td>
				<td>Slug</td>
				<td>Menu Item</td>
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
		<td><?php 
			switch ($page->template) {
				case 'homepage':
					echo "Home Page (Default)";
					break;
				case 'homepage_left':
					echo "Home Page (Left Sidebar)";
					break;
				case 'page':
					echo "Page (Default)";
					break;
				case 'page_left':
					echo "Page (Left Sidebar)";
					break;
				case 'newsarticle':
					echo "News Article";
					break;
				case 'article_left':
					echo "Article (Left Sidebar)";
					break;
				case 'sidebar':
					echo "Sidebar";
					break;
				default:
					echo ucfirst(str_replace('_', ' ', $page->template));
			}
		?></td>
		<td><?=$page->slug; ?></td>
		<td><?=(!empty($page->menu_item) ? "<i class='fa fa-check' aria-hidden='true'></i>" : ""); ?></td>
	    <td><?php echo btn_edit('admin/page/edit/'.$page->id); ?></td>
	    <td><?php echo btn_delete('admin/page/delete/'.$page->id); ?></td>
	</tr>
	<?php endforeach; ?> 
	
	<?php else: ?>
		<tr>
			<td colspan="8">We could not find any pages.</td>
		</tr>
<?php endif; ?>
		</tbody>
	</table>
</section>