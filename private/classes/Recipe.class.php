<?php
class Recipe extends DatabaseObject {
    static protected $table_name = 'recipes';
    static protected $db_columns = ['id', 'title', 'description', 'ingredients', 'instructions', 
                                  'prep_time', 'cook_time', 'servings', 'image_path', 
                                  'style_id', 'diet_id', 'user_id', 'rating', 'rating_count', 
                                  'created_at', 'updated_at'];

    public $id;
    public $title;
    public $description;
    public $ingredients;
    public $instructions;
    public $prep_time;
    public $cook_time;
    public $servings;
    public $image_path;
    public $style_id;
    public $diet_id;
    public $user_id;
    public $rating;
    public $rating_count;
    public $created_at;
    public $updated_at;

    // Cached related objects
    private $style;
    private $diet;
    private $user;

    public function __construct($args=[]) {
        $this->title = $args['title'] ?? '';
        $this->description = $args['description'] ?? '';
        $this->ingredients = $args['ingredients'] ?? '';
        $this->instructions = $args['instructions'] ?? '';
        $this->prep_time = $args['prep_time'] ?? 0;
        $this->cook_time = $args['cook_time'] ?? 0;
        $this->servings = $args['servings'] ?? 1;
        $this->image_path = $args['image_path'] ?? '';
        $this->style_id = $args['style_id'] ?? null;
        $this->diet_id = $args['diet_id'] ?? null;
        $this->user_id = $args['user_id'] ?? null;
        $this->rating = $args['rating'] ?? 0;
        $this->rating_count = $args['rating_count'] ?? 0;
        $this->created_at = $args['created_at'] ?? date('Y-m-d H:i:s');
        $this->updated_at = $args['updated_at'] ?? date('Y-m-d H:i:s');
    }

    public function style() {
        if(!isset($this->style) && isset($this->style_id)) {
            $this->style = Style::find_by_id($this->style_id);
        }
        return $this->style;
    }

    public function diet() {
        if(!isset($this->diet) && isset($this->diet_id)) {
            $this->diet = Diet::find_by_id($this->diet_id);
        }
        return $this->diet;
    }

    public function user() {
        if(!isset($this->user) && isset($this->user_id)) {
            $this->user = User::find_by_id($this->user_id);
        }
        return $this->user;
    }

    public static function find_by_page_with_relations($per_page=25, $offset=0, $search='', $style_id=null, $diet_id=null, $sort='newest') {
        $sql = "SELECT r.* FROM " . static::$table_name . " r";
        
        // Build WHERE clause
        $where_clauses = [];
        $params = [];
        
        if(!empty($search)) {
            $where_clauses[] = "(r.title LIKE ? OR r.description LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }
        
        if(!empty($style_id)) {
            $where_clauses[] = "r.style_id = ?";
            $params[] = $style_id;
        }
        
        if(!empty($diet_id)) {
            $where_clauses[] = "r.diet_id = ?";
            $params[] = $diet_id;
        }
        
        if(!empty($where_clauses)) {
            $sql .= " WHERE " . implode(' AND ', $where_clauses);
        }
        
        // Add ORDER BY clause based on sort parameter
        switch($sort) {
            case 'oldest':
                $sql .= " ORDER BY r.created_at ASC";
                break;
            case 'rating':
                $sql .= " ORDER BY r.rating DESC, r.rating_count DESC";
                break;
            case 'popular':
                $sql .= " ORDER BY r.rating_count DESC, r.rating DESC";
                break;
            case 'title':
                $sql .= " ORDER BY r.title ASC";
                break;
            default: // newest
                $sql .= " ORDER BY r.created_at DESC";
        }
        
        $sql .= " LIMIT ? OFFSET ?";
        $params[] = $per_page;
        $params[] = $offset;
        
        return static::find_by_sql_with_params($sql, $params);
    }

    public static function count_all_filtered($search='', $style_id=null, $diet_id=null) {
        $sql = "SELECT COUNT(*) as count FROM " . static::$table_name . " r";
        
        // Build WHERE clause
        $where_clauses = [];
        $params = [];
        
        if(!empty($search)) {
            $where_clauses[] = "(r.title LIKE ? OR r.description LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }
        
        if(!empty($style_id)) {
            $where_clauses[] = "r.style_id = ?";
            $params[] = $style_id;
        }
        
        if(!empty($diet_id)) {
            $where_clauses[] = "r.diet_id = ?";
            $params[] = $diet_id;
        }
        
        if(!empty($where_clauses)) {
            $sql .= " WHERE " . implode(' AND ', $where_clauses);
        }
        
        $result = static::find_by_sql_with_params($sql, $params);
        return $result[0]->count ?? 0;
    }

    protected static function find_by_sql_with_params($sql, $params=[]) {
        global $database;
        $stmt = $database->prepare($sql);
        if(!empty($params)) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return static::instantiate_from_result($result);
    }

    protected static function instantiate_from_result($result) {
        $object_array = [];
        while($record = $result->fetch_assoc()) {
            $object_array[] = static::instantiate($record);
        }
        $result->free();
        return $object_array;
    }

    protected function validate() {
        $this->errors = [];

        if(is_blank($this->title)) {
            $this->errors[] = "Title cannot be blank.";
        }
        if(is_blank($this->ingredients)) {
            $this->errors[] = "Ingredients cannot be blank.";
        }
        if(is_blank($this->instructions)) {
            $this->errors[] = "Instructions cannot be blank.";
        }
        if(!isset($this->style_id)) {
            $this->errors[] = "Style must be selected.";
        }
        if(!isset($this->diet_id)) {
            $this->errors[] = "Diet must be selected.";
        }
        if(!isset($this->user_id)) {
            $this->errors[] = "User must be set.";
        }

        return $this->errors;
    }
}