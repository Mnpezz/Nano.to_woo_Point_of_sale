<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

function woo_nanopay_pos_register_settings() {
    add_option('woo_nanopay_pos_nano_address', '');
    register_setting('woo_nanopay_pos_options_group', 'woo_nanopay_pos_nano_address', 'woo_nanopay_pos_callback');
}
add_action('admin_init', 'woo_nanopay_pos_register_settings');

function woo_nanopay_pos_register_options_page() {
    add_options_page('NanoPay POS Settings', 'NanoPay POS', 'manage_options', 'woo_nanopay_pos', 'woo_nanopay_pos_options_page');
}
add_action('admin_menu', 'woo_nanopay_pos_register_options_page');

function woo_nanopay_pos_options_page() {
    $pos_page_url = woo_nanopay_pos_create_page();
?>
    <div>
    <h2>NanoPay POS Settings</h2>
    <form method="post" action="options.php">
    <?php settings_fields('woo_nanopay_pos_options_group'); ?>
    <table>
    <tr valign="top">
    <th scope="row"><label for="woo_nanopay_pos_nano_address">Nano Address</label></th>
    <td><input type="text" id="woo_nanopay_pos_nano_address" name="woo_nanopay_pos_nano_address" value="<?php echo get_option('woo_nanopay_pos_nano_address'); ?>" /></td>
    </tr>
    </table>
    <?php submit_button(); ?>
    </form>
    <h3>POS Page</h3>
    <p>You can view the Nano Point of Sale page <a href="<?php echo esc_url($pos_page_url); ?>" target="_blank">here</a>.</p>
    </div>
<?php
}
?>