-- Insert sample users first (if they don't exist)
INSERT IGNORE INTO user_account (username, email, password_hash, user_level) VALUES
('john_chef', 'john@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'u'),
('maria_cook', 'maria@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'u'),
('admin_chef', 'admin@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'a');

-- Get user IDs for existing users
SET @john_id = (SELECT user_id FROM user_account WHERE username = 'john_chef');
SET @maria_id = (SELECT user_id FROM user_account WHERE username = 'maria_cook');
SET @admin_id = (SELECT user_id FROM user_account WHERE username = 'admin_chef');

-- Insert sample recipes
INSERT INTO recipe (user_id, title, description, style_id, diet_id, type_id, prep_hours, prep_minutes, cook_hours, cook_minutes, is_featured, created_date, created_time) VALUES
-- Classic Spaghetti Carbonara
(@john_id, 'Classic Spaghetti Carbonara', 'Traditional Roman pasta dish with eggs, cheese, pancetta, and black pepper', 
 (SELECT style_id FROM recipe_style WHERE name = 'Italian'),
 NULL,
 (SELECT type_id FROM recipe_type WHERE name = 'Main Course'),
 0, 20, 0, 15, TRUE, CURRENT_DATE, CURRENT_TIME),

-- Vegetarian Buddha Bowl
(@maria_id, 'Colorful Buddha Bowl', 'Healthy and vibrant bowl filled with quinoa, roasted vegetables, and tahini dressing',
 (SELECT style_id FROM recipe_style WHERE name = 'Mediterranean'),
 (SELECT diet_id FROM recipe_diet WHERE name = 'Vegetarian'),
 (SELECT type_id FROM recipe_type WHERE name = 'Main Course'),
 0, 15, 0, 30, TRUE, CURRENT_DATE, CURRENT_TIME),

-- Korean BBQ Tacos
(@john_id, 'Korean BBQ Tacos', 'Fusion dish combining Korean marinated beef with Mexican-style tacos',
 (SELECT style_id FROM recipe_style WHERE name = 'Korean'),
 NULL,
 (SELECT type_id FROM recipe_type WHERE name = 'Main Course'),
 1, 0, 0, 30, FALSE, CURRENT_DATE, CURRENT_TIME);

-- Add image columns to recipe table
ALTER TABLE recipe
ADD COLUMN img_file_path VARCHAR(255) DEFAULT NULL,
ADD COLUMN alt_text TEXT DEFAULT NULL;

-- Update Buddha Bowl image and alt text
UPDATE recipe 
SET img_file_path = '/images/recipes/buddha-bowl.jpg',
    alt_text = 'A beautifully arranged vegetarian Buddha bowl featuring crispy tofu, sliced avocado, edamame, shredded carrots, cucumber, fresh herbs, and bean sprouts in a black bowl, with a small blue and white patterned sauce dish'
WHERE title LIKE '%Buddha Bowl%';

-- Insert ingredients for Spaghetti Carbonara
INSERT INTO recipe_ingredient (recipe_id, measurement_id, quantity, ingredient_text) VALUES
((SELECT recipe_id FROM recipe WHERE title = 'Classic Spaghetti Carbonara'), 
 (SELECT measurement_id FROM measurement WHERE name = 'gram'), 400, 'spaghetti'),
((SELECT recipe_id FROM recipe WHERE title = 'Classic Spaghetti Carbonara'),
 (SELECT measurement_id FROM measurement WHERE name = 'gram'), 200, 'pancetta or guanciale'),
((SELECT recipe_id FROM recipe WHERE title = 'Classic Spaghetti Carbonara'),
 (SELECT measurement_id FROM measurement WHERE name = 'cup'), 1, 'freshly grated Pecorino Romano'),
((SELECT recipe_id FROM recipe WHERE title = 'Classic Spaghetti Carbonara'),
 (SELECT measurement_id FROM measurement WHERE name = 'cup'), 1, 'freshly grated Parmigiano-Reggiano');

-- Insert ingredients for Buddha Bowl
INSERT INTO recipe_ingredient (recipe_id, measurement_id, quantity, ingredient_text) VALUES
((SELECT recipe_id FROM recipe WHERE title = 'Colorful Buddha Bowl'),
 (SELECT measurement_id FROM measurement WHERE name = 'cup'), 1, 'quinoa'),
((SELECT recipe_id FROM recipe WHERE title = 'Colorful Buddha Bowl'),
 (SELECT measurement_id FROM measurement WHERE name = 'cup'), 2, 'mixed roasted vegetables'),
((SELECT recipe_id FROM recipe WHERE title = 'Colorful Buddha Bowl'),
 (SELECT measurement_id FROM measurement WHERE name = 'tablespoon'), 2, 'tahini');

-- Insert ingredients for Korean BBQ Tacos
INSERT INTO recipe_ingredient (recipe_id, measurement_id, quantity, ingredient_text) VALUES
((SELECT recipe_id FROM recipe WHERE title = 'Korean BBQ Tacos'),
 (SELECT measurement_id FROM measurement WHERE name = 'pound'), 1, 'Korean-style marinated beef'),
((SELECT recipe_id FROM recipe WHERE title = 'Korean BBQ Tacos'),
 (SELECT measurement_id FROM measurement WHERE name = 'cup'), 1, 'kimchi'),
((SELECT recipe_id FROM recipe WHERE title = 'Korean BBQ Tacos'),
 (SELECT measurement_id FROM measurement WHERE name = 'cup'), 0.5, 'Korean BBQ sauce');

-- Insert recipe steps for Carbonara
INSERT INTO recipe_step (recipe_id, step_number, instruction) VALUES
((SELECT recipe_id FROM recipe WHERE title = 'Classic Spaghetti Carbonara'), 1, 'Bring a large pot of salted water to boil'),
((SELECT recipe_id FROM recipe WHERE title = 'Classic Spaghetti Carbonara'), 2, 'Cook spaghetti according to package instructions'),
((SELECT recipe_id FROM recipe WHERE title = 'Classic Spaghetti Carbonara'), 3, 'Meanwhile, cook pancetta until crispy'),
((SELECT recipe_id FROM recipe WHERE title = 'Classic Spaghetti Carbonara'), 4, 'Mix eggs, cheese, and pepper in a bowl'),
((SELECT recipe_id FROM recipe WHERE title = 'Classic Spaghetti Carbonara'), 5, 'Combine hot pasta with egg mixture and pancetta');

-- Insert recipe steps for Buddha Bowl
INSERT INTO recipe_step (recipe_id, step_number, instruction) VALUES
((SELECT recipe_id FROM recipe WHERE title = 'Colorful Buddha Bowl'), 1, 'Cook quinoa according to package instructions'),
((SELECT recipe_id FROM recipe WHERE title = 'Colorful Buddha Bowl'), 2, 'Roast mixed vegetables with olive oil and seasonings'),
((SELECT recipe_id FROM recipe WHERE title = 'Colorful Buddha Bowl'), 3, 'Prepare tahini dressing'),
((SELECT recipe_id FROM recipe WHERE title = 'Colorful Buddha Bowl'), 4, 'Assemble bowl with quinoa base, vegetables, and drizzle with dressing');

-- Insert recipe steps for Korean BBQ Tacos
INSERT INTO recipe_step (recipe_id, step_number, instruction) VALUES
((SELECT recipe_id FROM recipe WHERE title = 'Korean BBQ Tacos'), 1, 'Marinate beef for at least 1 hour'),
((SELECT recipe_id FROM recipe WHERE title = 'Korean BBQ Tacos'), 2, 'Grill or pan-fry the marinated beef'),
((SELECT recipe_id FROM recipe WHERE title = 'Korean BBQ Tacos'), 3, 'Warm the tortillas'),
((SELECT recipe_id FROM recipe WHERE title = 'Korean BBQ Tacos'), 4, 'Assemble tacos with beef, kimchi, and sauce');

-- Insert some comments
INSERT INTO recipe_comment (recipe_id, user_id, comment_text) VALUES
((SELECT recipe_id FROM recipe WHERE title = 'Classic Spaghetti Carbonara'), @maria_id, 'Perfect authentic recipe! Just like in Rome.'),
((SELECT recipe_id FROM recipe WHERE title = 'Colorful Buddha Bowl'), @john_id, 'So healthy and filling!'),
((SELECT recipe_id FROM recipe WHERE title = 'Korean BBQ Tacos'), @admin_id, 'Love this fusion concept!');

-- Insert more detailed comments
INSERT INTO recipe_comment (recipe_id, user_id, comment_text, created_date, created_time) VALUES
-- Comments for Carbonara
((SELECT recipe_id FROM recipe WHERE title = 'Classic Spaghetti Carbonara'), @maria_id, 'Perfect authentic recipe! Just like in Rome. The key is really in using good quality Pecorino Romano.', CURRENT_DATE - INTERVAL 5 DAY, '12:30:00'),
((SELECT recipe_id FROM recipe WHERE title = 'Classic Spaghetti Carbonara'), @admin_id, 'Great recipe but I would add a bit more black pepper for extra kick!', CURRENT_DATE - INTERVAL 3 DAY, '15:45:00'),
((SELECT recipe_id FROM recipe WHERE title = 'Classic Spaghetti Carbonara'), @john_id, 'Made this for date night and it was a huge hit. Super creamy without using any cream!', CURRENT_DATE - INTERVAL 1 DAY, '19:20:00'),

-- Comments for Buddha Bowl
((SELECT recipe_id FROM recipe WHERE title = 'Colorful Buddha Bowl'), @john_id, 'So healthy and filling! I added some chickpeas for extra protein.', CURRENT_DATE - INTERVAL 4 DAY, '13:15:00'),
((SELECT recipe_id FROM recipe WHERE title = 'Colorful Buddha Bowl'), @admin_id, 'The tahini dressing really makes this dish special. I make this at least once a week now!', CURRENT_DATE - INTERVAL 2 DAY, '18:30:00'),
((SELECT recipe_id FROM recipe WHERE title = 'Colorful Buddha Bowl'), @maria_id, 'Perfect meal prep recipe. The vegetables stay fresh for days.', CURRENT_DATE - INTERVAL 1 DAY, '11:45:00'),

-- Comments for Korean BBQ Tacos
((SELECT recipe_id FROM recipe WHERE title = 'Korean BBQ Tacos'), @admin_id, 'Love this fusion concept! The kimchi adds the perfect crunch and tang.', CURRENT_DATE - INTERVAL 6 DAY, '20:00:00'),
((SELECT recipe_id FROM recipe WHERE title = 'Korean BBQ Tacos'), @john_id, 'Made these for a party and everyone was impressed. The sauce is incredible!', CURRENT_DATE - INTERVAL 3 DAY, '14:25:00'),
((SELECT recipe_id FROM recipe WHERE title = 'Korean BBQ Tacos'), @maria_id, 'Great flavor combination! I added some sesame seeds on top for extra crunch.', CURRENT_DATE - INTERVAL 1 DAY, '17:50:00');

-- Insert comprehensive ratings
INSERT INTO recipe_rating (recipe_id, user_id, rating_value) VALUES
-- Ratings for Carbonara
((SELECT recipe_id FROM recipe WHERE title = 'Classic Spaghetti Carbonara'), @john_id, 5),
((SELECT recipe_id FROM recipe WHERE title = 'Classic Spaghetti Carbonara'), @maria_id, 5),
((SELECT recipe_id FROM recipe WHERE title = 'Classic Spaghetti Carbonara'), @admin_id, 4),

-- Ratings for Buddha Bowl
((SELECT recipe_id FROM recipe WHERE title = 'Colorful Buddha Bowl'), @john_id, 5),
((SELECT recipe_id FROM recipe WHERE title = 'Colorful Buddha Bowl'), @maria_id, 4),
((SELECT recipe_id FROM recipe WHERE title = 'Colorful Buddha Bowl'), @admin_id, 5),

-- Ratings for Korean BBQ Tacos
((SELECT recipe_id FROM recipe WHERE title = 'Korean BBQ Tacos'), @john_id, 5),
((SELECT recipe_id FROM recipe WHERE title = 'Korean BBQ Tacos'), @maria_id, 4),
((SELECT recipe_id FROM recipe WHERE title = 'Korean BBQ Tacos'), @admin_id, 5);
