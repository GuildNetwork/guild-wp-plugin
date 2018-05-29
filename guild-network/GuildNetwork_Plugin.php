<?php


include_once('GuildNetwork_LifeCycle.php');

class GuildNetwork_Plugin extends GuildNetwork_LifeCycle {

    /**
     * See: http://plugin.michael-simpson.com/?page_id=31
     * @return array of option meta data.
     */
    public function getOptionMetaData() {
        //  http://plugin.michael-simpson.com/?page_id=31
        return array(
            //'_version' => array('Installed Version'), // Leave this one commented-out. Uncomment to test upgrades.
            'SiteCode' => array(__('Site Code', 'guild-network')),
            'HandlePages' => array(__('Exclusive page handling', 'guild-network'), 'protect', 'ignore'),
            'HandlePosts' => array(__('Exclusive post handling', 'guild-network'), 'protect single post per page', 'protect everywhere', 'ignore'),
            'ExclusiveCategory' => array(__('Exclusive content category', 'guild-network'), 'Guild Exclusive'),
            'ExclusiveTag' => array(__('Exclusive content tag name', 'guild-network'), 'guild-exclusive'),
            'AdRemoval' => array(__('Remove ads for pass holders', 'guild-network'), 'false', 'true'),
            'AdClasses' => array(__('Ad classes', 'guild-network'), 'adsbygoogle'),
            'AdDivIds' => array(__('Ad DIV IDs', 'guild-network'), ''),
            'AdTags' => array(__('Ad tags', 'guild-network'), ''),
        );
    }

//    protected function getOptionValueI18nString($optionValue) {
//        $i18nValue = parent::getOptionValueI18nString($optionValue);
//        return $i18nValue;
//    }

    protected function initOptions() {
        $options = $this->getOptionMetaData();
        if (!empty($options)) {
            foreach ($options as $key => $arr) {
                if (is_array($arr) && count($arr) > 1) {
                    $this->addOption($key, $arr[1]);
                }
            }
        }
    }

    public function getPluginDisplayName() {
        return 'Guild Network';
    }

    protected function getMainPluginFileName() {
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
    protected function installDatabaseTables() {
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
    protected function unInstallDatabaseTables() {
        //        global $wpdb;
        //        $tableName = $this->prefixTableName('mytable');
        //        $wpdb->query("DROP TABLE IF EXISTS `$tableName`");
    }


    /**
     * Perform actions when upgrading from version X to version Y
     * See: http://plugin.michael-simpson.com/?page_id=35
     * @return void
     */
    public function upgrade() {
    }

    public function addActionsAndFilters() {
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

        add_action( 'wp_head', array(&$this, 'addGuildPageHeader'));
        add_filter( 'post_class', array(&$this, 'addGuildPostClass'));


        // Adding scripts & styles to all pages
        // Examples:
        //        wp_enqueue_script('jquery');
        //        wp_enqueue_style('my-style', plugins_url('/css/my-style.css', __FILE__));
        //        wp_enqueue_script('my-script', plugins_url('/js/my-script.js', __FILE__));


        // Register short codes
        // http://plugin.michael-simpson.com/?page_id=39


        // Register AJAX hooks
        // http://plugin.michael-simpson.com/?page_id=41

    }

    public function addGuildPageHeader() {
      $siteCode = $this->getOption('SiteCode');
      if ($siteCode) {
        echo '<!-- Guild -->';
        echo '<script async src="https://guild.network/guild-embed.js"></script>';
        echo '<script>';
        echo '  window.guild = { ';
        echo 'site: \'' . $siteCode . '\', ';
        $page_id = get_queried_object_id();
        if ($this->isExclusive($page_id)) {
          if ('protect' == $this->getOption('HandlePages', 'protect')) {
            echo 'exclusive: true, ';
          }
        } 
        if ('true' == $this->getOption('AdRemoval')) {
          echo 'adRemoval:  true, ';
          if ('' !== $this->getOption('AdClasses', '')) {
            echo 'adClasses: \'' . $this->getOption('AdClasses', '') . '\', ';
          }
          if ('' !== $this->getOption('AdDivIds', '')) {
            echo 'adDivIds: \'' . $this->getOption('AdDivIds', '') . '\', ';
          }
          if ('' !== $this->getOption('AdDivIds', '')) {
            echo 'adTags: \'' . $this->getOption('AdTags', '') . '\', ';
          }
        }
        echo ' };';
        echo '</script>';  
      }
    }

    public function addGuildPostClass($classes) {
      $this->guildLog("Checking post classes");
      $post_id = get_queried_object_id();
      if ($post_id) {
        if ($this->isExclusive($post_id)) {
          $setting = $this->getOption('HandlePosts', 'protect single post per page');
          if ('protect single post per page' == $setting) {
            if (is_single($post_id)) {
              $classes[] = 'guild-protect-post-single';              
            }
          } else if ($setting == 'protect everywhere') {
            $classes[] = 'guild-protect-post-feed';
          }
        }    
      }
      return $classes;
    }

    private function isExclusive($id) {
      $exclusiveCategory = $this->getOption('ExclusiveCategory', '');
      $exclusiveTag = $this->getOption('ExclusiveTag', '');
      return in_category($exclusiveCategory, $id) || has_tag($exclusiveTag, $id);
    }

}
