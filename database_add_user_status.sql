-- Add status column to user table
ALTER TABLE user 
ADD COLUMN status ENUM('active', 'blocked') DEFAULT 'active' AFTER role;

-- Add index for performance
CREATE INDEX idx_user_status ON user(status);
