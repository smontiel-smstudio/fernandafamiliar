<?php
/**
 * Moove_Importer_Options File Doc Comment
 *
 * @category  Moove_Importer_Options
 * @package   moove-feed-importer
 * @author    Gaspar Nemes
 */

/**
 * Moove_Importer_Options Class Doc Comment
 *
 * @category Class
 * @package  Moove_Importer_Options
 * @author   Gaspar Nemes
 */
class Moove_Importer_Options
{
    /**
     * Construct
     */
    function __construct()
    {
        add_action('admin_menu', array(&$this, 'moove_importer_admin_menu'));
        add_action('moove_importer_addons_tab_content', array(&$this, 'moove_importer_load_active_tab_view'));
        add_action('moove_importer_buttons', array(&$this, 'moove_importer_buttons'), 10);
        add_action('plugins_loaded', array($this, 'load_languages'));

    }

    function load_languages()
    {

        load_plugin_textdomain('import-xml-feed', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }

    /**
     * Moove load active tab view
     *
     * @return  void
     */
    function moove_importer_load_active_tab_view($data)
    {

        if ($data['tab'] == 'feed_importer') :
            echo Moove_Importer_View::load('moove.admin.settings.post_type', $data['data']);
        elseif ($data['tab'] == 'plugin_addons'):
            echo Moove_Importer_View::load('moove.admin.settings.addons', null);
        elseif ($data['tab'] == 'plugin_documentation'):
            echo Moove_Importer_View::load('moove.admin.settings.documentation', null);
        elseif ($data['tab'] == 'licence'):
            echo Moove_Importer_View::load('moove.admin.settings.licence', null);
        endif;
    }

    /**
     * Moove feed importer page added to settings
     *
     * @return  void
     */
    function moove_importer_admin_menu()
    {
        add_options_page('Feed importer', __('Moove feed importer','import-xml-feed'), 'manage_options', 'moove-importer', array(&$this, 'moove_importer_settings_page'));
    }

    /**
     * Settings page registration
     *
     * @return void
     */
    function moove_importer_settings_page()
    {
        $post_types = get_post_types(array('public' => true));
        unset($post_types['attachment']);
        $data = array();
        if (count($post_types)) :
            foreach ($post_types as $cpt) :
                $taxonomies = get_object_taxonomies($cpt, 'object');
                $data[$cpt] = array(
                    'post_type' => $cpt,
                    'taxonomies' => $taxonomies,
                );
            endforeach;
        endif;
        echo Moove_Importer_View::load('moove.admin.settings.settings_page', $data);
    }

    function moove_importer_buttons()
    {
        ?>
        <a href="#" class="button button-primary moove-start-import-feed"><?php _e('START IMPORT', 'import-xml-feed'); ?></a>
        <?php
    }
}

$moove_importer_options = new Moove_Importer_Options();
