    <?php
class User extends DatabaseObject {
    static protected $table_name = 'user_account';
    static protected $db_columns = ['user_id', 'username', 'email', 'password_hash', 'user_level', 'is_active'];
    static protected $primary_key = 'user_id';

    public $user_id;
    public $username;
    public $email;
    public $password_hash;
    public $user_level;
    public $is_active;
    protected $password;
    public $errors = [];

    public function __construct($args=[]) {
        $this->username = $args['username'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->user_level = $args['user_level'] ?? 'u';
        $this->is_active = $args['is_active'] ?? true;
    }

    public static function find_by_username($username) {
        $sql = "SELECT * FROM " . static::$table_name . " ";
        $sql .= "WHERE username='" . self::$database->escape_string($username) . "'";
        $obj_array = static::find_by_sql($sql);
        if(!empty($obj_array)) {
            return array_shift($obj_array);
        } else {
            return false;
        }
    }

    public static function find_by_email($email) {
        $sql = "SELECT * FROM " . static::$table_name . " ";
        $sql .= "WHERE email='" . self::$database->escape_string($email) . "'";
        $obj_array = static::find_by_sql($sql);
        if(!empty($obj_array)) {
            return array_shift($obj_array);
        } else {
            return false;
        }
    }

    public function verify_password($password) {
        return password_verify($password, $this->password_hash);
    }

    protected function set_hashed_password() {
        $this->password_hash = password_hash($this->password, PASSWORD_BCRYPT);
    }

    protected function create() {
        $this->set_hashed_password();
        return parent::create();
    }

    protected function update() {
        if($this->password != '') {
            $this->set_hashed_password();
        }
        return parent::update();
    }

    protected function validate() {
        $this->errors = [];

        if(is_blank($this->username)) {
            $this->errors[] = "Username cannot be blank.";
        } elseif (!has_length($this->username, array('min' => 3, 'max' => 50))) {
            $this->errors[] = "Username must be between 3 and 50 characters.";
        } elseif (!has_unique_username($this->username, $this->user_id ?? 0)) {
            $this->errors[] = "Username already exists. Try another.";
        }

        if(is_blank($this->email)) {
            $this->errors[] = "Email cannot be blank.";
        } elseif (!has_length($this->email, array('max' => 100))) {
            $this->errors[] = "Email must be less than 100 characters.";
        } elseif (!has_valid_email_format($this->email)) {
            $this->errors[] = "Email must be a valid format.";
        } elseif (!has_unique_email($this->email, $this->user_id ?? 0)) {
            $this->errors[] = "Email already exists. Try another.";
        }

        if($this->password_required) {
            if(is_blank($this->password)) {
                $this->errors[] = "Password cannot be blank.";
            } elseif (!has_length($this->password, array('min' => 8))) {
                $this->errors[] = "Password must contain 8 or more characters";
            }
        }

        if(!in_array($this->user_level, ['s', 'a', 'u'])) {
            $this->errors[] = "Invalid user level.";
        }

        return $this->errors;
    }

    // Check if user is super admin
    public function is_super_admin() {
        return $this->user_level === 's';
    }

    // Check if user is admin
    public function is_admin() {
        return $this->user_level === 'a' || $this->user_level === 's';
    }

    // Find all admin users (both admin and super admin)
    public static function find_all_admins() {
        $sql = "SELECT * FROM " . static::$table_name . " ";
        $sql .= "WHERE user_level IN ('a', 's') ";
        $sql .= "ORDER BY username ASC";
        return static::find_by_sql($sql);
    }

    // Find all regular users (not admins or super admins)
    public static function find_all_regular_users() {
        $sql = "SELECT * FROM " . static::$table_name . " ";
        $sql .= "WHERE user_level = 'u' ";
        $sql .= "ORDER BY username ASC";
        return static::find_by_sql($sql);
    }

    // Count all users
    public static function count_all() {
        global $database;
        $sql = "SELECT COUNT(*) as count FROM " . static::$table_name;
        $result = $database->query($sql);
        $row = $result->fetch_assoc();
        return $row['count'] ?? 0;
    }

    // Count recipes by user
    public function count_recipes() {
        global $database;
        $sql = "SELECT COUNT(*) as count FROM recipe WHERE user_id = " . $database->escape_string($this->user_id);
        $result = $database->query($sql);
        $row = $result->fetch_assoc();
        return $row['count'] ?? 0;
    }

    // Toggle user active status
    public function toggle_active() {
        $this->is_active = !$this->is_active;
        return $this->save();
    }
}