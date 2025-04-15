<?php
/**
 * Plugin Name: Glass Analytics
 * Description: Add Glass Analytics tracking to your site
 * Version: 1.0
 * Author: Glass Analytics
 */

// Make sure we don't expose any info if called directly
if (!function_exists('add_action')) {
    echo 'Hi there! I\'m just a plugin, not much I can do when called directly.';
    exit;
}

// Define constants
define('GLASS_ANALYTICS_PLUGIN', __FILE__);
define('GLASS_ANALYTICS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('GLASS_ANALYTICS_BASENAME', plugin_basename(__FILE__));
define('GLASS_ANALYTICS_URL', plugin_dir_url(__FILE__));

// Register activation hook
register_activation_hook(__FILE__, 'glass_analytics_activate');

function glass_analytics_activate() {
    // Default settings
    $default_options = array(
        'site_id' => 'default',
        'workspace_id' => '',
        'script_url' => 'https://staging-cdn.glassanalytics.com/analytics.min.js'
    );
    
    // Add options if they don't exist
    add_option('glass_analytics_options', $default_options);
}

// Get plugin options
function glass_analytics_get_options() {
    $options = get_option('glass_analytics_options');
    
    // Set defaults if options don't exist
    if (!$options) {
        $options = array(
            'site_id' => 'default',
            'workspace_id' => '',
            'script_url' => 'https://staging-cdn.glassanalytics.com/analytics.min.js'
        );
    }
    
    // Make sure all expected options exist
    if (!isset($options['script_url']) || empty($options['script_url'])) {
        $options['script_url'] = 'https://staging-cdn.glassanalytics.com/analytics.min.js';
    }
    
    // Make sure workspace_id exists
    if (!isset($options['workspace_id'])) {
        $options['workspace_id'] = '';
    }
    
    // Make sure site_id exists and is not empty
    if (!isset($options['site_id']) || empty($options['site_id'])) {
        $options['site_id'] = 'default';
    }
    
    return $options;
}


// Add script to head
function glass_analytics_add_script() {
    $options = glass_analytics_get_options();
    $site_id = !empty($options['site_id']) ? sanitize_text_field($options['site_id']) : '';
    $script_url = !empty($options['script_url']) ? esc_url($options['script_url']) : 'https://staging-cdn.glassanalytics.com/analytics.min.js';
    
    if (!empty($site_id)) {
        echo '<script src="' . $script_url . '" data-site="' . esc_attr($site_id) . '"></script>';
    } else {
        echo '<script src="' . $script_url . '"></script>';
    }
}
add_action('wp_head', 'glass_analytics_add_script');

// Add admin menu with hover dropdown
function glass_analytics_add_admin_menu() {
    // Add settings page under Settings menu
    add_options_page(
        'Glass Analytics Settings',
        'Glass Analytics',
        'manage_options',
        'glass-analytics',
        'glass_analytics_options_page'
    );
    
    // Add the top-level menu as a blank non-functional item
    add_menu_page(
        'Glass Analytics',       // Page title
        'Glass Analytics',       // Menu title
        'manage_options',        // Capability
        'glass-analytics-noop',  // Menu slug - will be completely disabled by JS
        function() {             // Empty callback that does nothing
            wp_die('This page intentionally left blank.');
        },
        'dashicons-analytics',   // Using dashboard icon
        30                       // Position
    );
    
    // Add submenu items
    add_submenu_page(
        'glass-analytics-noop',  // Parent slug
        'Open Analytics',        // Page title
        'Open Analytics',        // Menu title
        'manage_options',        // Capability
        'glass-analytics-open',  // Menu slug
        function() {             // Empty callback
            wp_die('Redirecting...');
        }
    );
    
    add_submenu_page(
        'glass-analytics-noop',  // Parent slug
        'Settings',              // Page title
        'Settings',              // Menu title
        'manage_options',        // Capability
        'glass-analytics',       // Menu slug (points to settings page)
        'glass_analytics_options_page'
    );
    
    // Remove the duplicate first submenu item
    remove_submenu_page('glass-analytics-noop', 'glass-analytics-noop');
}
add_action('admin_menu', 'glass_analytics_add_admin_menu');

// Enqueue admin styles
function glass_analytics_admin_styles() {
    wp_enqueue_style('glass-analytics-admin', GLASS_ANALYTICS_URL . 'assets/glass-admin.css', array(), '1.0.4'); // Using version to manage caching
}
add_action('admin_enqueue_scripts', 'glass_analytics_admin_styles');

// No custom class needed, using standard dashicon

// Add JavaScript for menu behavior
function glass_analytics_admin_js() {
    ?>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // First find the main menu item
        var mainMenuItem = document.querySelector('a.toplevel_page_glass-analytics-noop');
        
        if (mainMenuItem) {
            // Completely disable clicks
            mainMenuItem.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }, true);
            
            // Set attributes to make it non-clickable
            mainMenuItem.setAttribute('onclick', 'return false;');
            mainMenuItem.setAttribute('href', 'javascript:void(0);');
        }
        
        // Handle click on "Open Analytics" submenu item
        var analyticsLink = document.querySelector('a[href="admin.php?page=glass-analytics-open"]');
        if (analyticsLink) {
            analyticsLink.addEventListener('click', function(e) {
                e.preventDefault();
                var options = <?php echo json_encode(glass_analytics_get_options()); ?>;
                var site_id = options.site_id;
                var workspace_id = options.workspace_id;
                
                if (!site_id || !workspace_id) {
                    alert('Please configure your Site ID and Workspace ID in Glass Analytics settings.');
                    return;
                }
                
                window.open('https://staging.app.glassanalytics.com/' + workspace_id + '/site/' + site_id, '_blank');
            });
        }
    });
    </script>
    <?php
}
add_action('admin_footer', 'glass_analytics_admin_js');

// Add Settings link to plugins page
function glass_analytics_add_settings_link($links) {
    $settings_link = '<a href="options-general.php?page=glass-analytics">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . GLASS_ANALYTICS_BASENAME, 'glass_analytics_add_settings_link');

// Register settings
function glass_analytics_register_settings() {
    register_setting('glass_analytics_options_group', 'glass_analytics_options', 'glass_analytics_validate_options');
}
add_action('admin_init', 'glass_analytics_register_settings');

// Validate options
function glass_analytics_validate_options($input) {
    $valid = array();
    
    // Validate site_id - ensure it's not empty
    $valid['site_id'] = sanitize_text_field($input['site_id']);
    if (empty($valid['site_id'])) {
        $valid['site_id'] = 'default';
    }
    
    // Validate workspace_id
    $valid['workspace_id'] = sanitize_text_field($input['workspace_id']);
    
    $valid['script_url'] = esc_url_raw($input['script_url']);
    return $valid;
}

// Settings page
function glass_analytics_options_page() {
    // Get options
    $options = glass_analytics_get_options();
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('glass_analytics_options_group');
            do_settings_sections('glass_analytics_options_group');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Site ID</th>
                    <td>
                        <input type="text" name="glass_analytics_options[site_id]" value="<?php echo esc_attr($options['site_id']); ?>" class="regular-text" required />
                        <p class="description">Enter your site ID for Glass Analytics.</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Workspace ID</th>
                    <td>
                        <input type="text" name="glass_analytics_options[workspace_id]" value="<?php echo esc_attr($options['workspace_id']); ?>" class="regular-text" required />
                        <p class="description">Enter your workspace ID.</p>
                    </td>
                </tr>
                <!-- Script URL option - hidden but preserved in the database -->
                <input type="hidden" name="glass_analytics_options[script_url]" value="<?php echo esc_url($options['script_url']); ?>" />
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}