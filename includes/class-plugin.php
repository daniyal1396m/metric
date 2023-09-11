<?php

namespace Todo;

class Plugin
{
    public function __construct()
    {
    }

    public static function activation_handler()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'todos';
        $wpdb_collate = $wpdb->collate;
        $sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
            id mediumint(8) unsigned NOT NULL auto_increment,
            status varchar(255) NULL,
            title varchar(255) NULL,
            description varchar(255) NULL,
            user_id bigint(20) unsigned NOT NULL,
            PRIMARY KEY (id),
            FOREIGN KEY (user_id) REFERENCES wp_users(ID)
        ) COLLATE {$wpdb_collate}";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
//    return 'ToDo Plugin Activated';
//    wp_die('');
    }

    public static function deactivation_handler()
    {
//    return 'ToDo Plugin Deactivated';
//    wp_die('deactivated');
    }

    public static function uninstall_handler()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'todos';
        $wpdb->query("DROP TABLE IF EXISTS $table_name");
//    return 'ToDo Plugin Uninstalled';
//    wp_die('');
    }
}