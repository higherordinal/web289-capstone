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

    // Count recipes by user
    public function count_recipes() {
        return Recipe::count_by_user($this->id);
    }

    // Toggle user active status
    public function toggle_active() {
        $this->is_active = !$this->is_active;
        return $this->save();
    }