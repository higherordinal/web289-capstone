-- user_account table
CREATE TABLE user_account (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    user_level CHAR(1) NOT NULL DEFAULT 'u' CHECK (user_level IN ('s', 'a', 'u')),
    is_active BOOLEAN DEFAULT TRUE -- Active status for user accounts
);

-- recipe table
CREATE TABLE recipe (
    recipe_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    style_id INT,
    diet_id INT,
    type_id INT,
    prep_hours INT DEFAULT 0, -- Preparation time in hours
    prep_minutes INT DEFAULT 0, -- Preparation time in minutes
    cook_hours INT DEFAULT 0, -- Cooking time in hours
    cook_minutes INT DEFAULT 0, -- Cooking time in minutes
    video_url VARCHAR(255), -- YouTube video link for walkthrough
    img_file_path VARCHAR(255), -- Path to the single uploaded image
    alt_text VARCHAR(255), -- Alt text for the image (optional)
    is_featured BOOLEAN DEFAULT FALSE, -- Mark recipes as featured
    created_date DATE DEFAULT CURRENT_DATE,
    created_time TIME DEFAULT CURRENT_TIME,
    FOREIGN KEY (user_id) REFERENCES user_account(user_id) ON DELETE CASCADE,
    FOREIGN KEY (style_id) REFERENCES recipe_style(style_id) ON DELETE SET NULL,
    FOREIGN KEY (diet_id) REFERENCES recipe_diet(diet_id) ON DELETE SET NULL,
    FOREIGN KEY (type_id) REFERENCES recipe_type(type_id) ON DELETE SET NULL
);

-- user_favorite table
CREATE TABLE user_favorite (
    user_id INT NOT NULL,
    recipe_id INT NOT NULL,
    added_date DATE DEFAULT CURRENT_DATE, -- Date the recipe was favorited
    PRIMARY KEY (user_id, recipe_id), -- Composite key to prevent duplicates
    FOREIGN KEY (user_id) REFERENCES user_account(user_id) ON DELETE CASCADE,
    FOREIGN KEY (recipe_id) REFERENCES recipe(recipe_id) ON DELETE CASCADE
);

-- tag table
CREATE TABLE tag (
    tag_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL, -- Tag name (e.g., "Healthy", "Quick")
    user_id INT, -- User who created the tag
    UNIQUE (name, user_id), -- Prevent duplicate tags for the same user
    FOREIGN KEY (user_id) REFERENCES user_account(user_id) ON DELETE SET NULL
);

-- recipe_tag table
CREATE TABLE recipe_tag (
    recipe_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (recipe_id, tag_id), -- Prevent duplicate recipe-tag pairs
    FOREIGN KEY (recipe_id) REFERENCES recipe(recipe_id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tag(tag_id) ON DELETE CASCADE
);

-- measurement table
CREATE TABLE measurement (
    measurement_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE -- Example: "cup", "teaspoon", "gram", etc.
);

-- recipe_ingredient table
CREATE TABLE recipe_ingredient (
    ingredient_id INT AUTO_INCREMENT PRIMARY KEY,
    recipe_id INT NOT NULL, -- Links the ingredient to a specific recipe
    measurement_id INT NOT NULL, -- Links to a predefined measurement
    quantity INT NOT NULL, -- Numeric quantity (e.g., 2, 500)
    ingredient_text VARCHAR(255) NOT NULL, -- Free-text ingredient
    FOREIGN KEY (recipe_id) REFERENCES recipe(recipe_id) ON DELETE CASCADE,
    FOREIGN KEY (measurement_id) REFERENCES measurement(measurement_id) ON DELETE SET NULL
);

-- recipe_style table
CREATE TABLE recipe_style (
    style_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

-- recipe_diet table
CREATE TABLE recipe_diet (
    diet_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

-- recipe_type table
CREATE TABLE recipe_type (
    type_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

-- recipe_comment table
CREATE TABLE recipe_comment (
    comment_id INT AUTO_INCREMENT PRIMARY KEY,
    recipe_id INT NOT NULL,
    user_id INT NOT NULL,
    comment_text VARCHAR(255) NOT NULL,
    created_date DATE DEFAULT CURRENT_DATE,
    created_time TIME DEFAULT CURRENT_TIME,
    FOREIGN KEY (recipe_id) REFERENCES recipe(recipe_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES user_account(user_id) ON DELETE CASCADE
);

-- recipe_rating table
CREATE TABLE recipe_rating (
    rating_id INT AUTO_INCREMENT PRIMARY KEY,
    recipe_id INT NOT NULL,
    user_id INT NOT NULL,
    rating_value TINYINT NOT NULL CHECK (rating_value BETWEEN 1 AND 5),
    FOREIGN KEY (recipe_id) REFERENCES recipe(recipe_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES user_account(user_id) ON DELETE CASCADE
);

-- recipe_step table
CREATE TABLE recipe_step (
    step_id INT AUTO_INCREMENT PRIMARY KEY,
    recipe_id INT NOT NULL,
    step_number INT NOT NULL,
    instruction VARCHAR(255) NOT NULL,
    FOREIGN KEY (recipe_id) REFERENCES recipe(recipe_id) ON DELETE CASCADE
);

-- Insert predefined measurements
INSERT INTO measurement (name) VALUES
('cup'), ('teaspoon'), ('tablespoon'), ('gram'), ('kilogram'), ('ounce'), ('pound'), ('pinch'), ('dash'), ('liter'), ('milliliter');
