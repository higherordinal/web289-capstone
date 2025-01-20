<?php

/**
 * Review class for handling recipe ratings and comments
 * Extends DatabaseObject to provide database operations
 */
class Review extends DatabaseObject {
    /** @var string Database table name for ratings */
    static protected $table_name = "recipe_rating";
    
    /** @var string Primary key field name */
    static protected $primary_key = "rating_id";
    
    /** @var array List of database columns */
    static protected $db_columns = ['rating_id', 'recipe_id', 'user_id', 'rating_value', 'comment_text', 'created_date', 'created_time'];

    /** @var int Unique identifier for the rating */
    public $rating_id;
    
    /** @var int Recipe ID this review belongs to */
    public $recipe_id;
    
    /** @var int User ID who wrote the review */
    public $user_id;
    
    /** @var int Rating value (1-5) */
    public $rating_value;
    
    /** @var string Optional comment text */
    public $comment_text;
    
    /** @var string Date the review was created */
    public $created_date;
    
    /** @var string Time the review was created */
    public $created_time;
    
    /** @var string Username of the reviewer */
    public $username;

    /**
     * Constructor for Review class
     * @param array $args Associative array of property values
     */
    public function __construct($args=[]) {
        $this->rating_id = $args['rating_id'] ?? '';
        $this->recipe_id = $args['recipe_id'] ?? '';
        $this->user_id = $args['user_id'] ?? '';
        $this->rating_value = $args['rating_value'] ?? '';
        $this->comment_text = $args['comment_text'] ?? '';
        $this->created_date = $args['created_date'] ?? date('Y-m-d');
        $this->created_time = $args['created_time'] ?? date('H:i:s');
    }

    /**
     * Gets all attributes except the rating_id
     * @return array Array of object attributes
     */
    public function attributes() {
        $attributes = [];
        foreach(static::$db_columns as $column) {
            if($column == 'rating_id') { continue; }
            $attributes[$column] = $this->$column;
        }
        return $attributes;
    }

    /**
     * Saves the review and its associated comment
     * @return bool True if both rating and comment were saved successfully
     */
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

    /**
     * Finds all reviews for a specific recipe
     * @param int $recipe_id The ID of the recipe
     * @return array Array of Review objects with comments
     */
    public static function find_by_recipe_id($recipe_id) {
        $sql = "SELECT r.*, c.comment_text, c.created_date, c.created_time, u.username ";
        $sql .= "FROM " . static::$table_name . " AS r ";
        $sql .= "LEFT JOIN recipe_comment AS c ON r.recipe_id = c.recipe_id AND r.user_id = c.user_id ";
        $sql .= "LEFT JOIN users AS u ON r.user_id = u.id ";
        $sql .= "WHERE r.recipe_id='" . self::$database->escape_string($recipe_id) . "' ";
        $sql .= "ORDER BY COALESCE(c.created_date, CURRENT_DATE) DESC, COALESCE(c.created_time, CURRENT_TIME) DESC";
        return static::find_by_sql($sql);
    }

    /**
     * Gets the user who wrote this review
     * @return User|null User object or null if not found
     */
    public function get_user() {
        if($this->user_id) {
            return User::find_by_id($this->user_id);
        }
        return null;
    }

    /**
     * Validates the review data
     * @return array Array of validation errors
     */
    protected function validate() {
        $this->errors = [];

        if(is_blank($this->recipe_id)) {
            $this->errors[] = "Recipe ID cannot be blank.";
        }
        
        if(is_blank($this->user_id)) {
            $this->errors[] = "User ID cannot be blank.";
        }
        
        if(is_blank($this->rating_value)) {
            $this->errors[] = "Rating cannot be blank.";
        }
        
        if(!is_blank($this->rating_value) && !has_number_between($this->rating_value, 1, 5)) {
            $this->errors[] = "Rating must be between 1 and 5.";
        }

        return $this->errors;
    }
}

?>
