-- ============================================================
-- 🎮 MONDE MAGIQUE - COMPLETE DATABASE
-- ============================================================
-- Complete Integrated Database Setup
-- Date: April 2026
-- Status: Production Ready
-- Language Support: French and Arabic
-- ============================================================

-- START TRANSACTION
START TRANSACTION;

-- ============================================================
-- SECTION 1: DATABASE INITIALIZATION
-- ============================================================

DROP DATABASE IF EXISTS monde_magique;
CREATE DATABASE monde_magique 
  CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;

USE monde_magique;

-- ============================================================
-- SECTION 2: USERS TABLE
-- ============================================================

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255),
    guide_name VARCHAR(50) NOT NULL,
    guide_gender ENUM('boy', 'girl') NOT NULL DEFAULT 'boy',
    gender ENUM('boy', 'girl'),
    age INT NOT NULL CHECK (age >= 5 AND age <= 18),
    birth_date DATE,
    avatar_url VARCHAR(255) DEFAULT 'assets/images/avatars/default.png',
    level INT DEFAULT 1 CHECK (level >= 1 AND level <= 10),
    xp INT DEFAULT 0 CHECK (xp >= 0),
    coins INT DEFAULT 0 CHECK (coins >= 0),
    diamonds INT DEFAULT 0 CHECK (diamonds >= 0),
    current_stage VARCHAR(50) DEFAULT 'tunisia',
    current_station INT DEFAULT 1,
    total_play_time INT DEFAULT 0,
    
    -- Langue et préférences
    language ENUM('fr', 'ar') DEFAULT 'fr',
    profile_visibility ENUM('public', 'friends_only', 'private') DEFAULT 'public',
    profile_completed BOOLEAN DEFAULT false,
    profile_updated_at TIMESTAMP NULL,
    
    settings JSON DEFAULT '{
        "music": true,
        "sound": true,
        "voice": true,
        "notifications": true,
        "animations": true,
        "difficulty": "auto"
    }',
    
    -- Débloqués
    unlocked_vehicles JSON DEFAULT \'["bicycle"]\',
    guide_outfits JSON DEFAULT \'["basic"]\',
    unlocked_accessories JSON DEFAULT \'[]\',
    
    -- Sécurité et métadonnées
    is_active BOOLEAN DEFAULT true,
    is_verified BOOLEAN DEFAULT false,
    verification_token VARCHAR(100),
    reset_token VARCHAR(100),
    reset_token_expires DATETIME,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_level (level),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- SECTION 3: STAGES TABLE
-- ============================================================

CREATE TABLE stages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    stage_key VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    name_ar VARCHAR(100) NOT NULL,
    title_fr VARCHAR(100),
    title_ar VARCHAR(100),
    icon VARCHAR(10) NOT NULL,
    region VARCHAR(50),
    coordinates_x DECIMAL(10, 6),
    coordinates_y DECIMAL(10, 6),
    latitude DECIMAL(10, 6),
    longitude DECIMAL(10, 6),
    zoom_level INT DEFAULT 7,
    stage_order INT NOT NULL UNIQUE,
    order_num INT,
    required_level INT DEFAULT 1,
    required_score INT DEFAULT 80,
    total_stations INT DEFAULT 7,
    theme_color VARCHAR(20),
    description_en TEXT,
    description_fr TEXT,
    description_ar TEXT,
    estimated_time INT,
    difficulty ENUM('easy', 'medium', 'hard', 'expert') DEFAULT 'easy',
    
    base_rewards JSON DEFAULT '{
        "diamonds": 5,
        "coins": 100,
        "xp": 500
    }',
    perfect_score_bonus JSON DEFAULT '{
        "extra_diamonds": 10,
        "special_item": true
    }',
    unlock_requirements JSON DEFAULT \'[]\',
    
    is_active BOOLEAN DEFAULT true,
    version VARCHAR(10) DEFAULT '1.0',
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_order (stage_order),
    INDEX idx_key (stage_key),
    INDEX idx_difficulty (difficulty)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert Stages Data
INSERT INTO stages (stage_key, name, name_ar, icon, stage_order, required_level, required_score, theme_color, description_en, description_ar) VALUES
('tunisia', 'Tunisia', 'تونس', '🇹🇳', 1, 1, 80, '#FF6B6B', 'Discover the birthplace of your adventure', 'اكتشف مهد مغامرتك'),
('maghreb', 'Maghreb', 'المغرب العربي', '🌍', 2, 1, 80, '#4ECDC4', 'Explore the heart of North Africa', 'استكشف قلب شمال أفريقيا'),
('africa', 'Africa', 'أفريقيا', '🦁', 3, 1, 80, '#FFE66D', 'Adventure across the vast continent', 'مغامرة عبر القارة الشاسعة'),
('europe', 'Europe', 'أوروبا', '🏰', 4, 1, 80, '#95E1D3', 'Journey through European wonders', 'رحلة عبر عجائب أوروبا'),
('asia', 'Asia', 'آسيا', '🏯', 5, 1, 80, '#C7CEEA', 'Experience the mysteries of Asia', 'اختبر ألغاز آسيا'),
('namerica', 'North America', 'أمريكا الشمالية', '🗽', 6, 1, 80, '#FF8B94', 'Explore the New World', 'استكشف العالم الجديد'),
('samerica', 'South America', 'أمريكا الجنوبية', '🌴', 7, 1, 80, '#A8E6CF', 'Discover Amazon wonders', 'اكتشف عجائب الأمازون'),
('oceania', 'Oceania', 'أوقيانوسيا', '🏝️', 8, 1, 80, '#FFD3B6', 'Island hopping adventure', 'مغامرة من جزيرة لأخرى'),
('poles', 'Poles', 'القطبان', '❄️', 9, 1, 80, '#FFAAA5', 'Arctic and Antarctic mysteries', 'ألغاز القطب الشمالي والجنوبي'),
('world', 'World Tour', 'جولة حول العالم', '🌎', 10, 1, 80, '#FFD700', 'Final challenge: World knowledge', 'التحدي النهائي: معرفة عالمية');

-- ============================================================
-- SECTION 4: PROGRESSION TABLES
-- ============================================================

-- USER STAGE PROGRESSION - Complete progression data
CREATE TABLE user_stage_progression (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL UNIQUE,
    progression_data JSON NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_updated (updated_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- STAGE PROGRESS - Individual stage tracking
CREATE TABLE stage_progress (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    stage_id INT NOT NULL,
    
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    total_score INT DEFAULT 0,
    time_spent INT DEFAULT 0,
    
    station_1_completed BOOLEAN DEFAULT false,
    station_1_score INT DEFAULT 0,
    station_1_time INT DEFAULT 0,
    
    station_2_completed BOOLEAN DEFAULT false,
    station_2_score INT DEFAULT 0,
    station_2_time INT DEFAULT 0,
    
    station_3_completed BOOLEAN DEFAULT false,
    station_3_score INT DEFAULT 0,
    station_3_time INT DEFAULT 0,
    
    station_4_completed BOOLEAN DEFAULT false,
    station_4_score INT DEFAULT 0,
    station_4_time INT DEFAULT 0,
    
    station_5_completed BOOLEAN DEFAULT false,
    station_5_score INT DEFAULT 0,
    station_5_time INT DEFAULT 0,
    
    station_6_completed BOOLEAN DEFAULT false,
    station_6_score INT DEFAULT 0,
    station_6_time INT DEFAULT 0,
    
    station_7_completed BOOLEAN DEFAULT false,
    station_7_score INT DEFAULT 0,
    station_7_time INT DEFAULT 0,
    
    perfect_stations INT DEFAULT 0,
    hints_used INT DEFAULT 0,
    chatbot_questions INT DEFAULT 0,
    retry_count INT DEFAULT 0,
    
    rewards_collected BOOLEAN DEFAULT false,
    diamonds_earned INT DEFAULT 0,
    coins_earned INT DEFAULT 0,
    xp_earned INT DEFAULT 0,
    
    vehicle_used VARCHAR(50) DEFAULT 'bicycle',
    outfit_used VARCHAR(50) DEFAULT 'basic',
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_stage (user_id, stage_id),
    INDEX idx_stage_completion (stage_id, completed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- STAGE SCORES - Individual attempt tracking
CREATE TABLE stage_scores (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    stage_key VARCHAR(50) NOT NULL,
    stage_id INT,
    score INT NOT NULL CHECK (score >= 0 AND score <= 100),
    attempts INT DEFAULT 1 CHECK (attempts >= 1),
    attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_stage (user_id, stage_key),
    INDEX idx_score (score),
    INDEX idx_attempted (attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- SECTION 5: STATIONS AND CONTENT
-- ============================================================

CREATE TABLE stations (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    stage_id INT NOT NULL,
    station_number INT NOT NULL,
    
    type ENUM('pdf', 'video', 'game', 'quiz', 'rewards', 'chatbot') NOT NULL,
    
    title_fr VARCHAR(100) NOT NULL,
    title_ar VARCHAR(100) NOT NULL,
    instructions_fr TEXT,
    instructions_ar TEXT,
    
    content_url VARCHAR(255),
    content_data JSON,
    
    duration INT,
    required_score INT DEFAULT 70,
    max_attempts INT DEFAULT 3,
    hints_available INT DEFAULT 3,
    
    questions JSON,
    correct_answers JSON,
    explanations_fr JSON,
    explanations_ar JSON,
    fun_facts JSON,
    
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (stage_id) REFERENCES stages(id) ON DELETE CASCADE,
    UNIQUE KEY unique_stage_station (stage_id, station_number),
    INDEX idx_station_type (type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- SECTION 6: QUIZ QUESTIONS
-- ============================================================

CREATE TABLE quiz_questions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    stage_id INT,
    stage_key VARCHAR(50),
    station_id INT UNSIGNED,
    
    question_type ENUM('text', 'image', 'audio', 'video', 'multiple') DEFAULT 'multiple',
    question_fr TEXT NOT NULL,
    question_ar TEXT NOT NULL,
    question_text VARCHAR(500),
    question_image VARCHAR(255),
    question_audio VARCHAR(255),
    
    options JSON NOT NULL,
    correct_option_id VARCHAR(10),
    correct_answer VARCHAR(255),
    
    explanation_fr TEXT,
    explanation_ar TEXT,
    fun_fact_fr TEXT,
    fun_fact_ar TEXT,
    
    difficulty ENUM('easy', 'medium', 'hard') DEFAULT 'medium',
    difficulty_int INT DEFAULT 1,
    category VARCHAR(50),
    tags JSON DEFAULT \'[]\',
    points INT DEFAULT 5,
    is_active BOOLEAN DEFAULT true,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (stage_id) REFERENCES stages(id) ON DELETE SET NULL,
    FOREIGN KEY (station_id) REFERENCES stations(id) ON DELETE SET NULL,
    INDEX idx_difficulty (difficulty),
    INDEX idx_category (category),
    INDEX idx_stage (stage_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- QUIZ RESULTS
CREATE TABLE quiz_results (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    stage_id INT NOT NULL,
    station_id INT,
    score INT NOT NULL,
    total_questions INT NOT NULL,
    time_spent INT NOT NULL,
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (stage_id) REFERENCES stages(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_score (score)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- SECTION 7: CHATBOT
-- ============================================================

CREATE TABLE chatbot_conversations (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    stage_id INT,
    station_id INT UNSIGNED,
    
    session_id VARCHAR(100) NOT NULL,
    user_message TEXT NOT NULL,
    bot_response TEXT NOT NULL,
    question TEXT,
    answer TEXT,
    
    ai_model VARCHAR(50),
    tokens_used INT,
    confidence_score FLOAT,
    response_time_ms INT,
    
    context JSON,
    language VARCHAR(10) DEFAULT 'fr',
    
    cost_diamonds INT DEFAULT 0,
    is_free BOOLEAN DEFAULT true,
    used_diamond BOOLEAN DEFAULT 0,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_session (session_id),
    INDEX idx_user_conversations (user_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE chatbot_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    stage_id INT,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    used_diamond BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- SECTION 8: ECONOMY - SHOP AND TRANSACTIONS
-- ============================================================

CREATE TABLE shop_items (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    
    item_type ENUM('vehicle', 'outfit', 'accessory', 'powerup', 'chatbot') NOT NULL,
    item_id VARCHAR(50) UNIQUE NOT NULL,
    name_fr VARCHAR(100) NOT NULL,
    name_ar VARCHAR(100) NOT NULL,
    description_fr TEXT,
    description_ar TEXT,
    
    icon_url VARCHAR(255) NOT NULL,
    preview_url VARCHAR(255),
    
    price_coins INT DEFAULT 0,
    price_diamonds INT DEFAULT 0,
    is_premium BOOLEAN DEFAULT false,
    
    unlock_level INT DEFAULT 1,
    unlock_requirements JSON DEFAULT \'[]\',
    stats JSON,
    
    is_active BOOLEAN DEFAULT true,
    is_featured BOOLEAN DEFAULT false,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_item_type (item_type),
    INDEX idx_unlock_level (unlock_level),
    INDEX idx_price (price_coins, price_diamonds)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE transactions (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    
    transaction_type ENUM('purchase', 'reward', 'refund', 'transfer') NOT NULL,
    type ENUM('purchase', 'reward', 'penalty'),
    category VARCHAR(50),
    item_type VARCHAR(50),
    item_id VARCHAR(50),
    item_name VARCHAR(100),
    
    coins_amount INT DEFAULT 0,
    diamonds_amount INT DEFAULT 0,
    xp_amount INT DEFAULT 0,
    amount INT DEFAULT 0,
    currency ENUM('coins', 'diamonds'),
    
    user_coins_before INT,
    user_coins_after INT,
    user_diamonds_before INT,
    user_diamonds_after INT,
    user_xp_before INT,
    user_xp_after INT,
    
    status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'completed',
    reference_id VARCHAR(100),
    notes TEXT,
    description TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_transactions (user_id, created_at),
    INDEX idx_transaction_type (transaction_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- SECTION 9: ACHIEVEMENTS AND REWARDS
-- ============================================================

CREATE TABLE achievements (
    id VARCHAR(50) PRIMARY KEY,
    achievement_id VARCHAR(50),
    
    name VARCHAR(100) NOT NULL,
    name_ar VARCHAR(100) NOT NULL,
    title_fr VARCHAR(100),
    title_ar VARCHAR(100),
    
    description TEXT,
    description_ar TEXT,
    description_fr TEXT,
    description_ar_full TEXT,
    
    icon VARCHAR(10) NOT NULL,
    icon_url VARCHAR(255),
    badge_color VARCHAR(20) DEFAULT '#FFD700',
    
    achievement_type ENUM('stage_complete', 'score', 'time', 'streak', 'collection', 'special') NOT NULL,
    requirement_type VARCHAR(50),
    requirement_value JSON NOT NULL,
    unlock_type VARCHAR(50),
    unlock_value INT,
    
    reward_diamonds INT DEFAULT 0,
    reward_coins INT DEFAULT 0,
    reward_xp INT DEFAULT 0,
    special_reward VARCHAR(100),
    
    is_secret BOOLEAN DEFAULT false,
    is_active BOOLEAN DEFAULT true,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_achievement_type (achievement_type),
    INDEX idx_unlock_type (unlock_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert Achievements
INSERT INTO achievements (id, name, name_ar, icon, achievement_type, requirement_value, reward_diamonds, reward_coins, reward_xp) VALUES
('first_step', 'First Step', 'الخطوة الأولى', '🌟', 'stage_complete', JSON_OBJECT('stage', 1), 5, 50, 100),
('explorer', 'Explorer', 'المستكشف', '🎯', 'collection', JSON_OBJECT('stages', 3), 15, 150, 300),
('traveler', 'Traveler', 'الرحالة', '🧭', 'collection', JSON_OBJECT('stages', 5), 25, 250, 500),
('expert', 'Expert', 'الخبير', '🏅', 'collection', JSON_OBJECT('stages', 7), 40, 400, 800),
('legend', 'Legend', 'الأسطورة', '👑', 'collection', JSON_OBJECT('stages', 10), 100, 1000, 2000),
('perfect_score', 'Perfect Master', 'سيد الكمال', '⭐', 'score', JSON_OBJECT('score', 95), 20, 200, 400),
('collector', 'Collector', 'الجامع', '👕', 'collection', JSON_OBJECT('items', 10), 10, 100, 200),
('diamond_hunter', 'Diamond Hunter', 'صائد الماسات', '💎', 'collection', JSON_OBJECT('diamonds', 100), 50, 500, 1000);

-- USER ACHIEVEMENTS
CREATE TABLE user_achievements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    achievement_id VARCHAR(50) NOT NULL,
    
    progress_current INT DEFAULT 0,
    progress_target INT DEFAULT 1,
    is_unlocked BOOLEAN DEFAULT false,
    
    unlocked_at TIMESTAMP NULL,
    unlocked_stage_id INT,
    unlocked_score INT,
    
    rewards_collected BOOLEAN DEFAULT false,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (achievement_id) REFERENCES achievements(id) ON DELETE CASCADE,
    UNIQUE KEY unique_achievement (user_id, achievement_id),
    INDEX idx_user_achievements (user_id, is_unlocked)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- SECTION 10: USER SESSIONS
-- ============================================================

CREATE TABLE user_sessions (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    
    session_token VARCHAR(255) UNIQUE NOT NULL,
    device_info JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    
    is_active BOOLEAN DEFAULT true,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    logout_time TIMESTAMP NULL,
    expires_at TIMESTAMP NOT NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_session_token (session_token),
    INDEX idx_user_sessions (user_id, is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- SECTION 11: STATISTICS
-- ============================================================

CREATE TABLE user_statistics (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL UNIQUE,
    
    total_play_time INT DEFAULT 0,
    daily_play_time JSON DEFAULT '{}',
    weekly_play_time JSON DEFAULT '{}',
    
    stages_completed INT DEFAULT 0,
    stations_completed INT DEFAULT 0,
    quizzes_completed INT DEFAULT 0,
    perfect_scores INT DEFAULT 0,
    
    current_streak INT DEFAULT 0,
    longest_streak INT DEFAULT 0,
    average_score DECIMAL(5,2) DEFAULT 0,
    
    total_diamonds_earned INT DEFAULT 0,
    total_coins_earned INT DEFAULT 0,
    total_xp_earned INT DEFAULT 0,
    items_purchased INT DEFAULT 0,
    
    achievements_unlocked INT DEFAULT 0,
    chatbot_questions_asked INT DEFAULT 0,
    hints_used INT DEFAULT 0,
    
    last_stage_completed INT,
    last_stage_completed_at TIMESTAMP NULL,
    last_login_date DATE,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- SECTION 12: ACTIVITY LOGS
-- ============================================================

CREATE TABLE activity_logs (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    
    activity_type VARCHAR(50) NOT NULL,
    activity_data JSON,
    
    stage_id INT,
    station_id INT UNSIGNED,
    score INT,
    
    ip_address VARCHAR(45),
    user_agent TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_activities (user_id, created_at),
    INDEX idx_activity_type (activity_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- SECTION 13: FRIENDS AND SOCIAL
-- ============================================================

CREATE TABLE friends (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNSIGNED NOT NULL,
    friend_id INT UNSIGNED NOT NULL,
    
    friendship_status ENUM('active', 'blocked') DEFAULT 'active',
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_friendship (user_id, friend_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (friend_id) REFERENCES users(id) ON DELETE CASCADE,
    
    INDEX idx_user_friends (user_id, friendship_status),
    INDEX idx_friend_lookup (friend_id, friendship_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE friend_requests (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    sender_id INT UNSIGNED NOT NULL,
    receiver_id INT UNSIGNED NOT NULL,
    
    status ENUM('pending', 'accepted', 'rejected', 'cancelled') DEFAULT 'pending',
    message TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    responded_at TIMESTAMP NULL,
    
    UNIQUE KEY unique_request (sender_id, receiver_id, status),
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
    
    INDEX idx_receiver_pending (receiver_id, status),
    INDEX idx_sender_requests (sender_id, status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- SECTION 14: USER SETTINGS
-- ============================================================

CREATE TABLE user_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    setting_key VARCHAR(50) NOT NULL,
    setting_value TEXT,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_setting (user_id, setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- SECTION 15: SAMPLE TEST DATA
-- ============================================================

INSERT INTO users (username, email, password, guide_name, guide_gender, age, level, xp, coins, diamonds, current_stage) VALUES
('demo_child_1', 'demo1@mondmagique.local', 'hashed_password_1', 'أحمد', 'boy', 10, 5, 2500, 500, 50, 'africa'),
('demo_child_2', 'demo2@mondmagique.local', 'hashed_password_2', 'فاطمة', 'girl', 9, 3, 1000, 300, 20, 'maghreb'),
('demo_child_3', 'demo3@mondmagique.local', 'hashed_password_3', 'علي', 'boy', 11, 7, 4500, 1200, 100, 'europe');

INSERT INTO user_stage_progression (user_id, progression_data) VALUES
(1, JSON_OBJECT(
    'tunisia', JSON_OBJECT('passed', true, 'score', 95, 'attempts', 2),
    'maghreb', JSON_OBJECT('passed', true, 'score', 82, 'attempts', 1),
    'africa', JSON_OBJECT('passed', true, 'score', 88, 'attempts', 2)
)),
(2, JSON_OBJECT(
    'tunisia', JSON_OBJECT('passed', true, 'score', 92, 'attempts', 1),
    'maghreb', JSON_OBJECT('passed', true, 'score', 85, 'attempts', 2)
)),
(3, JSON_OBJECT(
    'tunisia', JSON_OBJECT('passed', true, 'score', 98, 'attempts', 1)
));

-- ============================================================
-- SECTION 16: DATABASE VERIFICATION
-- ============================================================

COMMIT;

-- Verify database structure
SELECT '✅ DATABASE STRUCTURE COMPLETE' as status,
       (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'monde_magique') as tables_created;

-- Verify data integrity
SELECT '✅ SAMPLE DATA LOADED' as status,
       (SELECT COUNT(*) FROM users) as total_users,
       (SELECT COUNT(*) FROM stages) as total_stages,
       (SELECT COUNT(*) FROM achievements) as total_achievements;

-- ============================================================
-- END OF COMPLETE DATABASE SETUP
-- ============================================================
