<?php

namespace Jumia;

ob_start();

/** General settings page **/
class Settings_Page
{

    static $instance;

    /** class constructor */
    public function __construct()
    {

        add_action('admin_menu', array($this, 'register_settings_page'), 1);
        add_action('plugins_loaded', array($this, 'maybe_self_deactivate'));
        add_action('admin_notices', array($this, 'license_invalid_notice'));
    }

    public function license_invalid_notice()
    {
        if (!ju_is_licence_active()) {
            echo '<div class="error"><p><strong>' . sprintf(
                    __('Your JumiaShop license is invalid or has expired and as such, the plugin has stopped working. %sLogin to your account%s to renew or purchase a new license', 'jumia'),
                    '<a href="https://affiliateshop.com.ng/my-account/" target="_blank">',
                    '</a>'
                )
                . '.</strong></p></div>';
        }
    }

    public function register_settings_page()
    {
        add_menu_page(
            'Jumia Affiliate Shop',
            'Jumia Shop',
            'manage_options',
            'jumia-shop',
            array(
                $this,
                'settings_page_callback',
            ),
            JU_INCLUDES_URL . '/images/icon.png'
        );
    }

    public function settings_page_callback()
    {
        $this->save_settings_data();
        ?>
        <div class="wrap">
            <div id="icon-options-general" class="icon32"></div>
            <h2><?php _e('Jumia Shop Settings', 'jumia'); ?></h2>
            <?php if (isset($_GET['settings-update']) && $_GET['settings-update']) : ?>
                <div id="message" class="updated"><p><strong><?php _e('Settings saved', 'jumia'); ?>.</strong>
                    </p></div>
            <?php endif; ?>

            <?php if (isset($_GET['force-sync']) && 'true' == $_GET['force-sync']) : ?>
                <div id="message" class="updated"><p>
                        <strong><?php _e('Synchronisation Completed.', 'jumia'); ?>.</strong>
                    </p></div>
            <?php endif; ?>

            <?php if (isset($_GET['force-sync']) && 'false' == $_GET['force-sync']) : ?>
                <div id="message" class="updated"><p>
                        <strong><?php _e('Synchronisation failed. Ensure the activate checkbox is checked', 'jumia'); ?>
                            .</strong>
                    </p></div>
            <?php endif; ?>

            <?php if (isset($_GET['upgrade-platform']) && 'true' == $_GET['upgrade-platform']) : ?>
                <div id="message" class="updated"><p>
                        <strong><?php _e('platform upgrade apparently successful.', 'jumia'); ?>
                            .</strong>
                    </p></div>
            <?php endif; ?>

            <?php if (isset($_GET['delete-product']) && $_GET['delete-product']) : ?>
                <div id="message" class="updated"><p><strong><?php _e('Products Deleted', 'jumia'); ?>.</strong>
                    </p></div>
            <?php endif;

            $db_data = ju_db_data();
            ?>

            <div id="poststuff" class="ppview">

                <div id="post-body" class="metabox-holder columns-2">
                    <div id="post-body-content">
                        <div class="meta-box-sortables ui-sortable">
                            <form method="post">
                                <div class="postbox">
                                    <div class="handlediv" title="Click to toggle"><br></div>
                                    <h3 class="hndle ui-sortable-handle"><span>Configuration</span></h3>

                                    <div class="inside">
                                        <table class="form-table">
                                            <tr>
                                                <th scope="row"><label for="activate">Activate Plugin</label></th>
                                                <td>
                                                    <label for="activate"><strong>Activate</strong></label>
                                                    <input type="checkbox" id="activate" name="activate" value="yes" <?php checked(@$db_data['activate'], 'yes'); ?>
                                                    "/>
                                                    <p class="description">Check to activate and un-check to deactivate
                                                        the plugin momentarily.</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><label for="license_key">License Key</label></th>
                                                <td>
                                                    <input type="text" id="license_key" name="license_key" class="all-options" value="<?php echo !empty($db_data['license_key']) ? $db_data['license_key'] : esc_url_raw(@$_POST['license_key']); ?>"/>

                                                    <p class="description">Enter your license key to receive plugin
                                                        updates.</a></p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><label for="aff_url">Affiliate URL</label></th>
                                                <td>
                                                    <input type="text" id="aff_url" name="aff_url" class="all-options" value="<?php echo !empty($db_data['aff_url']) ? $db_data['aff_url'] : esc_url_raw(@$_POST['aff_url']); ?>"/>

                                                    <p class="description">Enter your Jumia affiliate link.
                                                        <a target="_blank" href="http://affiliateshop.com.ng/documentation/jumia-plugin-setup/#affiliate_url">Learn
                                                            how to get it</a>
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><label for="crawl_interval">Crawl Interval</label></th>
                                                <td>
                                                    <select id="crawl_interval" name="crawl_interval">
                                                        <option value="hourly" <?php isset($_POST["crawl_interval"]) && $_POST["crawl_interval"] == 'hourly' ? selected($_POST["crawl_interval"], 'hourly') : selected(@$db_data['crawl_interval'], 'hourly'); ?>>
                                                            Hourly
                                                        </option>
                                                        <option value="twicedaily" <?php isset($_POST["crawl_interval"]) && $_POST["crawl_interval"] == 'twicedaily' ? selected($_POST["crawl_interval"], 'twicedaily') : selected(@$db_data['crawl_interval'], 'twicedaily'); ?>>
                                                            Twice Daily
                                                        </option>
                                                        <option value="daily" <?php isset($_POST["crawl_interval"]) && $_POST["crawl_interval"] == 'daily' ? selected($_POST["crawl_interval"], 'daily') : selected(@$db_data['crawl_interval'], 'daily'); ?>>
                                                            Daily
                                                        </option>
                                                    </select>

                                                    <p class="description">How often to crawl Jumia website.</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><label for="crawl_interval">SEO Plugin</label></th>
                                                <td>
                                                    <select id="seo_plugin" name="seo_plugin">
                                                        <option>Select..</option>
                                                        <option value="yoast_seo" <?php isset($_POST["seo_plugin"]) && $_POST["seo_plugin"] == 'yoast_seo' ? selected($_POST["seo_plugin"], 'yoast_seo') : selected(@$db_data['seo_plugin'], 'yoast_seo'); ?>>
                                                            WordPress SEO by Yoast
                                                        </option>
                                                        <option value="seo_ultimate" <?php isset($_POST["seo_plugin"]) && $_POST["seo_plugin"] == 'seo_ultimate' ? selected($_POST["seo_plugin"], 'seo_ultimate') : selected(@$db_data['seo_plugin'], 'seo_ultimate'); ?>>
                                                            SEO Ultimate
                                                        </option>
                                                    </select>

                                                    <p class="description">Select the WordPress SEO Plugin this site is
                                                        using.</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><label for="force_sync">Force Sync</label></th>
                                                <td>
                                                    <?php submit_button('Force Sync', 'secondary', 'force_sync', false); ?>

                                                    <p class="description">Click to force the plugin to fetch
                                                        products.</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><label for="product_age">Delete Old Product</label>
                                                </th>
                                                <td>
                                                    <input type="number" id="product_age" style="width: 60px;" name="product_age" class="all-options" value="<?php echo !empty($db_data['product_age']) ? $db_data['product_age'] : '30'; ?>"/>
                                                    <?php submit_button('Delete', 'secondary', 'delete_product', false); ?>
                                                    <p class="description">Delete WooCommerce products older than the
                                                        number of days entered in the input field above.</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><label for="product_age">Upgrade to New Platform</label>
                                                </th>
                                                <td>
                                                    <input type="text" id="old_aff_url" style="width: 400px;" name="old_aff_url" class="all-options" value="<?php echo !empty($db_data['old_aff_url']) ? $db_data['old_aff_url'] : ''; ?>"/>
                                                    <?php submit_button('Upgrade', 'secondary', 'upgrade_platform', false); ?>
                                                    <p class="description">Enter your old affiliate URL and click submit
                                                        button to upgrade to new platform.</p>
                                                    <p class="description"><strong>Note:</strong> ensure you've entered
                                                        your new Affiliate URL in the "Affiliate URL" field above.</p>
                                                </td>
                                            </tr>
                                        </table>
                                        <p>
                                            <?php wp_nonce_field('settings_nonce'); ?>
                                            <input class="button-primary" type="submit" name="settings_submit" value="Save All Changes">
                                        </p>
                                    </div>
                                </div>

                                <div class="postbox">
                                    <div class="handlediv" title="Click to toggle"><br></div>
                                    <h3 class="hndle ui-sortable-handle">
                                        <span><?php _e('Category Force Sync', 'jumia'); ?></span></h3>

                                    <div class="inside">
                                        <table class="form-table">
                                            <?php if (count($this->get_woo_categories()) < 1) {
                                                echo 'No WooCommerce product category found. Consider <a href="' . admin_url('edit-tags.php?taxonomy=product_cat&post_type=product') . '">creating one</a>.';
                                            } else { ?>
                                                <tr>
                                                    <td><label for="category-to-force-sync">Product Category</label>
                                                    </td>
                                                    <td>
                                                        <div><?php $this->category_dropdown_single(); ?></div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <th scope="row"><label for="force_sync">Category Force Sync</label>
                                                    </th>
                                                    <td>
                                                        <?php submit_button('Force Sync', 'secondary', 'category_force_sync', false); ?>

                                                        <p class="description">Click to force the plugin to fetch
                                                            products from selected category only.</p>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </table>
                                    </div>
                                </div>
                                <div class="postbox">
                                    <div class="handlediv" title="Click to toggle"><br></div>
                                    <h3 class="hndle ui-sortable-handle">
                                        <span><?php _e('Product Setup', 'jumia'); ?></span></h3>

                                    <div class="inside">
                                        <table class="form-table">
                                            <?php if (count($this->get_woo_categories()) < 1) {
                                                echo 'No WooCommerce product category found. Consider <a href="' . admin_url('edit-tags.php?taxonomy=product_cat&post_type=product') . '">creating one</a>.';
                                            } else { ?>
                                                <?php for ($i = 0; $i < count($this->get_woo_categories()); $i++) : ?>
                                                    <tr>
                                                        <td><label for="pd-<?php echo $i; ?>">Product Category</label>
                                                        </td>
                                                        <td>
                                                            <div><?php $this->category_dropdown($i); ?></div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <label for="source-<?php echo $i; ?>">Product Sources
                                                                (URLs)</label>
                                                        </td>
                                                        <td>
                                                            <div>
                                                                <textarea rows="5" id="source-<?php echo $i; ?>" name="ju_sources[<?php echo $i; ?>]"><?php echo !empty($db_data['ju_sources'][$i]) ? $db_data['ju_sources'][$i] : esc_url_raw(@$_POST['ju_sources'][$i]); ?></textarea>
                                                            </div>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <th scope="row"></th>
                                                    </tr>
                                                <?php endfor;
                                            } ?>
                                        </table>
                                        <?php if (count($this->get_woo_categories()) >= 1) : ?>
                                            <p>
                                                <?php wp_nonce_field('settings_nonce'); ?>
                                                <input class="button-primary" type="submit" name="settings_submit" value="Save All Changes">
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                    <?php include_once 'settings-sidebar.php'; ?>

                </div>
                <br class="clear">
            </div>
        </div>
        <?php
    }

    /**
     * Save the settings page data
     *
     */
    function save_settings_data()
    {
        if (isset($_POST['_wpnonce']) && check_admin_referer('settings_nonce', '_wpnonce')) {

            if (isset($_POST['force_sync']) || isset($_POST['category_force_sync'])) {

                if (isset($_POST['force_sync'])) {
                    $status = jumia_scrap_and_create_product();
                }

                if (isset($_POST['category_force_sync'])) {
                    $status = jumia_scrap_and_create_product(true);
                }

                if ('false' == $status) {
                    $redirect = add_query_arg('force-sync', 'false');
                } else {
                    $redirect = add_query_arg('force-sync', 'true');
                }
                wp_redirect(esc_url_raw($redirect));
                exit;
            }

            $aff_url = $_POST['aff_url'];
            $old_aff_url = $_POST['old_aff_url'];

            if (isset($_POST['upgrade_platform']) && !empty($aff_url) && !empty($old_aff_url)) {
                $this->upgrade_to_new_affiliate_platform(
                    sanitize_text_field($_POST['old_aff_url']),
                    sanitize_text_field($_POST['aff_url'])
                );

                wp_redirect(esc_url_raw(add_query_arg('upgrade-platform', 'true')));
                exit;
            }

            if (isset($_POST['delete_product']) && !empty($_POST['product_age'])) {
                jumia_delete_old_product(absint($_POST['product_age']));
                wp_redirect(esc_url_raw(add_query_arg('delete-product', 'true')));
                exit;
            }

            if (isset($_POST['settings_submit'])) {
                ju_data_update($_POST);

                $settings_data = array();
                foreach ($_POST as $key => $value) {

                    // do not save the nonce value to DB
                    if ($key == '_wpnonce') {
                        continue;
                    }
                    // do not save the nonce referer to DB
                    if ($key == '_wp_http_referer') {
                        continue;
                    }
                    // do not save the submit button value
                    if ($key == 'settings_submit') {
                        continue;
                    }
                    // do not save the submit button value
                    if ($key == 'force_sync') {
                        continue;
                    }
                    // do not save the submit button value
                    if ($key == 'delete_product') {
                        continue;
                    }

                    $settings_data[$key] = $value;
                }

                $data_to_save = array();

                foreach ($settings_data as $key => $value) {
                    if ($key == 'ju_products' && is_array($settings_data[$key])) {
                        $data_to_save['ju_products'] = $settings_data[$key];
                    }

                    if ($key == 'ju_sources' && is_array($settings_data[$key])) {
                        $data_to_save['ju_sources'] = $settings_data[$key];
                    }

                    if (is_string($settings_data[$key])) {
                        $data_to_save[$key] = $settings_data[$key];
                    }
                }


                $db_data = ju_db_data();
                $old_aff_link = $db_data['aff_url'];

                update_option('ju_settings_data', $data_to_save);

                // ju_db_data() is duplicated because we need to refresh the data from DB.
                $db_data = ju_db_data();
                $new_aff_link = $db_data['aff_url'];

                // if old aff url contains "marketing.net.jumia.com.ng", upgrade to new aff platform
                if (strpos($old_aff_link, 'marketing.net.jumia.com.ng')) {
                    $this->upgrade_to_new_affiliate_platform($old_aff_link, $new_aff_link);
                }

                wp_redirect(esc_url_raw(add_query_arg('settings-update', 'true')));
                exit;
            }
        }
    }

    /**
     * Array of woocommerce created product categories.
     *
     * @return array
     */
    public function get_woo_categories()
    {
        return get_categories(
            array(
                'taxonomy' => 'product_cat',
                'show_count' => 0,
                'pad_counts' => 0,
                'hierarchical' => 1,
                'hide_empty' => 0
            )
        );
    }


    public function category_dropdown($key)
    {
        $db_data = ju_db_data();
        ?>
        <select name="ju_products[<?php echo $key; ?>]" id="pd-<?php echo $key; ?>">
            <?php foreach ($this->get_woo_categories() as $category) : ?>
                <option value="<?php echo $category->term_id; ?>"
                    <?php isset($_POST["ju_products"][$key]) && $_POST["ju_products"][$key] == $category->term_id ? selected($_POST["ju_products"][$key], $category->term_id) : selected($db_data['ju_products'][$key], $category->term_id); ?>>
                    <?php echo $category->name; ?></option>
            <?php endforeach; ?>
        </select>
    <?php }


    public function category_dropdown_single()
    {
        $db_data = ju_db_data();
        ?>
        <select name="category_to_force_sync" id="category-to-force-sync">
            <?php foreach ($this->get_woo_categories() as $category) : ?>
                <option value="<?php echo $category->term_id; ?>"
                    <?php isset($_POST["category_to_force_sync"]) && $_POST["category_to_force_sync"] == $category->term_id ? selected($_POST["category_to_force_sync"], $category->term_id) : selected(@$db_data['category_to_force_sync'], $category->term_id); ?>>
                    <?php echo $category->name; ?></option>
            <?php endforeach; ?>
        </select>
    <?php }

    public function upgrade_to_new_affiliate_platform($search, $replace)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'postmeta';

        $sql = "UPDATE $table SET meta_value = REPLACE (meta_value,'%s','%s');";

        return $wpdb->query(
            $wpdb->prepare($sql, array($search, $replace))
        );

    }

    /**
     * If dependency requirements are not satisfied, self-deactivate
     */
    public static function maybe_self_deactivate()
    {
        if (!class_exists('WooCommerce')) {
            require_once(ABSPATH . 'wp-admin/includes/plugin.php');
            deactivate_plugins(plugin_basename(JU_SYSTEM_FILE_PATH));
            add_action('admin_notices', array(__CLASS__, 'self_deactivate_notice'));
        }
    }

    /**
     * Display an error message when the plugin deactivates itself.
     */
    public static function self_deactivate_notice()
    {
        echo '<div class="error"><p><strong>' . __('Jumia Shop', 'jumia') . '</strong> ' . __('requires WooCommerce activated to work', 'jumia') . '.</p></div>';
    }

    static function get_instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

}

ob_clean();
