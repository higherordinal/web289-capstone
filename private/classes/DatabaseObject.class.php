<?php

abstract class DatabaseObject {
    protected static $table_name;
    protected static $db_columns = [];
    protected static $database;
    public $errors = [];

    public static function set_database($database) {
        self::$database = $database;
    }

    public static function get_database() {
        if(!isset(self::$database)) {
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
        $sql = "SELECT * FROM " . static::$table_name . " ";
        $sql .= "WHERE " . static::get_primary_key() . "='" . db_escape(static::get_database(), $id) . "'";
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
        $sql .= " WHERE " . static::get_primary_key() . "='" . db_escape(static::get_database(), $this->{static::get_primary_key()}) . "' ";
        $sql .= "LIMIT 1";

        $database = static::get_database();
        $result = mysqli_query($database, $sql);
        return $result;
    }

    public function save() {
        if(isset($this->{static::get_primary_key()})) {
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
        $sql .= "WHERE " . static::get_primary_key() . "='" . db_escape(static::get_database(), $this->{static::get_primary_key()}) . "' ";
        $sql .= "LIMIT 1";

        $database = static::get_database();
        $result = mysqli_query($database, $sql);
        return $result;
    }

    protected static function get_primary_key() {
        if(isset(static::$primary_key)) {
            return static::$primary_key;
        }
        // Default to table_name + _id
        return static::$table_name . '_id';
    }
}