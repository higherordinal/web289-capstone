-- Insert sample users first (if they don't exist)
INSERT IGNORE INTO user_account (username, email, password_hash, user_level) VALUES
('john_chef', 'john@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'u'),
('maria_cook', 'maria@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'u'),
('admin_chef', 'admin@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'a');

-- Get user IDs for existing users
SET @john_id = (SELECT user_id FROM user_account WHERE username = 'john_chef' LIMIT 1);
SET @maria_id = (SELECT user_id FROM user_account WHERE username = 'maria_cook' LIMIT 1);
SET @admin_id = (SELECT user_id FROM user_account WHERE username = 'admin_chef' LIMIT 1);

-- Insert recipe attributes if they don't exist
INSERT IGNORE INTO recipe_style (name) VALUES 
('Italian'), ('Mediterranean'), ('Korean');

INSERT IGNORE INTO recipe_diet (name) VALUES 
('Vegetarian');

INSERT IGNORE INTO recipe_type (name) VALUES 
('Main Course');

-- Insert measurement units if they don't exist
INSERT IGNORE INTO measurement (name) VALUES 
('pound'), ('ounce'), ('cup');

-- Insert sample recipes
INSERT INTO recipe (user_id, title, description, style_id, diet_id, type_id, prep_hours, prep_minutes, cook_hours, cook_minutes, is_featured, created_date, created_time, img_file_path, alt_text) VALUES
-- Classic Spaghetti Carbonara
(@john_id, 'Classic Spaghetti Carbonara', 'Traditional Roman pasta dish with eggs, cheese, pancetta, and black pepper', 
 (SELECT style_id FROM recipe_style WHERE name = 'Italian' LIMIT 1),
 NULL,
 (SELECT type_id FROM recipe_type WHERE name = 'Main Course' LIMIT 1),
 0, 20, 0, 15, TRUE, CURRENT_DATE, CURRENT_TIME,
 '/images/recipes/carbonara.jpg',
 'A plate of perfectly cooked spaghetti carbonara with crispy pancetta and fresh black pepper'),

-- Vegetarian Buddha Bowl
(@maria_id, 'Colorful Buddha Bowl', 'Healthy and vibrant bowl filled with quinoa, roasted vegetables, and tahini dressing',
 (SELECT style_id FROM recipe_style WHERE name = 'Mediterranean' LIMIT 1),
 (SELECT diet_id FROM recipe_diet WHERE name = 'Vegetarian' LIMIT 1),
 (SELECT type_id FROM recipe_type WHERE name = 'Main Course' LIMIT 1),
 0, 15, 0, 30, TRUE, CURRENT_DATE, CURRENT_TIME,
 '/images/recipes/buddha-bowl.jpg',
 'A beautifully arranged vegetarian Buddha bowl featuring crispy tofu, sliced avocado, edamame, shredded carrots, cucumber, fresh herbs, and bean sprouts in a black bowl'),

-- Korean BBQ Tacos
(@john_id, 'Korean BBQ Tacos', 'Fusion dish combining Korean marinated beef with Mexican-style tacos',
 (SELECT style_id FROM recipe_style WHERE name = 'Korean' LIMIT 1),
 NULL,
 (SELECT type_id FROM recipe_type WHERE name = 'Main Course' LIMIT 1),
 1, 0, 0, 30, FALSE, CURRENT_DATE, CURRENT_TIME,
 '/images/recipes/korean-tacos.jpg',
 'Three Korean BBQ tacos topped with kimchi slaw and sesame seeds');

-- Set recipe IDs for ingredients and steps
SET @carbonara_id = (SELECT recipe_id FROM recipe WHERE title = 'Classic Spaghetti Carbonara' LIMIT 1);

-- Insert ingredients for Spaghetti Carbonara
INSERT INTO recipe_ingredient (recipe_id, measurement_id, quantity, ingredient_text) VALUES
(@carbonara_id, (SELECT measurement_id FROM measurement WHERE name = 'pound' LIMIT 1), 1, 'spaghetti'),
(@carbonara_id, (SELECT measurement_id FROM measurement WHERE name = 'ounce' LIMIT 1), 4, 'pancetta'),
(@carbonara_id, (SELECT measurement_id FROM measurement WHERE name = 'cup' LIMIT 1), 1, 'Pecorino Romano cheese');

-- Insert recipe steps for Spaghetti Carbonara
INSERT INTO recipe_step (recipe_id, step_number, instruction) VALUES
(@carbonara_id, 1, 'Bring a large pot of salted water to boil'),
(@carbonara_id, 2, 'Cook spaghetti according to package instructions'),
(@carbonara_id, 3, 'In a pan, cook pancetta until crispy'),
(@carbonara_id, 4, 'Mix eggs, cheese, and pepper in a bowl'),
(@carbonara_id, 5, 'Combine hot pasta with egg mixture and pancetta');
