<style>
	table{
    	width:100%;
	}
	tr{
		font-size: 0.77em;
	}
	label {
    	display: inline-flex;
    	margin-bottom: .5rem;
    	margin-top: .5rem;
}
</style>	
<section>
	<h2>Lottery Actual Win History</h2>
	<?php if (isset($message)) : ?> <h4 class="bg-warning" id = "message" style = "margin-top: 20px; text-align:center;"><?=$message; endif; ?></h4>
	<h4 id = "status" style = "margin-top: 20px; text-align:center; display:none;"></h4>	
	<div class = "status"></div>
	<div class="table-responsive">
	<div class ="table-responsive-xl">
	<table class="table table-striped">
		<thead>
			<tr> 
				<th style = "text-align:center; white-space: nowrap;">Logo</th>
				<th style = "text-align:center; white-space: nowrap;">Lottery</th>
				<th style = "text-align:center; white-space: nowrap;">State/Prov</th>
				<th style = "text-align:center; white-space: nowrap;">Country</th>
				<th style = "text-align:center; white-space: nowrap;">At a Glance</th>
				<th style = "text-align:center; white-space: nowrap;">H-W-C Wins</th>
				<th style = "text-align:center; white-space: nowrap;">Follower Wins</th>
				<th style = "text-align:center; white-space: nowrap;">Friend Wins</th>
			</tr>
		</thead>
		<tbody>
	<?php if (count($lotteries)): foreach($lotteries as $lottery): ?>
	<tr>
		<td style = "text-align:center;"><?php if (!empty($lottery->lottery_image)) 
			{ 
				if (DIRECTORY_SEPARATOR === '/') 
				{
    			// unix, linux, mac
					$path = $this->input->server('DOCUMENT_ROOT').'/images/uploads/'.$lottery->lottery_image;
				}
				else
				{
				// windows	
					$path =	base_url().'images/uploads/'.$lottery->lottery_image;
				}
				$image_info = getimagesize($path); 
				$extra = array('width' => $image_info[0]/2, 'height' => $image_info[1]/2);
				echo img(base_url().'images/uploads/'.$lottery->lottery_image, FALSE, $extra); 
			} ?></td>
		<td style = "text-align:center; white-space: nowrap;"><?php echo anchor('admin/statistics/view_draws/'.$lottery->id, $lottery->lottery_name);?></td>
		<td style = "text-align:center; white-space: nowrap;"><?=$lottery->lottery_state_prov; ?></td>
		<td style = "text-align:center; white-space: nowrap;"><?=$lottery->lottery_country_id; ?></td>
		<td style = "text-align:center;"><?php echo $history->btn_glance('admin/history/glance/'.$lottery->id); ?></td>
		<td style = "text-align:center;"><?php echo $history->btn_hwc('admin/history/h_w_c/'.$lottery->id); ?></td>
		<td style = "text-align:center;"><?php echo $history->btn_followers('admin/history/followers/'.$lottery->id); ?></td>
		<td style = "text-align:center;"><?php echo $history->btn_friends('admin/history/friends/'.$lottery->id); ?></td>
	</tr>
	<?php endforeach; ?> 
	
	<?php else: ?>
		<tr>
			<td colspan="12" style = "text-align:center">No Lotteries are available.</td>
		</tr>
<?php endif; ?>
		</tbody>
	</table>
	</div>
	</div>
</section>
<script>
$(document).ready(function(){
  $('.calculate').click(function(){
	$('#status').css('display', 'block');
	$('#message').css('display', 'none'); 
	document.getElementById("status").innerHTML = "Updating Lottery. Please Wait.";
	setTimeout(fade_out, 1500);
	});
	function fade_out() {
      $("#status").fadeOut();
    }
});
function redirect(url) {
   window.location.href = url; }
</script>