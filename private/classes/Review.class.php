<?php

class Review extends DatabaseObject {
    static protected $table_name = 'recipe_comment';
    static protected $db_columns = ['review_id', 'recipe_id', 'user_id', 'rating', 'comment', 'created_date', 'created_time'];
    static protected $primary_key = 'review_id';

    public $review_id;
    public $recipe_id;
    public $user_id;
    public $rating;
    public $comment;
    public $created_date;
    public $created_time;

    public function __construct($args=[]) {
        $this->recipe_id = $args['recipe_id'] ?? '';
        $this->user_id = $args['user_id'] ?? '';
        $this->rating = $args['rating'] ?? '';
        $this->comment = $args['comment'] ?? '';
        $this->created_date = $args['created_date'] ?? date('Y-m-d');
        $this->created_time = $args['created_time'] ?? date('H:i:s');
    }

    public function recipe() {
        if($this->recipe_id) {
            return Recipe::find_by_id($this->recipe_id);
        }
        return null;
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

        if(is_blank($this->rating)) {
            $this->errors[] = "Rating cannot be blank.";
        } elseif(!is_numeric($this->rating) || $this->rating < 1 || $this->rating > 5) {
            $this->errors[] = "Rating must be between 1 and 5.";
        }

        if(is_blank($this->comment)) {
            $this->errors[] = "Comment cannot be blank.";
        } elseif (!has_length($this->comment, array('max' => 255))) {
            $this->errors[] = "Comment cannot exceed 255 characters.";
        }

        return $this->errors;
    }

    // Count all reviews
    public static function count_all() {
        global $database;
        $sql = "SELECT COUNT(*) as count FROM " . static::$table_name;
        $result = $database->query($sql);
        $row = $result->fetch_assoc();
        return $row['count'] ?? 0;
    }

    // Count reviews by recipe
    public static function count_by_recipe($recipe_id) {
        global $database;
        $sql = "SELECT COUNT(*) as count FROM " . static::$table_name;
        $sql .= " WHERE recipe_id = " . $database->escape_string($recipe_id);
        $result = $database->query($sql);
        $row = $result->fetch_assoc();
        return $row['count'] ?? 0;
    }

    // Get average rating for a recipe
    public static function average_rating_for_recipe($recipe_id) {
        global $database;
        $sql = "SELECT AVG(rating) as avg_rating FROM " . static::$table_name;
        $sql .= " WHERE recipe_id = " . $database->escape_string($recipe_id);
        $result = $database->query($sql);
        $row = $result->fetch_assoc();
        return number_format($row['avg_rating'] ?? 0, 1);
    }

    // Find reviews by recipe
    public static function find_by_recipe($recipe_id) {
        global $database;
        $sql = "SELECT * FROM " . static::$table_name;
        $sql .= " WHERE recipe_id = " . $database->escape_string($recipe_id);
        $sql .= " ORDER BY created_date DESC, created_time DESC";
        return static::find_by_sql($sql);
    }

    // Find reviews by user
    public static function find_by_user($user_id) {
        global $database;
        $sql = "SELECT * FROM " . static::$table_name;
        $sql .= " WHERE user_id = " . $database->escape_string($user_id);
        $sql .= " ORDER BY created_date DESC, created_time DESC";
        return static::find_by_sql($sql);
    }
}

?>
