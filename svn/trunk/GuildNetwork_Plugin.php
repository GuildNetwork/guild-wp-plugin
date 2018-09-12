<?php

include_once 'GuildNetwork_LifeCycle.php';
include_once 'GuildNetwork_Widgets.php';

class GuildNetwork_Plugin extends GuildNetwork_LifeCycle
{
    /**
     * See: http://plugin.michael-simpson.com/?page_id=31
     * @return array of option meta data.
     */
    public function getOptionMetaData()
    {
        //  http://plugin.michael-simpson.com/?page_id=31
        return array(
            'SiteCode' => array(__('Site Code', 'guild-network')),
            'FilmStripCssPosition' => array(__('FilmStrip widget CSS position', 'guild-network'), 'none', 'before selector', 'after selector', 'first child of selector', 'last child of selector'),
            'FilmStripCss' => array(__('FilmStrip CSS selector', 'guild-network')),
            'ExplorerCssPosition' => array(__('Explorer widget CSS position', 'guild-network'), 'none', 'before selector', 'after selector', 'first child of selector', 'last child of selector'),
            'ExplorerCss' => array(__('Explorer CSS selector', 'guild-network')),
            //'_version' => array('Installed Version'), // Leave this one commented-out. Uncomment to test upgrades.
            // 'Theme' => array(__('Theme', 'guild-network'), 'dark', 'light'),
            // 'TabVerticalOffset' => array(__('Tab offset', 'guild-network'), '80'),
            // 'TabZIndex' => array(__('Tab z-index', 'guild-network'), '10'),
            // 'ExclusiveCategory' => array(__('Exclusive content category', 'guild-network'), 'Guild Exclusive'),
            // 'ExclusiveTag' => array(__('Exclusive content tag name', 'guild-network'), 'guild-exclusive'),
            // 'HandlePages' => array(__('Exclusive page handling', 'guild-network'), 'protect', 'ignore'),
            // 'HandlePosts' => array(__('Exclusive posts', 'guild-network'), 'protect single post per page', 'protect everywhere', 'ignore'),
            // 'AdClasses' => array(__('Ad classes', 'guild-network'), 'adsbygoogle'),
            // 'AdIds' => array(__('Ad IDs', 'guild-network'), ''),
            // 'AdTags' => array(__('Ad tags', 'guild-network'), ''),
            'GuildServerUrl' => array(__('Guild server (test-only)', 'guild-network'), ''),
        );
    }

//    protected function getOptionValueI18nString($optionValue) {
    //        $i18nValue = parent::getOptionValueI18nString($optionValue);
    //        return $i18nValue;
    //    }

    protected function initOptions()
    {
        $options = $this->getOptionMetaData();
        if (!empty($options)) {
            foreach ($options as $key => $arr) {
                if (is_array($arr) && count($arr) > 1) {
                    $this->addOption($key, $arr[1]);
                }
            }
        }
    }

    public function getPluginDisplayName()
    {
        return 'Guild Network';
    }

    protected function getMainPluginFileName()
    {
        return 'guild-network.php';
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=101
     * Called by install() to create any database tables if needed.
     * Best Practice:
     * (1) Prefix all table names with $wpdb->prefix
     * (2) make table names lower case only
     * @return void
     */
    protected function installDatabaseTables()
    {
        //        global $wpdb;
        //        $tableName = $this->prefixTableName('mytable');
        //        $wpdb->query("CREATE TABLE IF NOT EXISTS `$tableName` (
        //            `id` INTEGER NOT NULL");
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=101
     * Drop plugin-created tables on uninstall.
     * @return void
     */
    protected function unInstallDatabaseTables()
    {
        //        global $wpdb;
        //        $tableName = $this->prefixTableName('mytable');
        //        $wpdb->query("DROP TABLE IF EXISTS `$tableName`");
    }

    /**
     * Perform actions when upgrading from version X to version Y
     * See: http://plugin.michael-simpson.com/?page_id=35
     * @return void
     */
    public function upgrade()
    {
    }

    public function addActionsAndFilters()
    {
        // Add options administration page
        // http://plugin.michael-simpson.com/?page_id=47
        add_action('admin_menu', array(&$this, 'addSettingsSubMenuPage'));

        // Example adding a script & style just for the options administration page
        // http://plugin.michael-simpson.com/?page_id=47
        //        if (strpos($_SERVER['REQUEST_URI'], $this->getSettingsSlug()) !== false) {
        //            wp_enqueue_script('my-script', plugins_url('/js/my-script.js', __FILE__));
        //            wp_enqueue_style('my-style', plugins_url('/css/my-style.css', __FILE__));
        //        }

        // Add Actions & Filters
        // http://plugin.michael-simpson.com/?page_id=37

        add_action('wp_head', array(&$this, 'addGuildPageHeader'));

        // Adding scripts & styles to all pages
        // Examples:
        //        wp_enqueue_script('jquery');
        //        wp_enqueue_style('my-style', plugins_url('/css/my-style.css', __FILE__));
        //        wp_enqueue_script('my-script', plugins_url('/js/my-script.js', __FILE__));

        // Register short codes
        // http://plugin.michael-simpson.com/?page_id=39

        add_shortcode('guild-film-strip', array($this, 'doFilmStripShortcode'));
        add_shortcode('guild-explorer', array($this, 'doExplorerShortcode'));

        // Register AJAX hooks
        // http://plugin.michael-simpson.com/?page_id=41

        // Ensure pages can be configured with categories and tags
        add_action('init', array(&$this, 'add_taxonomies_to_pages'));

        $prefix = is_network_admin() ? 'network_admin_' : '';
        $plugin_file = plugin_basename($this->getPluginDir() . DIRECTORY_SEPARATOR . $this->getMainPluginFileName()); //plugin_basename( $this->getMainPluginFileName() );
        $this->guildLog('Adding filter ' . "{$prefix}plugin_action_links_{$plugin_file}");
        add_filter("{$prefix}plugin_action_links_{$plugin_file}", array(&$this, 'onActionLinks'));
    }

    public function onActionLinks($links)
    {
        $this->guildLog('onActionLinks ' . admin_url('options-general.php?page=GuildNetwork_PluginSettings'));
        $mylinks = array('<a href="' . admin_url('options-general.php?page=GuildNetwork_PluginSettings') . '">Settings</a>');
        return array_merge($links, $mylinks);
    }

    public function doFilmStripShortcode()
    {
        return '<div class="guild-widget guild-film-strip guild-shortcode"></div>';
    }

    public function doExplorerShortcode()
    {
        return '<div class="guild-widget guild-explorer guild-shortcode"></div>';
    }

    public function add_taxonomies_to_pages()
    {
        register_taxonomy_for_object_type('post_tag', 'page');
        register_taxonomy_for_object_type('category', 'page');
    }

    /* determine whether post has a featured image, if not, find the first image inside the post content, $size passes the thumbnail size, $url determines whether to return a URL or a full image tag*/
    /* adapted from http://www.amberweinberg.com/wordpress-find-featured-image-or-first-image-in-post-find-dimensions-id-by-url/ */

    public function getPostImage($post)
    {
        ob_start();
        ob_end_clean();

        /*If there's a featured image, show it*/

        if (has_post_thumbnail($post)) {
            $images = wp_get_attachment_image_src(get_post_thumbnail_id($post), 'single-post-thumbnail');
            return $images[0];
        } else {
            $content = $post->post_content;
            $first_img = '';
            $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $content, $matches);
            $first_img = $matches[1][0];

            /*No featured image, so we get the first image inside the post content*/

            if ($first_img) {
                return $first_img;
            } else {
                return null;
            }
        }
    }

    public function addGuildPageHeader()
    {
        global $page;
        global $post;
        if ($post) {
            $postId = $post->ID;
            $siteCode = $this->getOption('SiteCode');
            if ($siteCode) {
                $serverUrl = 'https://guild.network/e1/embed.js';
                $override = $this->getOption('GuildServerUrl', '');
                if (isset($override) && trim($override) !== '') {
                    $serverUrl = trim($override);
                }
                echo "\n" . '<script defer src="' . $serverUrl . '"></script>' . "\n";
                echo '<script>';
                echo '  window.guild = { ';
                echo 'site: \'' . $siteCode . '\', ';
                $filmStripCssPosition = $this->getOption('FilmStripCssPosition', 'none');
                $filmStripCss = $this->getOption('FilmStripCss', '');
                if ($filmStripCssPosition !== 'none' && !empty($filmStripCss)) {
                    echo 'filmStrip: { position: "' . $filmStripCssPosition . '", selector: "' . $filmStripCss . '" }';
                }
                $explorerCssPosition = $this->getOption('ExplorerCssPosition', 'none');
                $explorerCss = $this->getOption('ExplorerCss', '');
                if ($explorerCssPosition !== 'none' && !empty($explorerCss)) {
                    echo 'explorer: { position: "' . $explorerCssPosition . '", selector: "' . $explorerCss . '" }';
                }
                echo ' };';
                echo '</script>' . "\n";
            }
        }
    }
}
