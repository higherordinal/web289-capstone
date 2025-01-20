<?php

abstract class DatabaseObject {
    protected static $table_name;
    protected static $db_columns = [];
    protected static $database;
    public $errors = [];

    public static function set_database($database) {
        self::$database = $database;
        error_log("Database connection set: " . (self::$database ? "true" : "false"));
        if (self::$database) {
            error_log("Connected to database: " . self::$database->host_info);
        }
    }

    public static function get_database() {
        if (!isset(self::$database)) {
            error_log("Warning: Database connection not set!");
            throw new Exception("Database connection not set. Call set_database() first.");
        }
        return self::$database;
    }

    public static function find_by_sql($sql) {
        $database = static::get_database();
        $result = mysqli_query($database, $sql);
        if(!$result) {
            exit("Database query failed: " . mysqli_error($database) . "\nQuery: " . $sql);
        }

        $object_array = [];
        while($record = mysqli_fetch_assoc($result)) {
            $object_array[] = static::instantiate($record);
        }
        mysqli_free_result($result);
        return $object_array;
    }

    public static function find_all() {
        $sql = "SELECT * FROM " . static::$table_name;
        return static::find_by_sql($sql);
    }

    public static function count_all() {
        $database = static::get_database();
        $sql = "SELECT COUNT(*) FROM " . static::$table_name;
        $result = mysqli_query($database, $sql);
        $row = mysqli_fetch_array($result);
        mysqli_free_result($result);
        return array_shift($row);
    }

    public static function find_by_id($id) {
        $database = static::get_database();
        $sql = "SELECT * FROM " . static::$table_name . " ";
        $sql .= "WHERE " . static::get_primary_key() . "='" . db_escape($database, $id) . "'";
        $obj_array = static::find_by_sql($sql);
        if(!empty($obj_array)) {
            return array_shift($obj_array);
        } else {
            return false;
        }
    }

    protected static function instantiate($record) {
        $object = new static;
        foreach($record as $property => $value) {
            if(property_exists($object, $property)) {
                $object->$property = $value;
            }
        }
        return $object;
    }

    protected function validate() {
        $this->errors = [];
        return $this->errors;
    }

    protected function create() {
        $this->validate();
        if(!empty($this->errors)) { return false; }

        $attributes = $this->sanitized_attributes();
        $sql = "INSERT INTO " . static::$table_name . " (";
        $sql .= join(', ', array_keys($attributes));
        $sql .= ") VALUES ('";
        $sql .= join("', '", array_values($attributes));
        $sql .= "')";

        $database = static::get_database();
        $result = mysqli_query($database, $sql);
        if($result) {
            $this->id = mysqli_insert_id($database);
            return true;
        } else {
            return false;
        }
    }

    protected function update() {
        $this->validate();
        if(!empty($this->errors)) { return false; }

        $attributes = $this->sanitized_attributes();
        $attribute_pairs = [];
        foreach($attributes as $key => $value) {
            $attribute_pairs[] = "{$key}='{$value}'";
        }

        $sql = "UPDATE " . static::$table_name . " SET ";
        $sql .= join(', ', $attribute_pairs);
        $sql .= " WHERE id='" . db_escape(static::get_database(), $this->id) . "' ";
        $sql .= "LIMIT 1";

        $database = static::get_database();
        $result = mysqli_query($database, $sql);
        return $result;
    }

    public function save() {
        if(isset($this->id)) {
            return $this->update();
        } else {
            return $this->create();
        }
    }

    public function merge_attributes($args=[]) {
        foreach($args as $key => $value) {
            if(property_exists($this, $key) && !is_null($value)) {
                $this->$key = $value;
            }
        }
    }

    public function attributes() {
        $attributes = [];
        foreach(static::$db_columns as $column) {
            if($column == 'id') { continue; }
            $attributes[$column] = $this->$column;
        }
        return $attributes;
    }

    protected function sanitized_attributes() {
        $database = static::get_database();
        $sanitized = [];
        foreach($this->attributes() as $key => $value) {
            $sanitized[$key] = db_escape($database, $value);
        }
        return $sanitized;
    }

    public function delete() {
        $sql = "DELETE FROM " . static::$table_name . " ";
        $sql .= "WHERE id='" . db_escape(static::get_database(), $this->id) . "' ";
        $sql .= "LIMIT 1";

        $database = static::get_database();
        $result = mysqli_query($database, $sql);
        return $result;
    }

    protected static function get_primary_key() {
        return isset(static::$primary_key) ? static::$primary_key : 'id';
    }
}