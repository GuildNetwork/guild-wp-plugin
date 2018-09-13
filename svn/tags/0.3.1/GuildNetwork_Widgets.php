<?php

class Guild_Explorer_Widget extends WP_Widget
{
    /**
     * based on https://www.wpexplorer.com/create-widget-plugin-wordpress/

     * The widget simply creates a container that the Guild script will populate
     * after the page loads.
     */

    public function __construct()
    {
        parent::__construct(
            'guild_explorer_widget',
            __('Guild Explorer Widget', 'text_domain'),
            array('customize_selective_refresh' => true)
        );
    }

    public function form($instance)
    {
        $defaults = array(
            'title' => '',
        );
        extract(wp_parse_args((array) $instance, $defaults));
        ?>
    <?php // Widget Title ?>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Widget Title', 'text_domain');?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
    </p>
    <?php
}

    public function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = isset($new_instance['title']) ? wp_strip_all_tags($new_instance['title']) : '';
        return $instance;
    }

    public function widget($args, $instance)
    {
        extract($args);
        $title = isset($instance['title']) ? apply_filters('widget_title', $instance['title']) : '';
        echo $before_widget;
        if ($title) {
            echo $before_title . $title . $after_title;
        }
        echo '<div class="guild-widget guild-explorer">';
        echo '</div>';
        echo $after_widget;
    }
}

class Guild_FilmStrip_Widget extends WP_Widget
{
    /**
     * based on https://www.wpexplorer.com/create-widget-plugin-wordpress/

     * The widget simply creates a container that the Guild script will populate
     * after the page loads.
     */

    public function __construct()
    {
        parent::__construct(
            'guild_filmstrip_widget',
            __('Guild FilmStrip Widget', 'text_domain'),
            array('customize_selective_refresh' => true)
        );
    }

    public function form($instance)
    {
        $defaults = array(
            'title' => '',
        );
        extract(wp_parse_args((array) $instance, $defaults));
        ?>
    <?php // Widget Title ?>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Widget Title', 'text_domain');?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
    </p>
    <?php
}

    public function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = isset($new_instance['title']) ? wp_strip_all_tags($new_instance['title']) : '';
        return $instance;
    }

    public function widget($args, $instance)
    {
        extract($args);
        $title = isset($instance['title']) ? apply_filters('widget_title', $instance['title']) : '';
        echo $before_widget;
        if ($title) {
            echo $before_title . $title . $after_title;
        }
        echo '<div class="guild-widget guild-film-strip">';
        echo '</div>';
        echo $after_widget;
    }
}

function register_guild_widgets()
{
    register_widget('Guild_Explorer_Widget');
    register_widget('Guild_FilmStrip_Widget');
}

add_action('widgets_init', 'register_guild_widgets');
