<?php 
$importer_controller 	= new Moove_Importer_Controller();
$plugin_details 		= $importer_controller->get_plugin_details( 'import-xml-feed' );
?>
<div class="moove-importer-plugins-info-boxes">

	<?php ob_start(); ?>
	<div class="m-plugin-box m-plugin-box-highlighted">
		<div class="box-header">
			<h4>Premium Add-On</h4>
		</div>
		<!--  .box-header -->
		<div class="box-content">
			<ul class="plugin-features">
				<li>Save & Load templates</li>
				<li>Support for tag attributes</li>
				<li>Custom Fields & ACF support</li>
			</ul>
			<hr />
			<strong>Buy Now for only <span>£49</span></strong>
			<a href="https://www.mooveagency.com/wordpress-plugins/import-xml-feed/" target="_blank" class="plugin-buy-now-btn">Buy Now</a>

		</div>
		<!--  .box-content -->
	</div>
	<!--  .m-plugin-box -->
	<?php echo $premium_box = apply_filters( 'importer_premium_section', ob_get_clean() ); ?>


	<?php $support_class = $premium_box ? '' : 'm-plugin-box-highlighted'; ?>
  
	<div class="m-plugin-box m-plugin-box-support <?php echo $support_class; ?>">
		<div class="box-header">
			<h4>Need Support or New Feature?</h4>
		</div>
		<!--  .box-header -->
		<div class="box-content">
			<?php 
				$forum_link = apply_filters( 'importer_forum_section_link', 'https://support.mooveagency.com/forum/import-xml-feed/' );
			?>
      <div class="xml-faq-forum-content">
        <p><span class="xml-chevron-left">&#8250;</span> Check the <a href="<?php echo admin_url( 'options-general.php?page=moove-importer&tab=plugin_documentation' ); ?>">Documentation section</a> to find out more.</p>
        <p><span class="xml-chevron-left">&#8250;</span> Create a new support ticket or request new features in our <a href="<?php echo $forum_link; ?>" target="_blank">Support Forum</a></p>
      </div>
      <!--  .xml-faq-forum-content -->
      <span class="xml-review-container" >
        <a href="<?php echo $forum_link; ?>#new-post" target="_blank" class="xml-review-bnt ">Create a new support ticket</a>
      </span>
		</div>
		<!--  .box-content -->
	</div>
	<!--  .m-plugin-box -->
	
	<div class="m-plugin-box">
		<div class="box-header">
			<h4>Help to improve this plugin!</h4>
		</div>
		<!--  .box-header -->
		<div class="box-content">
			<p>Enjoyed this plugin? <br />You can help by <a href="https://wordpress.org/support/plugin/import-xml-feed/reviews/?rate=5#new-post" target="_blank">rating this plugin on wordpress.org.</a></p>
			<hr />
			<?php if ( $plugin_details ) : ?>
				<div class="plugin-stats">
					<div class="plugin-downloads">
						Downloads: <strong><?php echo number_format( $plugin_details->downloaded, 0, '', ','); ?></strong>
					</div>
					<!--  .plugin-downloads -->
					<div class="plugin-active-installs">
						Active installations: <strong><?php echo number_format( $plugin_details->active_installs, 0, '', ','); ?>+</strong>
					</div>
					<!--  .plugin-downloads -->
					<div class="plugin-rating">
						<?php 
						$rating_val = $plugin_details->rating * 5 / 100;
						if ( $rating_val > 0 ) :
							$args = array(
								'rating' 	=> $rating_val,
								'number' 	=> $plugin_details->num_ratings,
								'echo'		=> false
							);
							$rating = wp_star_rating( $args );
						endif;
						?>
						<?php if ( $rating ) : ?>
							<?php echo $rating; ?>
						<?php endif; ?>
					</div>
					<!--  .plugin-rating -->
				</div>
				<!--  .plugin-stats -->
			<?php endif; ?>
		</div>
		<!--  .box-content -->
	</div>
	<!--  .m-plugin-box -->

	
	
</div>
<!--  .moove-plugins-info-boxes -->