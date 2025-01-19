<?php

class RecipeAttribute extends DatabaseObject {
    protected static $table_name; // Will be set dynamically
    protected static $db_columns = ['id', 'name'];
    
    public $id;
    public $name;

    // Types of attributes
    const TYPE_STYLE = 'recipe_style';
    const TYPE_DIET = 'recipe_diet';
    const TYPE_TYPE = 'recipe_type';
    
    public function __construct($args=[]) {
        $this->id = $args['id'] ?? '';
        $this->name = $args['name'] ?? '';
    }

    // Get all items of a specific type (style, diet, type)
    public static function get_all($type) {
        self::$table_name = $type;
        return static::find_by_sql("SELECT * FROM " . $type . " ORDER BY name");
    }

    // Find one record by ID for a specific type
    public static function find_one($id, $type) {
        self::$table_name = $type;
        $sql = "SELECT * FROM " . $type . " WHERE ";
        $sql .= match($type) {
            self::TYPE_STYLE => "style_id",
            self::TYPE_DIET => "diet_id",
            self::TYPE_TYPE => "type_id",
            default => "id"
        };
        $sql .= "= '" . self::get_database()->escape_string($id) . "'";
        
        $obj_array = static::find_by_sql($sql);
        if(!empty($obj_array)) {
            return array_shift($obj_array);
        }
        return false;
    }

    protected function validate() {
        $this->errors = [];

        if(is_blank($this->name)) {
            $this->errors[] = "Name cannot be blank.";
        } elseif (!has_length($this->name, ['min' => 2, 'max' => 50])) {
            $this->errors[] = "Name must be between 2 and 50 characters.";
        }

        return $this->errors;
    }
}
