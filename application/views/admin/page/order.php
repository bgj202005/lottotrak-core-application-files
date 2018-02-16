<section>
	<h2>Order Pages</h2>
		<p class ="alert alert-info">Drag to order pages and click 'Save'</p>
		<div class = "bg-success">/</div>
		<div id = "orderResult"></div> 
		<input type = "button" id = "save" value = "Save" class = "btn btn-primary" />
</section>
<script>
$(function() {
	$.post('<?php echo site_url('admin/page/order_ajax/'.$menu_id); ?>', {}, function(data){
		$('#orderResult').html(data);
		$('.bg-success').hide();
	});

	
	$('#save').click(function(){
		oSortable = $('.sortable').nestedSortable('toArray');
		$('#orderResult').slideUp(function(){
			$.post('<?php echo site_url('admin/page/order_ajax/'. $menu_id); ?>', { sortable: oSortable }, function(data){
				$('#orderResult').html(data);
				$('#orderResult').slideDown();
				$('.bg-success').show();
				$('.bg-success').html('<p>You have saved the order of the pages.</p>');
			});
		});
		
	});
});
</script>
