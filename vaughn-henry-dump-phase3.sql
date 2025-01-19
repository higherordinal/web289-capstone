-- Create the database
DROP DATABASE IF EXISTS flavorconnect;
CREATE DATABASE flavorconnect
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_general_ci;

USE flavorconnect;

-- Create user and grant privileges
DROP USER IF EXISTS 'flavor_admin'@'localhost';
CREATE USER 'flavor_admin'@'localhost' IDENTIFIED BY 'flavor_pass_2024';
GRANT ALL PRIVILEGES ON flavorconnect.* TO 'flavor_admin'@'localhost';
FLUSH PRIVILEGES;

-- user_account table
CREATE TABLE user_account (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    user_level CHAR(1) NOT NULL DEFAULT 'u' CHECK (user_level IN ('s', 'a', 'u')),
    is_active BOOLEAN DEFAULT TRUE -- Active status for user accounts
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

-- measurement table
CREATE TABLE measurement (
    measurement_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE -- Example: "cup", "teaspoon", "gram", etc.
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
    prep_hours INT DEFAULT 0 CHECK (prep_hours >= 0),
    prep_minutes INT DEFAULT 0 CHECK (prep_minutes BETWEEN 0 AND 59),
    cook_hours INT DEFAULT 0 CHECK (cook_hours >= 0),
    cook_minutes INT DEFAULT 0 CHECK (cook_minutes BETWEEN 0 AND 59),
    video_url VARCHAR(255),
    img_file_path VARCHAR(255),
    alt_text VARCHAR(255),
    is_featured BOOLEAN DEFAULT FALSE,
    created_date DATE DEFAULT CURRENT_DATE,
    created_time TIME DEFAULT CURRENT_TIME,
    FOREIGN KEY (user_id) REFERENCES user_account(user_id) ON DELETE CASCADE,
    FOREIGN KEY (style_id) REFERENCES recipe_style(style_id) ON DELETE SET NULL,
    FOREIGN KEY (diet_id) REFERENCES recipe_diet(diet_id) ON DELETE SET NULL,
    FOREIGN KEY (type_id) REFERENCES recipe_type(type_id) ON DELETE SET NULL,
    CONSTRAINT chk_video_url CHECK (video_url IS NULL OR video_url LIKE 'https://%'),
    CONSTRAINT chk_title_length CHECK (LENGTH(title) >= 3)
);

-- recipe_ingredient table
CREATE TABLE recipe_ingredient (
    ingredient_id INT AUTO_INCREMENT PRIMARY KEY,
    recipe_id INT NOT NULL,
    measurement_id INT NOT NULL,
    quantity DECIMAL(10,2) NOT NULL CHECK (quantity > 0),
    ingredient_text VARCHAR(255) NOT NULL,
    FOREIGN KEY (recipe_id) REFERENCES recipe(recipe_id) ON DELETE CASCADE,
    FOREIGN KEY (measurement_id) REFERENCES measurement(measurement_id) ON DELETE RESTRICT
);

-- tag table
CREATE TABLE tag (
    tag_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    user_id INT,
    UNIQUE (name, user_id),
    FOREIGN KEY (user_id) REFERENCES user_account(user_id) ON DELETE SET NULL
);

-- recipe_tag table
CREATE TABLE recipe_tag (
    recipe_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (recipe_id, tag_id),
    FOREIGN KEY (recipe_id) REFERENCES recipe(recipe_id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tag(tag_id) ON DELETE CASCADE
);

-- user_favorite table
CREATE TABLE user_favorite (
    user_id INT NOT NULL,
    recipe_id INT NOT NULL,
    added_date DATE DEFAULT CURRENT_DATE,
    PRIMARY KEY (user_id, recipe_id),
    FOREIGN KEY (user_id) REFERENCES user_account(user_id) ON DELETE CASCADE,
    FOREIGN KEY (recipe_id) REFERENCES recipe(recipe_id) ON DELETE CASCADE
);

-- recipe_comment table
CREATE TABLE recipe_comment (
    comment_id INT AUTO_INCREMENT PRIMARY KEY,
    recipe_id INT NOT NULL,
    user_id INT NOT NULL,
    comment_text VARCHAR(255) NOT NULL CHECK (LENGTH(TRIM(comment_text)) > 0),
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
    UNIQUE KEY unique_user_recipe_rating (user_id, recipe_id),
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

-- Insert default measurements
INSERT INTO measurement (name) VALUES 
('cup'), ('teaspoon'), ('tablespoon'), ('gram'), ('kilogram'), ('ounce'), ('pound'), ('pinch'), ('dash'), ('liter'), ('milliliter');

-- Insert default recipe styles
INSERT INTO recipe_style (name) VALUES
('American'), ('Italian'), ('Mexican'), ('Chinese'), ('Indian'), ('Japanese'), ('Thai'), ('Mediterranean'), ('French'), ('Korean');

-- Insert default recipe diets
INSERT INTO recipe_diet (name) VALUES
('Vegetarian'), ('Vegan'), ('Gluten-Free'), ('Dairy-Free'), ('Keto'), ('Paleo'), ('Low-Carb'), ('Low-Fat'), ('Pescatarian'), ('Halal');

-- Insert default recipe types
INSERT INTO recipe_type (name) VALUES
('Breakfast'), ('Lunch'), ('Dinner'), ('Appetizer'), ('Dessert'), ('Snack'), ('Beverage'), ('Soup'), ('Salad'), ('Main Course');
