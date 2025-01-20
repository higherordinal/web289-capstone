-- Insert sample users
INSERT INTO user_account (username, email, password_hash, user_level, is_active) VALUES
('hcvaughn', 'henrycvaughn@students.abtech.edu', '$2y$10$tyyDDxa/0IoYKD3nzHACUOkUMcZ2zY.bUN.WletlkaHNcqWbHDtuy', 'u', 1),
('john_chef', 'john@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'u', 1),
('maria_cook', 'maria@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'u', 1),
('admin_chef', 'admin@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'a', 1);
