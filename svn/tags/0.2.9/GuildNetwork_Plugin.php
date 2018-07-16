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
            // 'Theme' => array(__('Theme', 'guild-network'), 'dark', 'light'),
            // 'TabVerticalOffset' => array(__('Tab offset', 'guild-network'), '80'),
            // 'TabZIndex' => array(__('Tab z-index', 'guild-network'), '10'),
            'ExclusiveCategory' => array(__('Exclusive content category', 'guild-network'), 'Guild Exclusive'),
            'ExclusiveTag' => array(__('Exclusive content tag name', 'guild-network'), 'guild-exclusive'),
            // 'HandlePages' => array(__('Exclusive page handling', 'guild-network'), 'protect', 'ignore'),
            'HandlePosts' => array(__('Exclusive posts', 'guild-network'), 'protect single post per page', 'protect everywhere', 'ignore'),
            // 'AdClasses' => array(__('Ad classes', 'guild-network'), 'adsbygoogle'),
            // 'AdIds' => array(__('Ad IDs', 'guild-network'), ''),
            // 'AdTags' => array(__('Ad tags', 'guild-network'), ''),
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

        // Ensure pages can be configured with categories and tags
        add_action( 'init', array(&$this, 'add_taxonomies_to_pages'));

        add_action( 'the_post', array(&$this, 'onPost'));

        add_filter( 'excerpt_length', array(&$this, 'customExcerptLength'), 499);

        $prefix = is_network_admin() ? 'network_admin_' : '';
        $plugin_file =  'guild-network/guild-network.php'; // $this->getPluginDir() . DIRECTORY_SEPARATOR . $this->getMainPluginFileName(); //plugin_basename( __FILE__ );
        $this->guildLog('Adding filter ' . "{$prefix}plugin_action_links_{$plugin_file}");
        add_filter( "{$prefix}plugin_action_links_{$plugin_file}", array(&$this, 'onActionLinks'));
    }

    public function onActionLinks( $links ) {
      $this->guildLog('onActionLinks ' . admin_url( 'options-general.php?page=GuildNetwork_PluginSettings' ));
      $mylinks = array('<a href="' . admin_url( 'options-general.php?page=GuildNetwork_PluginSettings' ) . '">Settings</a>');
      return array_merge( $links, $mylinks );
    }

    public function customExcerptLength($length) {
      return 50;
    }

    public function add_taxonomies_to_pages() {
      register_taxonomy_for_object_type( 'post_tag', 'page' );
      register_taxonomy_for_object_type( 'category', 'page' );
    }

    // public function onLoopStart($query) {
    //   if ($query->is_main_query()) {
    //     add_action( 'the_post', array(&$this, 'onPost') );
    //   }
    // }

    public function onPost($post) {
      if (in_the_loop() && is_main_query() && $post) {
        $postId = $post->ID;
        if ($this->isExclusive($postId)) {
          if (is_single()) {
            echo '<div class="guild-ex-banner"></div>';
          }
          $snippet = '<div class="guild-no-pass" style="display:none;margin-bottom:250px;"><h1 class="guild-snippet-title">' . get_the_title() . '</h1>' . "\n";
          $excerpt = $post->post_excerpt;
          if (empty($excerpt)) {
            $excerpt = wp_trim_words($post->post_content, 25, '...');
          }
          if (!empty($excerpt)) {
            $snippet = $snippet . '<p>' . $excerpt. '</p>' . "\n";
          }
          $imageUrl = $this->getPostImage($post);
          if (!empty($imageUrl)) {
            $snippet = $snippet . '<div class="guild-snippet-image" style="height:250px;background:linear-gradient(rgba(255,255,255,0.0), rgba(255,255,255,0.01),rgba(255,255,255,0.03),rgba(255,255,255,0.06),rgba(255,255,255,0.2),rgba(255,255,255,0.3),rgba(255,255,255,0.4),rgba(255,255,255,0.5),rgba(255,255,255,0.6),rgba(255,255,255,0.65),rgba(255,255,255,0.7),rgba(255,255,255,0.8),rgba(255,255,255,0.85),rgba(255,255,255,0.9),rgba(255,255,255,0.95),rgba(255,255,255,1)),url(' . $imageUrl . '); background-size: cover;)"></div>';  
          }
          $snippet = $snippet . '</div>' . "\n";
          $setting = $this->getOption('HandlePosts', 'protect single post per page');
          if ('ignore' !== $setting && is_single($postId)) {
            echo $snippet;
          } else if ('protect everywhere' == $setting) {
            echo $snippet;
          }
        }
      }
    }

   
  /* determine whether post has a featured image, if not, find the first image inside the post content, $size passes the thumbnail size, $url determines whether to return a URL or a full image tag*/
  /* adapted from http://www.amberweinberg.com/wordpress-find-featured-image-or-first-image-in-post-find-dimensions-id-by-url/ */
   
  public function getPostImage($post) {
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

    // public function onLoopEnd() {
    //   remove_action( 'the_post', array(&$this, 'onLoopEnd') ); 
    // }

    public function addGuildPageHeader() {
      global $page;
      global $post;
      if ($post) {
        $postId = $post->ID;
        if ((is_page() || $post->post_type == 'page') && $this->isExclusive($page)) {
          if ('protect' == $this->getOption('HandlePages', 'protect')) {
            echo '<meta name="guild-exclusive" content="page" />' . "\n";
          }
        } else if ($this->isExclusive($postId)) {
          $setting = $this->getOption('HandlePosts', 'protect single post per page');
          if ('ignore' !== $setting && is_single($postId)) {
            echo '<meta name="guild-exclusive" content="post" />' . "\n";
          }
        }
        $excerpt = $post->post_excerpt;
        if (empty($excerpt)) {
          $excerpt = wp_trim_words($post->post_content, 25);
        }
        if (!empty($excerpt)) {
          $excerpt = str_replace('"', '\'', $excerpt);
          echo '<meta name="guild-excerpt" content="' . $excerpt . '" />' . "\n";
        }
      }

      $siteCode = $this->getOption('SiteCode');
      if ($siteCode) {
        $serverUrl = 'https://guild.network/e1/embed.js';
        if ('' !== $this->getOption('GuildServerUrl', '')) {
          $serverUrl = $this->getOption('GuildServerUrl');
        }
        echo "\n" . '<script defer src="' . $serverUrl . '"></script>' . "\n";
        echo '<script>';
        echo '  window.guild = { ';
        echo 'site: \'' . $siteCode . '\', ';
        // echo 'isPage: ' . (is_page() ? 'true' : 'false') . ', ';
        // echo 'pageId: ' . (empty($page) || empty($page->ID) ? '\'none\'' : $page->ID) . ', ';
        // echo 'postId: ' . (empty($post) ? 'none' : $post->ID) . ', ';
        if ((is_page() || $post->post_type == 'page')) {
          if ($page && $page->ID) {
            echo 'exclusive: ' . ($this->isExclusive($page) ? 'true' : 'false') . ', ';
          } else if ($post) {
            echo 'exclusive: ' . ($this->isExclusive($post) ? 'true' : 'false') . ', ';
          } 
        } 
        // if ('dark' !== $this->getOption('Theme', '')) {
        //   echo 'theme: \'' . $this->getOption('Theme', '') . '\', ';
        // }
        // if ('80' !== $this->getOption('TabVerticalOffset', '')) {
        //   echo 'tabVerticalOffset: \'' . $this->getOption('TabVerticalOffset', '') . '\', ';
        // }
        // if ('10' !== $this->getOption('TabZIndex', '')) {
        //   echo 'tabZIndex: \'' . $this->getOption('TabZIndex', '') . '\', ';
        // }
        // if (!empty(trim($this->getOption('AdClasses', '')))) {
        //   echo 'adClasses: \'' . trim($this->getOption('AdClasses', '')) . '\', ';
        // }
        // if (!empty(trim($this->getOption('AdIds', '')))) {
        //   echo 'adIds: \'' . trim($this->getOption('AdIds', '')) . '\', ';
        // }
        // if (!empty(trim($this->getOption('AdTags', '')))) {
        //   echo 'adTags: \'' . trim($this->getOption('AdTags', '')) . '\', ';
        // }
        $found = false;
        if (!empty(trim($this->getOption('ExclusiveCategory', '')))) {
          $category_id = get_cat_ID($this->getOption('ExclusiveCategory', ''));
          $category_link = get_category_link( $category_id ); 
          $postsByCat = $this->getPostTitlesByCategory($category_id);
          if (!empty($category_link) && count($postsByCat) > 0) {
            $found = true;
            echo 'exclusivePageUrl: \'' . $category_link . '\', ';
            // $this->echoExclusiveTitles($postsByCat);
          } 
        } 
        if (!$found && !empty(trim($this->getOption('ExclusiveTag', '')))) {
          $tag_id = $this->get_tag_ID(trim($this->getOption('ExclusiveTag', '')));
          if (!empty($tag_id)) {
            $tag_link = get_tag_link($tag_id);
            $postsByTag = $this->getPostTitlesByTag($tag_id);
            if (!empty($tag_link) && count($postsByTag) > 0) {
              echo 'exclusivePageUrl: \'' . $tag_link . '\', ';
              // $this->echoExclusiveTitles($postsByTag);
            }
          }
        }
        echo ' };';
        echo '</script>' . "\n";  
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
