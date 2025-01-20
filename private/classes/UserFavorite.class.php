<?php
class UserFavorite extends DatabaseObject {
    static protected $table_name = 'user_favorite';
    static protected $db_columns = ['user_id', 'recipe_id', 'added_date'];
    static protected $primary_key = ['user_id', 'recipe_id'];

    public $user_id;
    public $recipe_id;
    public $added_date;

    public function __construct($args=[]) {
        $this->user_id = $args['user_id'] ?? '';
        $this->recipe_id = $args['recipe_id'] ?? '';
        $this->added_date = $args['added_date'] ?? date('Y-m-d');
    }

    public static function find_by_user_and_recipe($user_id, $recipe_id) {
        $sql = "SELECT * FROM " . static::$table_name . " ";
        $sql .= "WHERE user_id='" . self::$database->escape_string($user_id) . "' ";
        $sql .= "AND recipe_id='" . self::$database->escape_string($recipe_id) . "'";
        
        $obj_array = static::find_by_sql($sql);
        if(!empty($obj_array)) {
            return array_shift($obj_array);
        } else {
            return false;
        }
    }

    public static function is_favorite($user_id, $recipe_id) {
        return static::find_by_user_and_recipe($user_id, $recipe_id) !== false;
    }

    public function validate() {
        $this->errors = [];

        if(is_blank($this->user_id)) {
            $this->errors[] = "User ID cannot be blank.";
        }
        if(is_blank($this->recipe_id)) {
            $this->errors[] = "Recipe ID cannot be blank.";
        }
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

        error_log("SQL Query: " . $sql);

        $database = static::get_database();
        $result = mysqli_query($database, $sql);
        if($result) {
            return true;
        } else {
            error_log("MySQL Error: " . mysqli_error($database));
            return false;
        }
    }

    public function delete() {
        $sql = "DELETE FROM " . static::$table_name . " ";
        $sql .= "WHERE user_id='" . self::$database->escape_string($this->user_id) . "' ";
        $sql .= "AND recipe_id='" . self::$database->escape_string($this->recipe_id) . "'";

        error_log("Delete SQL Query: " . $sql);

        $database = static::get_database();
        $result = mysqli_query($database, $sql);
        if($result) {
            return true;
        } else {
            error_log("MySQL Delete Error: " . mysqli_error($database));
            return false;
        }
    }
}
?>
