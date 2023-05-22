<?php

namespace Blax\Wordpress\Extendables;

interface ModelInterface
{
    const DATABASE = '';
}

abstract class Model implements ModelInterface
{
    public static $instance = null;
    public static $current_sql = null;
    public static $result = null;

    public function __construct()
    {
        if (static::$instance) return static::$instance;
        static::$instance = $this;
    }

    public static function select($selection = [])
    {
        if (!static::$current_sql) {
            static::$current_sql = "SELECT * FROM " . (static::DATABASE)::table();
        }
        return static::$instance;
    }

    public static function create($payload)
    {
        return (static::DATABASE)::insert($payload);
    }

    public static function all()
    {
        return (static::DATABASE)::all();
    }

    private static function arrayToOperand($array)
    {
        $sql = '';

        switch (count($array)) {
            default:
            case 0:
                break;
            case 1:
                if (is_array($array[0])) {
                    $sql .= self::arrayToOperand($array[0]);
                } else {
                    $sql .= "$array[0]";
                }
                break;
            case 2:
                $sql .= "$array[0] = '$array[1]'";
                break;
            case 3:
                // if $value[2] is string
                $sql .= (is_string($array[2]))
                    ? "$array[0] $array[1] '$array[2]'"
                    : "$array[0] $array[1] $array[2]";
                break;
        }

        return $sql;
    }

    public static function where($where)
    {
        global $wpdb;
        $sql = "SELECT * FROM " . (static::DATABASE)::table() . " WHERE ";

        // if func has 3 arguments supplied
        if (func_num_args() == 3) {
            $where = [func_get_args()];
        }

        foreach ($where as $key => $value) {
            // if value is array
            if (is_array($value)) {
                $sql .= self::arrayToOperand($value);
            } else {
                $sql .= "$key = '$value'";
            }

            // if not last item add ,
            $keys = array_keys($where);
            if (end($keys) != $key) {
                $sql .= " AND ";
            }
        }
        static::$current_sql = $sql;
        return static::$instance;
    }

    public static function get($pluck_array = [])
    {
        global $wpdb;
        $sql = static::$current_sql;
        $results = $wpdb->get_results($sql);

        if ($pluck_array) {
            $return = [];
            foreach ($results as $result) {
                $return[] = $result->$pluck_array;
            }
        } else {
            $return = $results;
        }

        static::$current_sql = null;
        return $return;
    }

    public static function delete($where = null)
    {
        global $wpdb;
        $sql = static::$current_sql;

        if (!$sql || $where) $sql = (static::where($where))::$current_sql;

        $sql = str_replace('SELECT * FROM', 'DELETE FROM', $sql);
        $wpdb->query($sql);
        static::$current_sql = null;

        return static::$instance;
    }

    // latest()
    public static function latest()
    {
        global $wpdb;

        $primary_column = (static::DATABASE)::primary();

        $sql = "SELECT * FROM " . (static::DATABASE)::table() . " ORDER BY $primary_column DESC LIMIT 1";

        $results = $wpdb->get_results($sql);

        return $results[0];
    }

    // first
    public static function first()
    {
        global $wpdb;
        $primary_column = (static::DATABASE)::primary();

        $sql = (static::$current_sql)
            ? static::$current_sql . " ORDER BY $primary_column ASC LIMIT 1"
            : "SELECT * FROM " . (static::DATABASE)::table() . " ORDER BY $primary_column ASC LIMIT 1";

        return @($wpdb->get_results($sql))[0];
    }

    // first or create
    public static function firstOrCreate($where, $create_payload)
    {
        $result = (static::where($where))->first();
        if (!$result) {
            (static::create($create_payload));
            $result = (static::where($where))->first();
        }
        return $result;
    }

    // updateOrCreate
    public static function updateOrCreate($where, $update_payload)
    {
        $result = (static::where($where))->count();

        if (!$result) {
            $update_payload = array_merge($where, $update_payload);
            $r = (static::create($update_payload));
            if (!$r) {
                Plugin::log('Error creating: ', $update_payload);
            }
            return (static::where($where))->first();
        } else {
            (static::where($where))->update($update_payload);
            return (static::where($where))->first();
        }
    }

    // update
    public static function update($payload)
    {
        global $wpdb;
        $sql = static::$current_sql;
        if (!$sql) return;

        $sql = str_replace('SELECT * FROM', 'UPDATE', $sql);

        // get keys from (static::DATABASE)::COLLUMNS
        $allowed_keys = array_keys((static::DATABASE)::COLLUMNS);

        // remove all keys in $payload except $allowed_keys
        foreach ($payload as $key => $value) {
            if (!in_array($key, $allowed_keys)) {
                unset($payload[$key]);
            }
        }

        // update payload
        $set_sql = " SET ";
        foreach ($payload as $key => $value) {
            if ($value === null) {
                $set_sql .= "$key = null";
            } else {
                $set_sql .= "$key = '$value'";
            }
            // if not last item add ,
            $keys = array_keys($payload);
            if (end($keys) != $key) {
                $set_sql .= ", ";
            } else {
                $set_sql .= " ";
            }
        }

        // insert set_sql into sql before the string "WHERE" (case insensitive)
        $sql = str_ireplace("WHERE", $set_sql . "WHERE", $sql);
        // Plugin::log($sql);
        static::$result = $wpdb->query($sql);
        static::$current_sql = null;
        return static::$instance;
    }

    // find
    public static function find($id)
    {
        global $wpdb;

        $primary_column = (static::DATABASE)::primary();

        $sql = "SELECT * FROM " . (static::DATABASE)::table() . " WHERE $primary_column = $id";
        self::$current_sql = $sql;

        $results = $wpdb->get_results($sql);

        return $results[0];
    }

    public static function sort($column, $order = 'ASC')
    {
        global $wpdb;
        $sql = static::$current_sql;

        if (!$sql) {
            self::select();
        }

        $sql .= " ORDER BY $column $order";

        static::$current_sql = $sql;
        return static::$instance;
    }

    // count
    public static function count()
    {
        global $wpdb;

        if (!static::$current_sql) {
            self::select();
        }

        $sql = static::$current_sql;

        $sql = str_replace('SELECT * FROM', 'SELECT COUNT(*) FROM', $sql);

        $results = $wpdb->get_results($sql);

        return $results[0]->{'COUNT(*)'};
    }

    // limit
    public static function limit($limit)
    {
        global $wpdb;
        $sql = static::$current_sql;

        if (!$sql) {
            self::select();
        }

        $sql .= " LIMIT $limit";

        static::$current_sql = $sql;
        return static::$instance;
    }
}
