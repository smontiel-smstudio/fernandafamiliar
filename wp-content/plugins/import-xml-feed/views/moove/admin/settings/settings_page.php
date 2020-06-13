<div class="wrap moove-importer-plugin-wrap" id="importer-settings-cnt">
	<h1><?php _e('Feed Importer','import-xml-feed'); ?></h1>
    <?php
        $current_tab_feed = isset( $_GET[ 'tab' ] ) ? sanitize_text_field( wp_unslash( $_GET[ 'tab' ]  ) ) : '';
        if( isset( $current_tab_feed ) && $current_tab_feed !== '' ) {
            $active_tab = $current_tab_feed;
        } else {
            $active_tab = "feed_importer";
        } // end if
    ?>
    <br />
    <div class="importer-tab-section-cnt">
        <?php do_action('xml_premium_update_alert'); ?>
        <h2 class="nav-tab-wrapper">
            <?php ob_start(); ?>

            <a href="?page=moove-importer&tab=feed_importer" class="nav-tab <?php echo $active_tab == 'feed_importer' || $active_tab === 'template_view' ? 'nav-tab-active' : ''; ?>">
                <?php _e('Feed Import','import-xml-feed'); ?>
            </a>
            <a href="?page=moove-importer&tab=licence" class="nav-tab <?php echo $active_tab == 'licence' ? 'nav-tab-active' : ''; ?>">
                <?php _e('Licence Manager','import-xml-feed'); ?>
            </a>
            <?php
                $tabs = ob_get_clean();
                echo $tabs;
                do_action( 'moove_importer_addons_tabs', $tabs, $active_tab );
            ?>
            <a href="?page=moove-importer&tab=plugin_documentation" class="nav-tab <?php echo $active_tab == 'plugin_documentation' ? 'nav-tab-active' : ''; ?>">
                <?php _e('Documentation','import-xml-feed'); ?>
            </a>

        </h2>
        <div class="moove-form-container <?php echo $active_tab; ?> <?php echo $active_tab == 'template_view' ? 'feed_importer' : ''; ?>">
            <?php
                $content = array(
                    'tab'   => $active_tab,
                    'data'  => $data
                );
                do_action( 'moove_importer_addons_tab_content', $content );
            ?>
        </div>
        <!-- moove-form-container -->

    </div>
    <!--  .importer-tab-section-cnt -->

    <?php 
        $view_cnt = new Moove_Importer_View();
        echo $view_cnt->load( 'moove.admin.settings.plugin_boxes', array() );
    ?>

</div>
<!-- wrap -->