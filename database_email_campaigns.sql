-- Email Campaigns Table
CREATE TABLE IF NOT EXISTS email_campaigns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    template_name VARCHAR(100),
    subject VARCHAR(255),
    content TEXT,
    ai_generated TINYINT(1) DEFAULT 0,
    sent_at DATETIME,
    status ENUM('sent', 'failed', 'pending') DEFAULT 'pending',
    opened_at DATETIME NULL,
    clicked_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,
    INDEX idx_user_campaigns (user_id),
    INDEX idx_campaign_status (status)
);

-- User Segments Table (for tracking segment history)
CREATE TABLE IF NOT EXISTS user_segments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    segment_type VARCHAR(50),
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,
    INDEX idx_user_segments (user_id)
);
