<footer>
<div id="footer">
		<div class="wrapper">
        	<div class="row">
        	<div class="col-lg-12">
            	<div class="footer-inner col-centered">
                	 
                    <?php echo get_footer_menu($footer_menu_outside, $maintenance, 'footer-menu'); ?>
					
					<p class="credit">
						Copyright (c) <?php echo date('Y'); ?><a href="http://lottotrak.com"> Lottotrak</a>
						and <a href="http://metadatamedia.ca">MetaData Media Inc.</a>.
					</p>
				</div>
			  </div>
			</div>
  		</div>	
  	</div>
</footer>
<!-- SmartMenus jQuery init -->
<script type="text/javascript">
	$(function() {
		$('#top-menu').smartmenus({
			mainMenuSubOffsetX: -1,
			mainMenuSubOffsetY: 4,
			subMenusSubOffsetX: 6,
			subMenusSubOffsetY: -6
		});
	});
</script>
</body>
</html>