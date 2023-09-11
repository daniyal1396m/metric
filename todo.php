<?php
/*
 * Plugin Name:       Daniel Todo
 * Plugin URI:        https://danieldeveloper.ir
 * Description:       Handle the basics with this plugin.
 * Version:           1.10.3
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Daniel Motahari
 * Author URI:        https://danieldeveloper.ir
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://danieldeveloper.ir
 * Text Domain:       todo
 * Domain Path:       /languages
 */
if (!defined('ABSPATH')) {
    die('You Can Not Access To This Dir');
}
__('Todo', 'todo');
__('Todo manage Task', 'todo');
add_action('admin_menu', 'add_plugin_menu');
add_action('wp_enqueue_scripts', 'load_plugin_styles');

function add_plugin_menu()
{
    add_menu_page(
        'Todo List',
        'Todo List',
        'manage_options',
        'todo_list',
        'my_plugin_page_function'
    );
}

function my_plugin_page_function()
{
    include(plugin_dir_path(__FILE__) . 'indexView.php');
}

function load_plugin_styles()
{
    wp_enqueue_style('plugin-styles', plugins_url('css/style.css', __FILE__));
}

define('TODO_PATH', plugin_dir_path(__FILE__));
define('TODO_URL', plugin_dir_url(__FILE__));
require_once(TODO_PATH . '/includes/class-plugin.php');
register_activation_hook(__FILE__, ['\Todo\Plugin', 'activation_handler']);
register_deactivation_hook(__FILE__, array('\Todo\Plugin', 'deactivation_handler'));
register_uninstall_hook(__FILE__, ['\Todo\Plugin', 'uninstall_handler']);

add_action('wp_ajax_save_data_to_database', 'save_data_to_database_callback');
add_action('wp_ajax_nopriv_save_data_to_database', 'save_data_to_database_callback');

function save_data_to_database_callback()
{
    // دریافت داده‌های ارسالی از درخواست AJAX
    $data_value = sanitize_text_field($_POST['data_value']);

    // ذخیره داده در دیتابیس
    global $wpdb;
    $table_name = $wpdb->prefix . 'todos';
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;

    $result = $wpdb->insert(
        $table_name,
        array(
            'status' => 0, // مقدار پیش‌فرض برای وضعیت
            'title' => $data_value,
            'user_id' => $user_id
        )
    );

    if ($result) {
        echo 'success';
    } else {
        echo 'error';
    }

    wp_die();
}

add_action('wp_ajax_update_task_status', 'update_task_status_callback');
add_action('wp_ajax_nopriv_update_task_status', 'update_task_status_callback');

function update_task_status_callback()
{
    $task_id = sanitize_text_field($_POST['task_id']);
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;
    global $wpdb;
    $table_name = $wpdb->prefix . 'todos';

    $result = $wpdb->update(
        $table_name,
        array(
            'status' => 1,
        ),
        array(
            'id' => $task_id,
            'user_id' => $user_id,
        )
    );

    if ($result !== false) {
        echo 'success';
    } else {
        echo 'error';
    }
    wp_die();
}