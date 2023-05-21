<?php

namespace Blax\Wordpress\Extendables;

interface DatabaseInterface
{
    const TABLE = '';
    const COLLUMNS = [];
    public static function migrate();
}

class Database implements DatabaseInterface
{
    public static $instance = null;

    public function __construct()
    {
        if (static::$instance) {
            return static::$instance;
        }
        static::$instance = $this;
    }

    public static function migrate()
    {
        global $wpdb;

        $sql = "CREATE TABLE " . $wpdb->prefix . static::TABLE . " (";
        foreach (static::COLLUMNS as $key => $value) {
            $sql .= "$key $value";

            // if not last item add ,
            $keys = array_keys(static::COLLUMNS);
            if (end($keys) != $key) {
                $sql .= ",";
            }
        }
        $sql .= ") " . $wpdb->get_charset_collate() . ";";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    // all()
    public static function all()
    {
        global $wpdb;
        return $wpdb->get_results("SELECT * FROM " . static::table());
    }

    public static function insert($payload)
    {
        global $wpdb;
        $r = $wpdb->insert(static::table(), $payload);
        if (!$r) {
            Plugin::log('Last error:', $wpdb->last_error);
        }
        return $r;
    }

    public static function update($payload, $where)
    {
        global $wpdb;
        $wpdb->update(static::table(), $payload, $where);
    }

    public static function delete($where)
    {
        global $wpdb;
        $wpdb->delete(static::table(), $where);
    }

    public static function table()
    {
        global $wpdb;
        return $wpdb->prefix . static::TABLE;
    }

    public static function name()
    {
        return static::table();
    }

    public static function raw($sql)
    {
        global $wpdb;
        $sql = "SELECT * FROM " . static::table() . " " . $sql;
        return $wpdb->get_results($sql);
    }

    // get column with primary key
    public static function primary()
    {
        global $wpdb;
        $sql = "SHOW KEYS FROM " . static::table() . " WHERE Key_name = 'PRIMARY'";
        $results = $wpdb->get_results($sql);
        return $results[0]->Column_name;
    }
}
