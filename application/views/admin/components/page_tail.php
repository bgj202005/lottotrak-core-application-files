<div id="footer">
	<div class="container">
		<p class="text-muted credit">
		Copyright (c) <?php echo date('Y'); ?><a href="http://lottotrak.com"> Lottotrak</a>
						and <a href="http://metadatamedia.ca">MetaData Media Inc.</a>.
		</p>
	</div>
</div>

<!-- Font Awesome 4.7 Loading Test -->
<script type="text/javascript">
$(document).ready(function() {
	// Test if Font Awesome loaded correctly
	setTimeout(function() {
		// Create a test icon element
		var testIcon = $('<i class="fa fa-question" style="position: absolute; left: -9999px;"></i>');
		$('body').append(testIcon);
		
		// Check if the icon has the correct font-family
		var fontFamily = testIcon.css('font-family');
		var hasFA = fontFamily && (fontFamily.indexOf('FontAwesome') !== -1 || fontFamily.indexOf('Font Awesome') !== -1);
		
		// Remove test element
		testIcon.remove();
		
		// If Font Awesome didn't load, apply fallback CSS
		if (!hasFA) {
			// Try to fix Font Awesome icons by applying additional CSS for FontAwesome 4.7.0
			$('<style type="text/css">.fa, [class^="fa-"], [class*=" fa-"] { font-family: FontAwesome !important; }</style>').appendTo('head');
		}
	}, 1000);
});
</script>

<!-- Session Activity Tracker -->
<script type="text/javascript">
$(document).ready(function() {
	// Track user activity to prevent unnecessary session timeouts
	var activityTimer;
	var keepaliveInterval = 5 * 60 * 1000; // Send keepalive every 5 minutes
	
	// Reset activity timer on user interactions
	function resetActivityTimer() {
		clearTimeout(activityTimer);
		activityTimer = setTimeout(function() {
			// Send keepalive ping to server
			$.ajax({
				url: '<?php echo site_url("admin/user/keepalive"); ?>',
				type: 'POST',
				dataType: 'json',
				data: { keepalive: true },
				success: function(response) {
					if (response && response.status === 'active') {
						console.log('Session keepalive successful');
					} else if (response && response.status === 'expired') {
						// Session expired, redirect to login
						window.location.href = '<?php echo site_url("admin/user/login"); ?>';
					}
				},
				error: function() {
					console.log('Keepalive request failed');
				}
			});
		}, keepaliveInterval);
	}
	
	// Monitor user activity events
	$(document).on('click keypress scroll mousemove', function() {
		resetActivityTimer();
	});
	
	// Initialize activity timer
	resetActivityTimer();
});
</script>

</body>
</html>