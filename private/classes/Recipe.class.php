<?php
class Recipe extends DatabaseObject {
    static protected $table_name = 'recipe';
    static protected $db_columns = ['recipe_id', 'user_id', 'title', 'description', 'style_id', 
                                  'diet_id', 'type_id', 'prep_hours', 'prep_minutes', 
                                  'cook_hours', 'cook_minutes', 'video_url', 'img_file_path', 
                                  'alt_text', 'is_featured', 'created_date', 'created_time'];
    static protected $primary_key = 'recipe_id';

    public $recipe_id;
    public $user_id;
    public $title;
    public $description;
    public $style_id;
    public $diet_id;
    public $type_id;
    public $prep_hours;
    public $prep_minutes;
    public $cook_hours;
    public $cook_minutes;
    public $video_url;
    public $img_file_path;
    public $alt_text;
    public $is_featured;
    public $created_date;
    public $created_time;

    public function __construct($args=[]) {
        $this->title = $args['title'] ?? '';
        $this->description = $args['description'] ?? '';
        $this->style_id = $args['style_id'] ?? null;
        $this->diet_id = $args['diet_id'] ?? null;
        $this->type_id = $args['type_id'] ?? null;
        $this->prep_hours = $args['prep_hours'] ?? 0;
        $this->prep_minutes = $args['prep_minutes'] ?? 0;
        $this->cook_hours = $args['cook_hours'] ?? 0;
        $this->cook_minutes = $args['cook_minutes'] ?? 0;
        $this->video_url = $args['video_url'] ?? '';
        $this->img_file_path = $args['img_file_path'] ?? '';
        $this->alt_text = $args['alt_text'] ?? '';
        $this->is_featured = $args['is_featured'] ?? false;
        $this->created_date = $args['created_date'] ?? date('Y-m-d');
        $this->created_time = $args['created_time'] ?? date('H:i:s');
    }

    public function style() {
        if($this->style_id) {
            return RecipeAttribute::find_one($this->style_id, RecipeAttribute::TYPE_STYLE);
        }
        return null;
    }

    public function diet() {
        if($this->diet_id) {
            return RecipeAttribute::find_one($this->diet_id, RecipeAttribute::TYPE_DIET);
        }
        return null;
    }

    public function type() {
        if($this->type_id) {
            return RecipeAttribute::find_one($this->type_id, RecipeAttribute::TYPE_TYPE);
        }
        return null;
    }

    public function user() {
        if($this->user_id) {
            return User::find_by_id($this->user_id);
        }
        return null;
    }

    public static function find_featured($limit=3) {
        $database = static::get_database();
        $sql = "SELECT * FROM " . static::$table_name;
        $sql .= " WHERE is_featured = TRUE";
        $sql .= " ORDER BY created_date DESC, created_time DESC";
        $sql .= " LIMIT " . $database->real_escape_string($limit);
        return static::find_by_sql($sql);
    }

    public function image_path() {
        return $this->img_file_path ? '/uploads/recipes/' . $this->img_file_path : '/images/recipe-placeholder.jpg';
    }

    public function author() {
        return $this->user();
    }

    public function rating_average() {
        $sql = "SELECT AVG(rating_value) as avg_rating FROM recipe_rating ";
        $sql .= "WHERE recipe_id='" . db_escape(self::$database, $this->recipe_id) . "'";
        $result = self::$database->query($sql);
        if(!$result) {
            return 0;
        }
        $row = $result->fetch_assoc();
        return $row['avg_rating'] ? round($row['avg_rating'], 1) : 0;
    }

    public function rating_count() {
        $sql = "SELECT COUNT(*) as count FROM recipe_rating ";
        $sql .= "WHERE recipe_id='" . db_escape(self::$database, $this->recipe_id) . "'";
        $result = self::$database->query($sql);
        if(!$result) {
            return 0;
        }
        $row = $result->fetch_assoc();
        return (int)$row['count'];
    }

    public static function count_all_filtered($search='', $style_id=null, $diet_id=null, $type_id=null) {
        $sql = "SELECT COUNT(*) FROM " . static::$table_name;
        $where_clauses = [];
        
        if(!empty($search)) {
            $database = static::get_database();
            $search = db_escape($database, $search);
            $where_clauses[] = "(title LIKE '%{$search}%' OR description LIKE '%{$search}%')";
        }
        
        if(!empty($style_id)) {
            $style_id = (int) $style_id;
            $where_clauses[] = "style_id = {$style_id}";
        }
        
        if(!empty($diet_id)) {
            $diet_id = (int) $diet_id;
            $where_clauses[] = "diet_id = {$diet_id}";
        }
        
        if(!empty($type_id)) {
            $type_id = (int) $type_id;
            $where_clauses[] = "type_id = {$type_id}";
        }
        
        if(!empty($where_clauses)) {
            $sql .= " WHERE " . implode(" AND ", $where_clauses);
        }
        
        $database = static::get_database();
        $result = mysqli_query($database, $sql);
        $row = mysqli_fetch_array($result);
        mysqli_free_result($result);
        return $row[0] ?? 0;
    }

    public static function find_all_filtered($search='', $style_id=null, $diet_id=null, $type_id=null, $sort='newest', $per_page=25, $offset=0) {
        $sql = "SELECT * FROM " . static::$table_name;
        $where_clauses = [];
        
        if(!empty($search)) {
            $database = static::get_database();
            $search = db_escape($database, $search);
            $where_clauses[] = "(title LIKE '%{$search}%' OR description LIKE '%{$search}%')";
        }
        
        if(!empty($style_id)) {
            $style_id = (int) $style_id;
            $where_clauses[] = "style_id = {$style_id}";
        }
        
        if(!empty($diet_id)) {
            $diet_id = (int) $diet_id;
            $where_clauses[] = "diet_id = {$diet_id}";
        }
        
        if(!empty($type_id)) {
            $type_id = (int) $type_id;
            $where_clauses[] = "type_id = {$type_id}";
        }
        
        if(!empty($where_clauses)) {
            $sql .= " WHERE " . implode(" AND ", $where_clauses);
        }
        
        // Add sorting
        switch($sort) {
            case 'oldest':
                $sql .= " ORDER BY created_date ASC, created_time ASC";
                break;
            case 'title':
                $sql .= " ORDER BY title ASC";
                break;
            case 'newest':
            default:
                $sql .= " ORDER BY created_date DESC, created_time DESC";
                break;
        }
        
        // Add pagination
        $sql .= " LIMIT " . (int)$per_page . " OFFSET " . (int)$offset;
        
        return static::find_by_sql($sql);
    }

    public static function find_by_page_with_relations($per_page = 25, $offset = 0, $search = '', $style_id = null, $diet_id = null, $type_id = null, $sort = 'newest') {
        $sql = "SELECT r.* ";
        $sql .= "FROM " . static::$table_name . " r ";
        
        $where_clauses = [];
        
        if(!empty($search)) {
            $database = static::get_database();
            $search = db_escape($database, $search);
            $where_clauses[] = "(r.title LIKE '%{$search}%' OR r.description LIKE '%{$search}%')";
        }
        
        if(!empty($style_id)) {
            $style_id = (int) $style_id;
            $where_clauses[] = "r.style_id = {$style_id}";
        }
        
        if(!empty($diet_id)) {
            $diet_id = (int) $diet_id;
            $where_clauses[] = "r.diet_id = {$diet_id}";
        }
        
        if(!empty($type_id)) {
            $type_id = (int) $type_id;
            $where_clauses[] = "r.type_id = {$type_id}";
        }
        
        if(!empty($where_clauses)) {
            $sql .= " WHERE " . implode(" AND ", $where_clauses);
        }
        
        // Add sorting
        switch($sort) {
            case 'oldest':
                $sql .= " ORDER BY r.created_date ASC, r.created_time ASC";
                break;
            case 'title':
                $sql .= " ORDER BY r.title ASC";
                break;
            case 'rating':
                $sql .= " ORDER BY r.created_date DESC"; // Default to newest if no rating
                break;
            case 'newest':
            default:
                $sql .= " ORDER BY r.created_date DESC, r.created_time DESC";
                break;
        }
        
        $sql .= " LIMIT {$per_page} OFFSET {$offset}";
        
        // Debug the SQL query
        error_log("Recipe query SQL: " . $sql);
        
        // Get the base recipe data
        $recipes = static::find_by_sql($sql);
        error_log("Found " . count($recipes) . " recipes");
        
        // Now get ratings for each recipe
        if (!empty($recipes)) {
            $recipe_ids = array_map(function($recipe) { 
                return $recipe->recipe_id; 
            }, $recipes);
            
            $ids_string = implode(',', $recipe_ids);
            
            // Get average ratings
            $rating_sql = "SELECT recipe_id, COALESCE(AVG(rating_value), 0) as rating ";
            $rating_sql .= "FROM recipe_rating ";
            $rating_sql .= "WHERE recipe_id IN ({$ids_string}) ";
            $rating_sql .= "GROUP BY recipe_id";
            
            $database = static::get_database();
            $rating_result = mysqli_query($database, $rating_sql);
            
            if ($rating_result) {
                $ratings = [];
                while ($row = mysqli_fetch_assoc($rating_result)) {
                    $ratings[$row['recipe_id']] = $row['rating'];
                }
                mysqli_free_result($rating_result);
                
                // Add ratings to recipe objects
                foreach ($recipes as $recipe) {
                    $recipe->rating = $ratings[$recipe->recipe_id] ?? 0;
                }
            }
        }
        
        return $recipes;
    }

    public static function find_by_sql($sql) {
        $database = static::get_database();
        $result = mysqli_query($database, $sql);
        if(!$result) {
            exit("Database query failed: " . mysqli_error($database));
        }

        // Convert results into objects
        $object_array = [];
        while($record = mysqli_fetch_assoc($result)) {
            $object_array[] = parent::instantiate($record);
        }
        mysqli_free_result($result);
        return $object_array;
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

        if(is_blank($this->title)) {
            $this->errors[] = "Title cannot be blank.";
        } elseif (!has_length($this->title, array('min' => 3, 'max' => 100))) {
            $this->errors[] = "Title must be between 3 and 100 characters.";
        }

        if(is_blank($this->description)) {
            $this->errors[] = "Description cannot be blank.";
        } elseif (!has_length($this->description, array('max' => 255))) {
            $this->errors[] = "Description cannot exceed 255 characters.";
        }

        if(!is_blank($this->video_url) && !preg_match('/^https:\/\//', $this->video_url)) {
            $this->errors[] = "Video URL must start with https://";
        }

        if($this->prep_hours < 0) {
            $this->errors[] = "Preparation hours cannot be negative.";
        }

        if($this->prep_minutes < 0 || $this->prep_minutes > 59) {
            $this->errors[] = "Preparation minutes must be between 0 and 59.";
        }

        if($this->cook_hours < 0) {
            $this->errors[] = "Cooking hours cannot be negative.";
        }

        if($this->cook_minutes < 0 || $this->cook_minutes > 59) {
            $this->errors[] = "Cooking minutes must be between 0 and 59.";
        }

        return $this->errors;
    }
}