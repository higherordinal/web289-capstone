-- Insert recipe attributes
INSERT IGNORE INTO recipe_style (name) VALUES 
('American'),
('Italian'),
('Mexican'),
('Chinese'),
('Indian'),
('Japanese'),
('Thai'),
('Mediterranean'),
('French'),
('Korean');

INSERT IGNORE INTO recipe_diet (name) VALUES 
('Vegetarian'),
('Vegan'),
('Gluten-Free'),
('Dairy-Free'),
('Keto'),
('Paleo'),
('Low-Carb'),
('Low-Fat'),
('High-Protein'),
('Mediterranean');

INSERT IGNORE INTO recipe_type (name) VALUES 
('Appetizer'),
('Main Course'),
('Side Dish'),
('Dessert'),
('Breakfast'),
('Lunch'),
('Dinner'),
('Snack'),
('Beverage'),
('Soup');

INSERT IGNORE INTO measurement (name) VALUES 
('teaspoon'),
('tablespoon'),
('cup'),
('ounce'),
('pound'),
('gram'),
('kilogram'),
('milliliter'),
('liter'),
('pinch');

-- Insert sample recipes
INSERT INTO recipe (user_id, title, description, style_id, diet_id, type_id, prep_hours, prep_minutes, cook_hours, cook_minutes, is_featured, created_date, created_time, img_file_path, alt_text) VALUES
-- Classic Spaghetti Carbonara
(1, 'Classic Spaghetti Carbonara', 'Traditional Roman pasta dish with eggs, cheese, pancetta, and black pepper', 
 (SELECT style_id FROM recipe_style WHERE name = 'Italian' LIMIT 1),
 NULL,
 (SELECT type_id FROM recipe_type WHERE name = 'Main Course' LIMIT 1),
 0, 20, 0, 15, TRUE, CURRENT_DATE, CURRENT_TIME,
 'carbonara.jpg',
 'A plate of perfectly cooked spaghetti carbonara with crispy pancetta and fresh black pepper'),

-- Vegetarian Buddha Bowl
(1, 'Colorful Buddha Bowl', 'Healthy and vibrant bowl filled with quinoa, roasted vegetables, and tahini dressing',
 (SELECT style_id FROM recipe_style WHERE name = 'Mediterranean' LIMIT 1),
 (SELECT diet_id FROM recipe_diet WHERE name = 'Vegetarian' LIMIT 1),
 (SELECT type_id FROM recipe_type WHERE name = 'Main Course' LIMIT 1),
 0, 15, 0, 30, TRUE, CURRENT_DATE, CURRENT_TIME,
 'buddha-bowl.jpg',
 'A beautifully arranged vegetarian Buddha bowl featuring crispy tofu, sliced avocado, edamame, shredded carrots, cucumber, fresh herbs, and bean sprouts in a black bowl'),

-- Korean BBQ Tacos
(1, 'Korean BBQ Tacos', 'Fusion dish combining Korean marinated beef with Mexican-style tacos',
 (SELECT style_id FROM recipe_style WHERE name = 'Korean' LIMIT 1),
 NULL,
 (SELECT type_id FROM recipe_type WHERE name = 'Main Course' LIMIT 1),
 1, 0, 0, 30, FALSE, CURRENT_DATE, CURRENT_TIME,
 'korean-tacos.jpg',
 'Three Korean BBQ tacos topped with kimchi slaw and sesame seeds');

-- Set recipe IDs for ingredients and steps
SET @carbonara_id = (SELECT recipe_id FROM recipe WHERE title = 'Classic Spaghetti Carbonara' LIMIT 1);
SET @buddha_bowl_id = (SELECT recipe_id FROM recipe WHERE title = 'Colorful Buddha Bowl' LIMIT 1);
SET @korean_tacos_id = (SELECT recipe_id FROM recipe WHERE title = 'Korean BBQ Tacos' LIMIT 1);

-- Insert ingredients for Spaghetti Carbonara
INSERT INTO recipe_ingredient (recipe_id, measurement_id, quantity, ingredient_text) VALUES
(@carbonara_id, (SELECT measurement_id FROM measurement WHERE name = 'pound' LIMIT 1), 1, 'spaghetti'),
(@carbonara_id, (SELECT measurement_id FROM measurement WHERE name = 'ounce' LIMIT 1), 4, 'pancetta'),
(@carbonara_id, (SELECT measurement_id FROM measurement WHERE name = 'cup' LIMIT 1), 1, 'Pecorino Romano cheese');

-- Insert ingredients for Buddha Bowl
INSERT INTO recipe_ingredient (recipe_id, measurement_id, quantity, ingredient_text) VALUES
(@buddha_bowl_id, (SELECT measurement_id FROM measurement WHERE name = 'cup' LIMIT 1), 1, 'quinoa'),
(@buddha_bowl_id, (SELECT measurement_id FROM measurement WHERE name = 'cup' LIMIT 1), 2, 'mixed vegetables'),
(@buddha_bowl_id, (SELECT measurement_id FROM measurement WHERE name = 'tablespoon' LIMIT 1), 2, 'tahini');

-- Insert ingredients for Korean BBQ Tacos
INSERT INTO recipe_ingredient (recipe_id, measurement_id, quantity, ingredient_text) VALUES
(@korean_tacos_id, (SELECT measurement_id FROM measurement WHERE name = 'pound' LIMIT 1), 1, 'Korean-style marinated beef'),
(@korean_tacos_id, (SELECT measurement_id FROM measurement WHERE name = 'cup' LIMIT 1), 2, 'kimchi slaw'),
(@korean_tacos_id, (SELECT measurement_id FROM measurement WHERE name = 'tablespoon' LIMIT 1), 1, 'sesame seeds');

-- Insert recipe steps for Spaghetti Carbonara
INSERT INTO recipe_step (recipe_id, step_number, instruction) VALUES
(@carbonara_id, 1, 'Bring a large pot of salted water to boil'),
(@carbonara_id, 2, 'Cook spaghetti according to package instructions'),
(@carbonara_id, 3, 'In a pan, cook pancetta until crispy'),
(@carbonara_id, 4, 'Mix eggs, cheese, and pepper in a bowl'),
(@carbonara_id, 5, 'Combine hot pasta with egg mixture and pancetta');

-- Insert recipe steps for Buddha Bowl
INSERT INTO recipe_step (recipe_id, step_number, instruction) VALUES
(@buddha_bowl_id, 1, 'Cook quinoa according to package instructions'),
(@buddha_bowl_id, 2, 'Roast mixed vegetables with olive oil and seasonings'),
(@buddha_bowl_id, 3, 'Prepare tahini dressing'),
(@buddha_bowl_id, 4, 'Arrange quinoa and vegetables in bowls'),
(@buddha_bowl_id, 5, 'Drizzle with tahini dressing and serve');

-- Insert recipe steps for Korean BBQ Tacos
INSERT INTO recipe_step (recipe_id, step_number, instruction) VALUES
(@korean_tacos_id, 1, 'Marinate beef for at least 1 hour'),
(@korean_tacos_id, 2, 'Grill or pan-fry the marinated beef'),
(@korean_tacos_id, 3, 'Warm the tortillas'),
(@korean_tacos_id, 4, 'Fill tortillas with beef'),
(@korean_tacos_id, 5, 'Top with kimchi slaw and sesame seeds');
