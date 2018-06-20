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
            'Theme' => array(__('Theme', 'guild-network'), 'dark', 'light'),
            'TabVerticalOffset' => array(__('Tab offset', 'guild-network'), '80'),
            'TabZIndex' => array(__('Tab z-index', 'guild-network'), '10'),
            'HandlePages' => array(__('Exclusive page handling', 'guild-network'), 'protect', 'ignore'),
            'HandlePosts' => array(__('Exclusive post handling', 'guild-network'), 'protect single post per page', 'protect everywhere', 'ignore'),
            'ExclusiveCategory' => array(__('Exclusive content category', 'guild-network'), 'Guild Exclusive'),
            'ExclusiveTag' => array(__('Exclusive content tag name', 'guild-network'), 'guild-exclusive'),
            'AdClasses' => array(__('Ad classes', 'guild-network'), 'adsbygoogle'),
            'AdIds' => array(__('Ad IDs', 'guild-network'), ''),
            'AdTags' => array(__('Ad tags', 'guild-network'), ''),
            // 'GuildServerUrl' => array(__('Guild server (test-only)', 'guild-network'), ''),
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
      global $page;

      $siteCode = $this->getOption('SiteCode');
      if ($siteCode) {
        $serverUrl = 'https://guild.network/e1/embed.js';
        if ('' !== $this->getOption('GuildServerUrl', '')) {
          $serverUrl = $this->getOption('GuildServerUrl');
        }
        echo '<script defer src="' . $serverUrl . '"></script>';
        echo '<script>';
        echo '  window.guild = { ';
        echo 'site: \'' . $siteCode . '\', ';
        if (is_page() && $this->isExclusive($page)) {
          if ('protect' == $this->getOption('HandlePages', 'protect')) {
            echo 'exclusive: true, ';
          }
        } 
        if ('dark' !== $this->getOption('Theme', '')) {
          echo 'theme: \'' . $this->getOption('Theme', '') . '\', ';
        }
        if ('80' !== $this->getOption('TabVerticalOffset', '')) {
          echo 'tabVerticalOffset: \'' . $this->getOption('TabVerticalOffset', '') . '\', ';
        }
        if ('10' !== $this->getOption('TabZIndex', '')) {
          echo 'tabZIndex: \'' . $this->getOption('TabZIndex', '') . '\', ';
        }
        if (!empty(trim($this->getOption('AdClasses', '')))) {
          echo 'adClasses: \'' . trim($this->getOption('AdClasses', '')) . '\', ';
        }
        if (!empty(trim($this->getOption('AdIds', '')))) {
          echo 'adIds: \'' . trim($this->getOption('AdIds', '')) . '\', ';
        }
        if (!empty(trim($this->getOption('AdTags', '')))) {
          echo 'adTags: \'' . trim($this->getOption('AdTags', '')) . '\', ';
        }
        $found = false;
        if (!empty(trim($this->getOption('ExclusiveCategory', '')))) {
          $category_id = get_cat_ID($this->getOption('ExclusiveCategory', ''));
          $category_link = get_category_link( $category_id ); 
          $postsByCat = $this->getPostTitlesByCategory($category_id);
          if (!empty($category_link) && count($postsByCat) > 0) {
            $found = true;
            echo 'exclusivePageUrl: \'' . $category_link . '\', ';
            $this->echoExclusiveTitles($postsByCat);
          } 
        } 
        if (!$found && !empty(trim($this->getOption('ExclusiveTag', '')))) {
          $tag_id = $this->get_tag_ID(trim($this->getOption('ExclusiveTag', '')));
          if (!empty($tag_id)) {
            $tag_link = get_tag_link($tag_id);
            $postsByTag = $this->getPostTitlesByTag($tag_id);
            if (!empty($tag_link) && count($postsByTag) > 0) {
              echo 'exclusivePageUrl: \'' . $tag_link . '\', ';
              $this->echoExclusiveTitles($postsByTag);
            }
          }
        }
        echo ' };';
        echo '</script>';  
      }
    }

    private function getPostTitlesByCategory($categoryId) {
      $args = array(
        'posts_per_page'   => 3,
        'category'         => $categoryId,
        'orderby'          => 'date',
        'order'            => 'DESC'
      );
      $posts = get_posts($args);
      $result = array();
      foreach ($posts as $post) {
        $result[] = get_the_title($post->ID);
      }
      return $result;
    }

    private function getPostTitlesByTag($tagId) {
      $args = array(
        'posts_per_page'   => 3,
        'tag_id'           => $tagId,
        'orderby'          => 'date',
        'order'            => 'DESC'
      );
      $posts = get_posts($args);
      $result = array();
      foreach ($posts as $post) {
        $result[] = get_the_title($post->ID);
      }
      return $result;
    }

    private function echoExclusiveTitles($titles) {
      echo 'exclusiveTitles: [';
      foreach ($titles as $title) {
        echo '\'' . $title . '\', ';
      }
      echo ']';
    }

    private function get_tag_ID($tag_name) {
      $tag = get_term_by('name', $tag_name, 'post_tag');
      if ($tag) {
        return $tag->term_id;
      } else {
        return 0;
      }
    }

    private function get_top_parent_page_id() { 
      global $post; 
      if ($post->ancestors) { 
        return end($post->ancestors); 
      } else { 
        return null; 
      } 
    }

    public function addGuildPostClass($classes) {
      global $post;
      if ($post) {
        $postId = $post->ID;
        if ($this->isExclusive($postId)) {
          $setting = $this->getOption('HandlePosts', 'protect single post per page');
          if ('ignore' !== $setting && is_single($postId)) {
            $classes[] = 'guild-protect-parent';              
          } else if ('protect everywhere' == $setting) {
            $classes[] = 'guild-protect';            
          } else {
            $classes[] = 'guild-not-protect';            
          }
        } else {
          $classes[] = 'guild-not-exclusive';            
        }
      } else {
        $classes[] = 'guild-no-post';            
      }
      return $classes;
    }

    private function isExclusive($id) {
      if (!id) {
        return false;
      }
      $exclusiveCategory = $this->getOption('ExclusiveCategory', '');
      $exclusiveTag = $this->getOption('ExclusiveTag', '');
      return in_category($exclusiveCategory, $id) || has_tag($exclusiveTag, $id);
    }

}
