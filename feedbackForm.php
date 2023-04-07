<?php
/*
Plugin Name: Feedback_Form
Description: Adds a feedback form to pages and posts.
Version: 1.0
Author: AHABBANE
*/


add_action('init', 'register_feedback_form_shortcode');
add_action('wp_enqueue_scripts', 'feedback_styles');

function register_feedback_form_shortcode()
{
    add_shortcode('feedback_form', 'my_feedback_form');
}

function feedback_styles()
{
    wp_enqueue_style('feedback-form', plugins_url('css/feedback.css', __FILE__));
}


// Add the feedback form shortcode
function my_feedback_form()
{

    // Check if a form has been submitted
    if (isset($_POST['submit_feedback'])) {
        $note = intval($_POST['note']);
        $message = sanitize_textarea_field($_POST['message']);
        $page_id = intval($_POST['page_id']);

        // Insert the feedback data into the database
        global $wpdb;
        $table_name = $wpdb->prefix . 'feedbackForm';
        $data = array(
            'note' => $note,
            'message' => $message,
            'page_id' => $page_id
        );
        $wpdb->insert($table_name, $data);
?>
    <?php
    }
    // Display the feedback form
    $page_id = get_the_ID();
    ?>
    <div class="feedback">
        <h2>Leave a feedback</h2>
        <form method="POST" class="wpforms-field-container">
            <input type="hidden" name="page_id" id="id" value="<?php echo "$page_id"; ?>">
            <div class="note" style="margin-bottom:25px;">
                <label for="note">Note</label>
                <div style="display:flex;gap:10px;">
                    <input type="range" min="0" max="5" name="note" onChange="rangeSlide(this.value)">
                    <span id="rangeValue" style="font-size:20px;">3</span>
                </div>

            </div>
            <div class="message">
                <label for="message">Message</label>
                <textarea class="wpforms-field-large" name="message" id="message" required></textarea>
                <button type="submit" name="submit_feedback" style="color:black;">Submit</button>
            </div>
        </form>
    </div>
    <script type="text/javascript">
        function rangeSlide(value) {
            document.getElementById('rangeValue').innerHTML = value;
        }
    </script>
<?php

}

// Create the feedback table in the database
function feedback_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'feedbackForm';
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        note tinyint(1) NOT NULL,
        message text NOT NULL,
        page_id mediumint(9) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'feedback_table');
