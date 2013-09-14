<?php

    /*
     * Grab the settings from $instance and fill out default
     * values as needed.
     */
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
    $first_name = empty($instance['first_name']) ? '' : $instance['first_name'];
    $last_name = empty($instance['last_name']) ? '' : $instance['last_name'];
    if( is_array( $instance['sailthru_list'] ) )
    {
        $sailthru_list = implode(',', $instance['sailthru_list'] );
    } else {
        $sailthru_list = $instance['sailthru_list'];
    }


    // display options
    $show_first_name = (isset($instance['show_first_name']) && $instance['show_first_name']) ? true : false;
    $show_last_name = (isset($instance['show_last_name']) && $instance['show_last_name']) ? true : false;

    // nonce
    $nonce = wp_create_nonce("add_subscriber_nonce");

 ?>
 <div class="sailthru-signup-widget">
     <div class="sailthru_form">

        <?php
            // title
            if (!empty($title)) {
                if(!isset($before_title)) {
                    $before_title = '';
                }
                if(!isset($after_title)) {
                    $after_title = '';
                }
                echo $before_title . esc_html(trim($title)) . $after_title;
            }
        ?>
        <div id="sailthru-add-subscriber-errors"></div>
        <form role="form" class="newsletter" id="sailthru-add-subscriber-form" novalidate="novalidate" method="post">
             <input type="hidden" name="sailthru_nonce" value="<?php echo $nonce; ?>" />
             <input type="hidden" name="sailthru_email_list" value="<?php echo esc_attr($sailthru_list); ?>" />
             <input type="hidden" name="action" value="add_subscriber" />
             <input type="hidden" name="vars[source]" value="<?php bloginfo('url'); ?>" />
            <div class="input-group">
                <?php if( $show_first_name ) { ?>
                    <div class="sailthru_form_input">
                        <label for="sailthru_first_name">First Name:</label>
                        <input type="text" name="first_name" id="sailthru_first_name" value="" class="form-control" placeholder="First Name"/>
                    </div>
                <?php } ?>
                <?php if( $show_last_name ) { ?>
                    <div class="sailthru_form_input">
                        <input type="text" name="last_name" id="sailthru_last_name" value="" class="form-control" placeholder="Last Name"/>
                    </div>
                <?php } ?>
                <input type="text" class="form-control" name="email" id="sailthru_email" placeholder="Enter your email">
                <span class="input-group-btn">
                <input class="btn btn-reverse" type="submit" value="SUBMIT">
                </span>
            </div>
        </form>
    </div>
</div>
