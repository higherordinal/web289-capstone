<?php

class Review extends DatabaseObject {
    static protected $table_name = "recipe_rating";
    static protected $db_columns = ['rating_id', 'recipe_id', 'user_id', 'rating_value', 'comment_text', 'created_date', 'created_time'];

    public $rating_id;
    public $recipe_id;
    public $user_id;
    public $rating_value;
    public $comment_text;
    public $created_date;
    public $created_time;
    public $username;

    public function __construct($args=[]) {
        $this->rating_id = $args['rating_id'] ?? '';
        $this->recipe_id = $args['recipe_id'] ?? '';
        $this->user_id = $args['user_id'] ?? '';
        $this->rating_value = $args['rating_value'] ?? '';
        $this->comment_text = $args['comment_text'] ?? '';
        $this->created_date = $args['created_date'] ?? date('Y-m-d');
        $this->created_time = $args['created_time'] ?? date('H:i:s');
    }

    public function save() {
        // First save the rating
        $result = parent::save();
        if($result && !empty($this->comment_text)) {
            // If rating saved and we have a comment, save the comment too
            $sql = "INSERT INTO recipe_comment ";
            $sql .= "(recipe_id, user_id, comment_text, created_date, created_time) ";
            $sql .= "VALUES (";
            $sql .= "'" . self::$database->escape_string($this->recipe_id) . "',";
            $sql .= "'" . self::$database->escape_string($this->user_id) . "',";
            $sql .= "'" . self::$database->escape_string($this->comment_text) . "',";
            $sql .= "'" . self::$database->escape_string($this->created_date) . "',";
            $sql .= "'" . self::$database->escape_string($this->created_time) . "'";
            $sql .= ")";
            $result = self::$database->query($sql);
        }
        return $result;
    }

    public static function find_by_recipe_id($recipe_id) {
        $sql = "SELECT r.*, c.comment_text, c.created_date, c.created_time, u.username ";
        $sql .= "FROM " . static::$table_name . " AS r ";
        $sql .= "LEFT JOIN recipe_comment AS c ON r.recipe_id = c.recipe_id AND r.user_id = c.user_id ";
        $sql .= "LEFT JOIN user_account AS u ON r.user_id = u.user_id ";
        $sql .= "WHERE r.recipe_id='" . self::$database->escape_string($recipe_id) . "' ";
        $sql .= "ORDER BY COALESCE(c.created_date, CURRENT_DATE) DESC, COALESCE(c.created_time, CURRENT_TIME) DESC";
        return static::find_by_sql($sql);
    }

    public function user() {
        if($this->user_id) {
            return User::find_by_id($this->user_id);
        }
        return null;
    }

    protected function validate() {
        $this->errors = [];

        if(is_blank($this->recipe_id)) {
            $this->errors[] = "Recipe ID cannot be blank.";
        }
        if(is_blank($this->user_id)) {
            $this->errors[] = "User ID cannot be blank.";
        }
        if(is_blank($this->rating_value)) {
            $this->errors[] = "Rating value cannot be blank.";
        }
        if(!is_blank($this->rating_value) && !has_number_between($this->rating_value, 1, 5)) {
            $this->errors[] = "Rating must be between 1 and 5.";
        }

        return $this->errors;
    }
}

?>
