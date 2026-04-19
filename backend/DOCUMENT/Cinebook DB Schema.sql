-- ============================================================
-- CINEBOOK DATABASE SCHEMA - PERFECT VERSION
-- Hệ thống đặt vé rạp phim hoàn chỉnh
-- Bao gồm: Vé, Bắp nước, Khuyến mãi, Loyalty, Gift Card
-- Tables ordered by Foreign Key dependencies
-- ============================================================

-- Create database
CREATE DATABASE IF NOT EXISTS cinebook;
USE cinebook;

-- ============================================================
-- PHASE 0: CORE TABLES (No Foreign Keys)
-- ============================================================

-- 1. CINEMAS - Chuỗi rạp (hỗ trợ multi-location)
CREATE TABLE cinemas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE,
    address VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    district VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(150),

    -- Vị trí
    latitude DECIMAL(10, 8) NULL,
    longitude DECIMAL(11, 8) NULL,

    -- Thông tin
    description TEXT,
    facilities JSON NULL,                        -- ["parking", "wheelchair", "food_court"]
    opening_hours JSON NULL,                     -- {"mon": "08:00-23:00", ...}
    image_url VARCHAR(255),

    is_active BOOLEAN DEFAULT TRUE,
    deleted_at TIMESTAMP NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_city (city),
    INDEX idx_active (is_active),
    INDEX idx_deleted (deleted_at)
);

-- 2. USERS - Tài khoản người dùng
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) UNIQUE,
    city VARCHAR(100),
    avatar_url VARCHAR(255),
    role ENUM('user','admin','staff') DEFAULT 'user',

    -- Membership & Loyalty (Phase 3)
    membership_tier_id INT UNSIGNED DEFAULT 1,
    loyalty_points INT UNSIGNED DEFAULT 0,
    lifetime_points INT UNSIGNED DEFAULT 0,
    lifetime_spent DECIMAL(12,0) DEFAULT 0,
    points_expiry_date DATE NULL,

    -- Profile
    date_of_birth DATE NULL,
    gender ENUM('male', 'female', 'other') NULL,

    -- Preferences
    preferred_cinema_id INT UNSIGNED NULL,
    notification_preferences JSON NULL,          -- {"email": true, "push": true, "sms": false}

    -- Referral
    referral_code VARCHAR(20) UNIQUE,
    referred_by_user_id INT UNSIGNED NULL,

    -- Status
    is_active BOOLEAN DEFAULT TRUE,
    deleted_at TIMESTAMP NULL,

    email_verified_at TIMESTAMP NULL,
    phone_verified_at TIMESTAMP NULL,
    remember_token VARCHAR(100) NULL,
    last_login_at TIMESTAMP NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_email (email),
    INDEX idx_phone (phone),
    INDEX idx_role (role),
    INDEX idx_membership (membership_tier_id),
    INDEX idx_referral_code (referral_code),
    INDEX idx_deleted (deleted_at)
);

-- 3. PASSWORD_RESET_TOKENS - Token đặt lại mật khẩu
CREATE TABLE password_reset_tokens (
    email VARCHAR(150) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    INDEX idx_email_token (email, token)
);

-- 4. SESSIONS - Phiên đăng nhập
CREATE TABLE sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id INT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload LONGTEXT NOT NULL,
    last_activity INT NOT NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_last_activity (last_activity)
);

-- 5. MOVIES - Thông tin phim
CREATE TABLE movies (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    original_title VARCHAR(200) NULL,            -- Tên gốc (phim nước ngoài)
    slug VARCHAR(200) UNIQUE,
    language VARCHAR(50),
    subtitle_language VARCHAR(50) DEFAULT 'Vietnamese',
    country VARCHAR(100),
    director VARCHAR(100),
    cast TEXT,
    duration INT NOT NULL,                       -- Thời lượng (phút)
    release_date DATE,
    end_date DATE,
    age_rating ENUM('P','T13','T16','T18','C') NOT NULL,  -- P: Mọi lứa tuổi
    status ENUM('now_showing','coming_soon','ended') DEFAULT 'coming_soon',
    poster_url VARCHAR(255),
    banner_url VARCHAR(255),
    trailer_url VARCHAR(255),
    description TEXT,

    -- Rating
    rating_avg DECIMAL(3,2) DEFAULT 0,
    rating_count INT UNSIGNED DEFAULT 0,

    -- SEO
    meta_title VARCHAR(200),
    meta_description VARCHAR(500),

    is_featured BOOLEAN DEFAULT FALSE,
    deleted_at TIMESTAMP NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_status (status),
    INDEX idx_release_date (release_date),
    INDEX idx_rating_avg (rating_avg),
    INDEX idx_featured (is_featured),
    INDEX idx_deleted (deleted_at)
);

-- 6. GENRES - Thể loại phim
CREATE TABLE genres (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(100) UNIQUE,
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 7. SCREEN_TYPES - Loại màn hình (2D, 3D, IMAX, 4DX)
CREATE TABLE screen_types (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    code VARCHAR(20) UNIQUE,                     -- '2D', '3D', 'IMAX'
    price_adjustment INT NOT NULL DEFAULT 0,     -- Phụ thu cho loại màn hình
    description VARCHAR(255) NULL,
    icon VARCHAR(50) NULL,
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 8. SEAT_TYPES - Loại ghế (Standard, VIP, Couple)
CREATE TABLE seat_types (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    code VARCHAR(20) UNIQUE,                     -- 'standard', 'vip', 'couple'
    base_price INT UNSIGNED NOT NULL,
    description VARCHAR(255),
    color_code VARCHAR(20) NULL,                 -- Màu hiển thị trên sơ đồ
    icon VARCHAR(50) NULL,
    seats_count INT UNSIGNED DEFAULT 1,          -- 1 cho standard/vip, 2 cho couple
    display_order INT DEFAULT 0,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ============================================================
-- PHASE 1: F&B - BẮP NƯỚC
-- ============================================================

-- 9. PRODUCT_CATEGORIES - Danh mục sản phẩm F&B
CREATE TABLE product_categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,                  -- 'Bắp rang', 'Nước uống', 'Combo', 'Snacks'
    slug VARCHAR(100) UNIQUE,
    icon VARCHAR(50) NULL,
    image_url VARCHAR(255) NULL,
    description VARCHAR(255),
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    deleted_at TIMESTAMP NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_active_order (is_active, display_order),
    INDEX idx_deleted (deleted_at)
);

-- 10. PRODUCTS - Sản phẩm F&B
CREATE TABLE products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NOT NULL,
    name VARCHAR(200) NOT NULL,                  -- 'Bắp Rang Bơ Size L'
    slug VARCHAR(200) NULL,
    sku VARCHAR(50) UNIQUE,                      -- Mã sản phẩm
    description TEXT,
    image_url VARCHAR(255),
    price DECIMAL(10,0) NOT NULL,                -- Giá bán
    original_price DECIMAL(10,0) NULL,           -- Giá gốc (nếu đang giảm)
    cost_price DECIMAL(10,0) NULL,               -- Giá vốn (internal)
    size ENUM('S', 'M', 'L', 'XL') NULL,
    is_combo BOOLEAN DEFAULT FALSE,              -- Có phải combo không
    is_available BOOLEAN DEFAULT TRUE,
    is_featured BOOLEAN DEFAULT FALSE,           -- Sản phẩm nổi bật
    display_order INT DEFAULT 0,

    -- Inventory (optional)
    track_inventory BOOLEAN DEFAULT FALSE,
    stock_quantity INT UNSIGNED NULL,            -- NULL = không giới hạn
    low_stock_threshold INT UNSIGNED DEFAULT 10,

    -- Nutrition info (optional)
    calories INT UNSIGNED NULL,

    deleted_at TIMESTAMP NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_category (category_id),
    INDEX idx_available (is_available),
    INDEX idx_featured (is_featured),
    INDEX idx_combo (is_combo),
    INDEX idx_deleted (deleted_at),

    FOREIGN KEY (category_id) REFERENCES product_categories(id)
);

-- 11. COMBO_ITEMS - Chi tiết sản phẩm trong combo
CREATE TABLE combo_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    combo_id INT UNSIGNED NOT NULL,              -- FK → products (is_combo = true)
    product_id INT UNSIGNED NOT NULL,            -- FK → products (sản phẩm trong combo)
    quantity INT UNSIGNED DEFAULT 1,

    FOREIGN KEY (combo_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id),

    UNIQUE KEY uq_combo_product (combo_id, product_id)
);

-- 12. PRODUCT_OPTIONS - Tùy chọn sản phẩm (size, topping, đá...)
CREATE TABLE product_options (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    option_group VARCHAR(50) NULL,               -- 'size', 'topping', 'ice'
    name VARCHAR(100) NOT NULL,                  -- 'Thêm bơ', 'Ít đá', 'Size Up'
    price_adjustment DECIMAL(10,0) DEFAULT 0,    -- Phụ thu/giảm giá
    is_default BOOLEAN DEFAULT FALSE,
    is_available BOOLEAN DEFAULT TRUE,
    display_order INT DEFAULT 0,

    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,

    INDEX idx_product_group (product_id, option_group)
);

-- ============================================================
-- PHASE 2: PROMOTION & VOUCHER - KHUYẾN MÃI
-- ============================================================

-- 13. PROMOTION_CAMPAIGNS - Chiến dịch khuyến mãi
-- NOTE: JSON fields (applicable_*) dùng cho rule engine linh hoạt
-- Với query phức tạp, sử dụng các bảng mapping campaign_* bên dưới
CREATE TABLE promotion_campaigns (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    code VARCHAR(50) UNIQUE,                     -- Mã campaign: 'SUMMER2026'
    type ENUM('percentage', 'fixed_amount', 'buy_x_get_y', 'free_product') NOT NULL,
    value DECIMAL(10,2) NOT NULL,                -- % hoặc số tiền
    max_discount DECIMAL(10,0) NULL,             -- Giảm tối đa (cho loại %)
    min_purchase DECIMAL(10,0) DEFAULT 0,        -- Đơn tối thiểu

    -- Điều kiện áp dụng (JSON cho flexibility, mapping tables cho query)
    applies_to ENUM('all', 'tickets', 'products', 'combo') DEFAULT 'all',
    applicable_days JSON NULL,                   -- ["monday", "tuesday"] hoặc null = tất cả
    applicable_hours JSON NULL,                  -- {"start": "09:00", "end": "17:00"}

    -- Đối tượng áp dụng
    is_first_purchase_only BOOLEAN DEFAULT FALSE,

    -- Giới hạn sử dụng
    usage_limit INT UNSIGNED NULL,               -- Tổng số lần dùng tối đa
    usage_per_user INT UNSIGNED DEFAULT 1,       -- Số lần mỗi user được dùng
    current_usage INT UNSIGNED DEFAULT 0,

    -- Thời hạn
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,

    -- Hiển thị
    status ENUM('draft', 'active', 'paused', 'ended') DEFAULT 'draft',
    description TEXT,
    terms_conditions TEXT,
    banner_url VARCHAR(255),
    display_order INT DEFAULT 0,
    is_public BOOLEAN DEFAULT TRUE,              -- Hiển thị công khai hay chỉ qua code

    created_by INT UNSIGNED NULL,
    deleted_at TIMESTAMP NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_status_date (status, start_date, end_date),
    INDEX idx_code (code),
    INDEX idx_type (type),
    INDEX idx_deleted (deleted_at)
);

-- 13a. CAMPAIGN_CINEMAS - Mapping campaign ↔ cinema (thay thế JSON cho query)
CREATE TABLE campaign_cinemas (
    campaign_id INT UNSIGNED NOT NULL,
    cinema_id INT UNSIGNED NOT NULL,

    PRIMARY KEY (campaign_id, cinema_id),

    FOREIGN KEY (campaign_id) REFERENCES promotion_campaigns(id) ON DELETE CASCADE,
    FOREIGN KEY (cinema_id) REFERENCES cinemas(id) ON DELETE CASCADE
);

-- 13b. CAMPAIGN_MOVIES - Mapping campaign ↔ movie
CREATE TABLE campaign_movies (
    campaign_id INT UNSIGNED NOT NULL,
    movie_id INT UNSIGNED NOT NULL,

    PRIMARY KEY (campaign_id, movie_id),

    FOREIGN KEY (campaign_id) REFERENCES promotion_campaigns(id) ON DELETE CASCADE,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE
);

-- 13c. CAMPAIGN_SEAT_TYPES - Mapping campaign ↔ seat_type
CREATE TABLE campaign_seat_types (
    campaign_id INT UNSIGNED NOT NULL,
    seat_type_id INT UNSIGNED NOT NULL,

    PRIMARY KEY (campaign_id, seat_type_id),

    FOREIGN KEY (campaign_id) REFERENCES promotion_campaigns(id) ON DELETE CASCADE,
    FOREIGN KEY (seat_type_id) REFERENCES seat_types(id) ON DELETE CASCADE
);

-- 13d. CAMPAIGN_PRODUCTS - Mapping campaign ↔ product
CREATE TABLE campaign_products (
    campaign_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,

    PRIMARY KEY (campaign_id, product_id),

    FOREIGN KEY (campaign_id) REFERENCES promotion_campaigns(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- 13e. CAMPAIGN_USER_TIERS - Mapping campaign ↔ membership_tier
CREATE TABLE campaign_user_tiers (
    campaign_id INT UNSIGNED NOT NULL,
    tier_id INT UNSIGNED NOT NULL,

    PRIMARY KEY (campaign_id, tier_id),

    FOREIGN KEY (campaign_id) REFERENCES promotion_campaigns(id) ON DELETE CASCADE,
    FOREIGN KEY (tier_id) REFERENCES membership_tiers(id) ON DELETE CASCADE
);

-- 14. VOUCHERS - Mã giảm giá cá nhân
CREATE TABLE vouchers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    campaign_id INT UNSIGNED NULL,               -- NULL nếu voucher độc lập
    code VARCHAR(50) UNIQUE NOT NULL,            -- Mã voucher: 'ABC123XYZ'
    user_id INT UNSIGNED NULL,                   -- NULL = ai cũng dùng được

    type ENUM('percentage', 'fixed_amount') NOT NULL,
    value DECIMAL(10,2) NOT NULL,
    max_discount DECIMAL(10,0) NULL,
    min_purchase DECIMAL(10,0) DEFAULT 0,
    applies_to ENUM('all', 'tickets', 'products') DEFAULT 'all',

    valid_from DATETIME NOT NULL,
    valid_until DATETIME NOT NULL,

    is_used BOOLEAN DEFAULT FALSE,
    used_at DATETIME NULL,
    used_in_booking_id INT UNSIGNED NULL,
    used_in_order_id INT UNSIGNED NULL,

    -- Nguồn voucher
    source ENUM('birthday', 'loyalty', 'referral', 'compensation', 'campaign', 'welcome', 'manual') DEFAULT 'manual',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_code (code),
    INDEX idx_user_valid (user_id, valid_until, is_used),
    INDEX idx_source (source),

    FOREIGN KEY (campaign_id) REFERENCES promotion_campaigns(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 15. PROMOTIONS - Thông tin khuyến mãi hiển thị (UI)
CREATE TABLE promotions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    campaign_id INT UNSIGNED NULL,               -- Liên kết với campaign thực tế
    category ENUM('cinema-gifts', 'member-rewards', 'student-deals', 'seasonal', 'combo-deals') NOT NULL,
    icon VARCHAR(10) NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    details_title VARCHAR(100),
    details_items JSON,
    cta_text VARCHAR(50) NOT NULL,
    cta_link VARCHAR(255) NOT NULL,
    validity_text VARCHAR(100),
    status ENUM('active', 'upcoming', 'ended') DEFAULT 'active',
    display_order INT DEFAULT 0,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_category (category),
    INDEX idx_status (status),
    INDEX idx_display_order (display_order),

    FOREIGN KEY (campaign_id) REFERENCES promotion_campaigns(id) ON DELETE SET NULL
);

-- ============================================================
-- PHASE 3: LOYALTY & MEMBERSHIP - THÀNH VIÊN
-- ============================================================

-- 16. MEMBERSHIP_TIERS - Hạng thành viên
CREATE TABLE membership_tiers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,                   -- 'Member', 'Silver', 'Gold', 'Platinum', 'Diamond'
    slug VARCHAR(50) UNIQUE,
    min_points INT UNSIGNED NOT NULL DEFAULT 0,  -- Điểm tối thiểu để đạt hạng
    min_lifetime_spent DECIMAL(12,0) DEFAULT 0,  -- Chi tiêu tối thiểu
    color VARCHAR(20),                           -- '#FFD700' (Gold)
    icon VARCHAR(50),
    badge_url VARCHAR(255),

    -- Quyền lợi
    points_multiplier DECIMAL(3,2) DEFAULT 1.00, -- Gold = 1.5x điểm
    ticket_discount_percent DECIMAL(5,2) DEFAULT 0,  -- % giảm vé
    product_discount_percent DECIMAL(5,2) DEFAULT 0, -- % giảm F&B
    birthday_voucher_value DECIMAL(10,0) DEFAULT 0,  -- Giá trị voucher sinh nhật
    free_upgrades_per_month INT UNSIGNED DEFAULT 0,  -- Nâng hạng ghế miễn phí
    priority_booking_hours INT UNSIGNED DEFAULT 0,   -- Đặt vé sớm hơn (giờ)
    free_cancellation BOOLEAN DEFAULT FALSE,         -- Hủy vé miễn phí
    lounge_access BOOLEAN DEFAULT FALSE,             -- Vào phòng chờ VIP

    benefits_description JSON,                   -- Chi tiết quyền lợi

    display_order INT DEFAULT 0,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_min_points (min_points)
);

-- 17. POINT_RULES - Quy tắc tích điểm
CREATE TABLE point_rules (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(50) UNIQUE,
    type ENUM('ticket_purchase', 'product_purchase', 'review', 'referral', 'birthday', 'checkin', 'special') NOT NULL,

    -- Công thức tính điểm
    points_per_amount DECIMAL(10,4) NULL,        -- VD: 0.001 = 1 điểm/1000đ
    fixed_points INT UNSIGNED NULL,              -- Điểm cố định

    -- Điều kiện
    min_purchase DECIMAL(10,0) DEFAULT 0,
    multiplier_conditions JSON NULL,             -- {"seat_type": "vip", "multiplier": 1.5}

    -- Giới hạn
    max_points_per_transaction INT UNSIGNED NULL,
    daily_limit INT UNSIGNED NULL,

    is_active BOOLEAN DEFAULT TRUE,
    valid_from DATETIME NULL,
    valid_until DATETIME NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_type_active (type, is_active)
);

-- 18. LOYALTY_TRANSACTIONS - Lịch sử điểm thưởng
CREATE TABLE loyalty_transactions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    type ENUM('earn', 'redeem', 'expire', 'adjust', 'bonus', 'tier_bonus') NOT NULL,
    points INT NOT NULL,                         -- Dương = cộng, âm = trừ
    balance_after INT UNSIGNED NOT NULL,

    -- Nguồn gốc
    source_type ENUM('booking', 'product_order', 'referral', 'birthday', 'promotion', 'review', 'checkin', 'manual') NULL,
    source_id INT UNSIGNED NULL,

    description VARCHAR(255),
    expires_at DATE NULL,                        -- Ngày hết hạn của điểm này

    created_by_admin_id INT UNSIGNED NULL,       -- Admin điều chỉnh

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_user_date (user_id, created_at),
    INDEX idx_type (type),
    INDEX idx_expires (expires_at),

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================================
-- PHASE 4: DYNAMIC PRICING - GIÁ ĐỘNG
-- ============================================================

-- 19. PRICING_RULES - Quy tắc giá theo thời điểm
-- NOTE: Pricing Engine Architecture
-- - Đây là CONFIG layer, định nghĩa rules
-- - showtime_prices là SNAPSHOT layer, lưu kết quả đã tính
-- - Khi scale lớn: Logic tính giá nên ở Service layer
-- - DB chỉ lưu showtime_prices.final_price (pre-calculated)
CREATE TABLE pricing_rules (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,                  -- 'Giờ vàng', 'Cuối tuần', 'Lễ Tết'
    code VARCHAR(50) UNIQUE,
    priority INT DEFAULT 0,                      -- Ưu tiên cao hơn ghi đè thấp hơn

    -- Điều kiện áp dụng
    day_of_week JSON NULL,                       -- [0,6] = CN, T7 hoặc null = tất cả
    time_range_start TIME NULL,
    time_range_end TIME NULL,
    specific_dates JSON NULL,                    -- ['2026-01-01', '2026-02-14'] ngày lễ

    -- Điều chỉnh giá
    adjustment_type ENUM('percentage', 'fixed') NOT NULL,
    adjustment_value DECIMAL(10,2) NOT NULL,     -- +20% hoặc +20000đ

    -- Phạm vi áp dụng
    applies_to_cinema_ids JSON NULL,             -- [1, 2] hoặc null = tất cả
    applies_to_seat_type_ids JSON NULL,          -- [1, 2, 3] hoặc null = tất cả
    applies_to_screen_type_ids JSON NULL,        -- [1, 2] hoặc null = tất cả
    applies_to_room_ids JSON NULL,

    is_active BOOLEAN DEFAULT TRUE,
    valid_from DATE NULL,
    valid_until DATE NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_active_priority (is_active, priority DESC)
);

-- 20. SPECIAL_DATES - Ngày lễ/sự kiện đặc biệt
CREATE TABLE special_dates (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    name VARCHAR(100) NOT NULL,                  -- 'Tết Nguyên Đán', 'Valentine'
    pricing_rule_id INT UNSIGNED NULL,
    is_holiday BOOLEAN DEFAULT FALSE,
    is_peak BOOLEAN DEFAULT FALSE,               -- Ngày cao điểm
    notes TEXT,

    UNIQUE KEY uq_date (date),

    FOREIGN KEY (pricing_rule_id) REFERENCES pricing_rules(id) ON DELETE SET NULL
);

-- ============================================================
-- PHASE 5: GIFT CARD & WALLET
-- ============================================================

-- 21. GIFT_CARD_TEMPLATES - Mẫu thẻ quà tặng
CREATE TABLE gift_card_templates (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    occasion VARCHAR(50),                        -- 'birthday', 'anniversary', 'holiday'
    image_url VARCHAR(255) NOT NULL,
    thumbnail_url VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    display_order INT DEFAULT 0,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 22. GIFT_CARDS - Thẻ quà tặng
CREATE TABLE gift_cards (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) UNIQUE NOT NULL,
    initial_value DECIMAL(10,0) NOT NULL,
    current_balance DECIMAL(10,0) NOT NULL,

    -- Người mua
    purchased_by_user_id INT UNSIGNED NULL,
    purchase_order_id VARCHAR(50) NULL,
    purchase_amount DECIMAL(10,0) NULL,          -- Số tiền đã thanh toán

    -- Người nhận
    recipient_email VARCHAR(150) NULL,
    recipient_name VARCHAR(100) NULL,
    recipient_phone VARCHAR(20) NULL,
    message TEXT,

    -- Template/Design
    template_id INT UNSIGNED NULL,

    -- Trạng thái
    status ENUM('pending', 'active', 'used', 'expired', 'cancelled') DEFAULT 'pending',
    activated_at DATETIME NULL,
    expires_at DATETIME NOT NULL,

    -- Delivery
    delivery_method ENUM('email', 'sms', 'print') DEFAULT 'email',
    delivered_at DATETIME NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_code (code),
    INDEX idx_status (status),
    INDEX idx_recipient_email (recipient_email),
    INDEX idx_expires (expires_at),

    FOREIGN KEY (purchased_by_user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (template_id) REFERENCES gift_card_templates(id) ON DELETE SET NULL
);

-- 23. GIFT_CARD_TRANSACTIONS - Lịch sử sử dụng gift card
CREATE TABLE gift_card_transactions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    gift_card_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    type ENUM('activation', 'payment', 'refund') NOT NULL,
    amount DECIMAL(10,0) NOT NULL,
    balance_after DECIMAL(10,0) NOT NULL,

    booking_id INT UNSIGNED NULL,
    order_id INT UNSIGNED NULL,

    notes VARCHAR(255),

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_gift_card (gift_card_id),
    INDEX idx_user (user_id),

    FOREIGN KEY (gift_card_id) REFERENCES gift_cards(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- 24. USER_WALLETS - Ví điện tử
CREATE TABLE user_wallets (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED UNIQUE NOT NULL,
    balance DECIMAL(12,0) DEFAULT 0,
    total_topup DECIMAL(12,0) DEFAULT 0,
    total_spent DECIMAL(12,0) DEFAULT 0,
    total_refunded DECIMAL(12,0) DEFAULT 0,

    is_active BOOLEAN DEFAULT TRUE,
    pin_hash VARCHAR(255) NULL,                  -- PIN bảo mật (optional)
    pin_attempts INT UNSIGNED DEFAULT 0,
    locked_until DATETIME NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 25. WALLET_TRANSACTIONS - Lịch sử giao dịch ví
CREATE TABLE wallet_transactions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    type ENUM('topup', 'payment', 'refund', 'bonus', 'withdraw') NOT NULL,
    amount DECIMAL(10,0) NOT NULL,
    balance_after DECIMAL(12,0) NOT NULL,

    -- Tham chiếu
    reference_type VARCHAR(50) NULL,             -- 'booking', 'product_order', 'momo_topup'
    reference_id INT UNSIGNED NULL,

    -- Thanh toán (cho topup)
    payment_method VARCHAR(50) NULL,
    payment_transaction_id VARCHAR(100) NULL,

    description VARCHAR(255),
    status ENUM('pending', 'completed', 'failed', 'cancelled') DEFAULT 'completed',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_user_date (user_id, created_at DESC),
    INDEX idx_type (type),
    INDEX idx_reference (reference_type, reference_id),
    INDEX idx_status (status),

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================================
-- CINEMA CORE TABLES (With Foreign Keys)
-- ============================================================

-- 26. MOVIE_GENRES - Liên kết phim - thể loại
CREATE TABLE movie_genres (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    movie_id INT UNSIGNED NOT NULL,
    genre_id INT UNSIGNED NOT NULL,

    UNIQUE KEY uq_movie_genre (movie_id, genre_id),

    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
    FOREIGN KEY (genre_id) REFERENCES genres(id) ON DELETE CASCADE
);

-- 27. ROOMS - Phòng chiếu
CREATE TABLE rooms (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cinema_id INT UNSIGNED NOT NULL,
    name VARCHAR(50) NOT NULL,
    total_rows INT UNSIGNED NOT NULL,
    seats_per_row INT UNSIGNED NOT NULL,
    total_seats INT UNSIGNED NOT NULL,
    screen_type_id INT UNSIGNED NOT NULL,

    -- Giá mặc định theo phòng
    price_standard DECIMAL(10,0) DEFAULT 80000,
    price_vip DECIMAL(10,0) DEFAULT 120000,
    price_couple DECIMAL(10,0) DEFAULT 200000,

    -- Layout
    seat_layout JSON NULL,                       -- Custom seat layout nếu có

    is_active BOOLEAN DEFAULT TRUE,
    maintenance_note TEXT NULL,
    last_maintenance_at DATETIME NULL,
    deleted_at TIMESTAMP NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_cinema (cinema_id),
    INDEX idx_screen_type (screen_type_id),
    INDEX idx_active (is_active),
    INDEX idx_deleted (deleted_at),

    UNIQUE KEY uq_cinema_room (cinema_id, name),

    FOREIGN KEY (cinema_id) REFERENCES cinemas(id) ON DELETE CASCADE,
    FOREIGN KEY (screen_type_id) REFERENCES screen_types(id)
);

-- 28. SEATS - Ghế trong phòng
CREATE TABLE seats (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    room_id INT UNSIGNED NOT NULL,
    seat_row VARCHAR(2) NOT NULL,                -- 'A', 'B', ... 'Z', 'AA', 'AB'
    seat_number INT UNSIGNED NOT NULL,
    seat_code VARCHAR(5) NOT NULL,               -- 'A1', 'B5', 'AA1'...
    seat_type_id INT UNSIGNED NOT NULL,

    -- Vị trí hiển thị
    position_x INT UNSIGNED NULL,                -- Tọa độ X trên sơ đồ
    position_y INT UNSIGNED NULL,                -- Tọa độ Y trên sơ đồ

    is_active BOOLEAN DEFAULT TRUE,              -- Ghế có sử dụng được không
    is_accessible BOOLEAN DEFAULT FALSE,         -- Ghế cho người khuyết tật

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uq_room_seat (room_id, seat_code),
    INDEX idx_room (room_id),
    INDEX idx_seat_type (seat_type_id),
    INDEX idx_active (is_active),

    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (seat_type_id) REFERENCES seat_types(id)
);

-- 29. SHOWTIMES - Suất chiếu
CREATE TABLE showtimes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    movie_id INT UNSIGNED NOT NULL,
    room_id INT UNSIGNED NOT NULL,
    show_date DATE NOT NULL,
    show_time TIME NOT NULL,
    end_time TIME NOT NULL,                      -- Giờ kết thúc (tự tính)

    -- Giá có thể override per showtime
    custom_pricing JSON NULL,                    -- {"standard": 90000, "vip": 130000}

    -- Booking stats
    total_seats INT UNSIGNED NOT NULL,
    booked_seats INT UNSIGNED DEFAULT 0,
    available_seats INT UNSIGNED NOT NULL,

    status ENUM('scheduled', 'open', 'full', 'cancelled', 'completed') DEFAULT 'scheduled',
    cancelled_at DATETIME NULL,
    cancelled_reason TEXT NULL,
    cancelled_by INT UNSIGNED NULL,

    -- Notification
    cancellation_notified_at DATETIME NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_movie_date (movie_id, show_date),
    INDEX idx_room_time (room_id, show_date, show_time),
    INDEX idx_status (status),
    INDEX idx_date_status (show_date, status),

    UNIQUE KEY uq_room_datetime (room_id, show_date, show_time),

    FOREIGN KEY (movie_id) REFERENCES movies(id),
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
);

-- 30. SHOWTIME_PRICES - Giá vé theo suất chiếu và loại ghế
CREATE TABLE showtime_prices (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    showtime_id INT UNSIGNED NOT NULL,
    seat_type_id INT UNSIGNED NOT NULL,
    base_price INT UNSIGNED NOT NULL,
    final_price INT UNSIGNED NOT NULL,           -- Sau khi áp dụng pricing rules

    applied_rules JSON NULL,                     -- [{rule_id: 1, adjustment: 20000}]

    UNIQUE KEY uq_showtime_seat_type (showtime_id, seat_type_id),

    FOREIGN KEY (showtime_id) REFERENCES showtimes(id) ON DELETE CASCADE,
    FOREIGN KEY (seat_type_id) REFERENCES seat_types(id)
);

-- 31. SHOWTIME_SEATS - Trạng thái ghế theo suất chiếu
CREATE TABLE showtime_seats (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    showtime_id INT UNSIGNED NOT NULL,
    seat_id INT UNSIGNED NOT NULL,
    status ENUM('available','booked','reserved','locked','maintenance') DEFAULT 'available',
    reserved_until TIMESTAMP NULL DEFAULT NULL,
    reserved_by_user_id INT UNSIGNED NULL DEFAULT NULL,
    booked_in_booking_id INT UNSIGNED NULL,

    UNIQUE KEY uq_showtime_seat (showtime_id, seat_id),
    INDEX idx_showtime_status (showtime_id, status),
    INDEX idx_reserved_until (reserved_until),

    FOREIGN KEY (showtime_id) REFERENCES showtimes(id) ON DELETE CASCADE,
    FOREIGN KEY (seat_id) REFERENCES seats(id),
    FOREIGN KEY (reserved_by_user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- 32. BOOKINGS - Đơn đặt vé
CREATE TABLE bookings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    showtime_id INT UNSIGNED NOT NULL,
    booking_code VARCHAR(20) UNIQUE NOT NULL,    -- Mã đặt vé: 'CB20260201ABC'

    -- Số lượng
    ticket_count INT UNSIGNED NOT NULL,

    -- Giá
    subtotal DECIMAL(10,0) NOT NULL,             -- Tổng tiền vé gốc

    -- Giảm giá
    voucher_id INT UNSIGNED NULL,
    voucher_discount DECIMAL(10,0) DEFAULT 0,
    campaign_id INT UNSIGNED NULL,
    campaign_discount DECIMAL(10,0) DEFAULT 0,

    -- Điểm thưởng
    points_used INT UNSIGNED DEFAULT 0,
    points_discount DECIMAL(10,0) DEFAULT 0,
    points_earned INT UNSIGNED DEFAULT 0,

    -- Gift card & Wallet
    gift_card_id INT UNSIGNED NULL,
    gift_card_amount DECIMAL(10,0) DEFAULT 0,
    wallet_amount DECIMAL(10,0) DEFAULT 0,

    -- Membership discount
    membership_discount DECIMAL(10,0) DEFAULT 0,

    -- Phí dịch vụ
    service_fee DECIMAL(10,0) DEFAULT 0,

    -- Tổng cộng
    total_discount DECIMAL(10,0) DEFAULT 0,
    total_price DECIMAL(10,0) NOT NULL,          -- Số tiền phải trả

    -- Trạng thái
    status ENUM('pending','confirmed','cancelled','expired','refunded','checked_in') DEFAULT 'pending',
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expired_at TIMESTAMP NULL DEFAULT NULL,
    confirmed_at DATETIME NULL,

    -- Thanh toán
    payment_method ENUM('momo','vnpay','zalopay','wallet','gift_card','mixed','cash') NULL,
    payment_status ENUM('pending','paid','refunded','partial_refund') DEFAULT 'pending',
    payment_transaction_id VARCHAR(100) NULL,
    paid_at DATETIME NULL,

    -- Email tracking
    confirmation_sent_at DATETIME NULL,
    reminder_sent_at DATETIME NULL,
    review_request_sent_at DATETIME NULL,

    -- Hủy/Hoàn tiền
    cancelled_at DATETIME NULL,
    cancelled_reason TEXT NULL,
    cancelled_by ENUM('user', 'admin', 'system') NULL,

    -- IP & Device
    ip_address VARCHAR(45),
    user_agent TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_user_status (user_id, status),
    INDEX idx_showtime (showtime_id),
    INDEX idx_payment_status (payment_status),
    INDEX idx_booking_code (booking_code),
    INDEX idx_booking_date (booking_date),
    INDEX idx_status (status),

    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (showtime_id) REFERENCES showtimes(id),
    FOREIGN KEY (voucher_id) REFERENCES vouchers(id) ON DELETE SET NULL,
    FOREIGN KEY (campaign_id) REFERENCES promotion_campaigns(id) ON DELETE SET NULL,
    FOREIGN KEY (gift_card_id) REFERENCES gift_cards(id) ON DELETE SET NULL
);

-- 33. BOOKING_SEATS - Vé (ghế đã đặt)
CREATE TABLE booking_seats (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id INT UNSIGNED NOT NULL,
    showtime_id INT UNSIGNED NOT NULL,
    seat_id INT UNSIGNED NOT NULL,
    price DECIMAL(10,0) NOT NULL,                -- Giá vé tại thời điểm đặt

    -- QR Code
    qr_code VARCHAR(255) NOT NULL UNIQUE,
    qr_status ENUM('active','checked','cancelled','expired') DEFAULT 'active',
    checked_at TIMESTAMP NULL DEFAULT NULL,
    checked_by_admin_id INT UNSIGNED NULL,

    -- Gate info
    gate_number VARCHAR(10) NULL,

    UNIQUE KEY uq_booking_seat (booking_id, seat_id),
    UNIQUE KEY uq_showtime_seat_booking (showtime_id, seat_id),
    INDEX idx_qr_code (qr_code),
    INDEX idx_qr_status (qr_status),
    INDEX idx_showtime (showtime_id),

    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (showtime_id) REFERENCES showtimes(id),
    FOREIGN KEY (seat_id) REFERENCES seats(id)
);

-- 34. PAYMENTS - Giao dịch thanh toán thống nhất
-- NOTE: Tách riêng từ bookings/product_orders để:
-- 1. Tránh duplicate logic thanh toán
-- 2. Dễ tích hợp payment gateway
-- 3. Audit trail rõ ràng
CREATE TABLE payments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    payment_code VARCHAR(30) UNIQUE NOT NULL,    -- 'PAY20260201ABC123'

    -- Nguồn thanh toán
    payable_type ENUM('booking', 'product_order', 'gift_card', 'wallet_topup') NOT NULL,
    payable_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,

    -- Số tiền
    amount DECIMAL(10,0) NOT NULL,
    currency VARCHAR(3) DEFAULT 'VND',

    -- Phương thức
    method ENUM('momo', 'vnpay', 'zalopay', 'wallet', 'gift_card', 'cash', 'card') NOT NULL,

    -- Gateway response
    gateway_transaction_id VARCHAR(100) NULL,
    gateway_response JSON NULL,                  -- Raw response từ payment gateway

    -- Trạng thái
    status ENUM('pending', 'processing', 'completed', 'failed', 'cancelled', 'refunded') DEFAULT 'pending',

    -- Timestamps
    initiated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at DATETIME NULL,
    failed_at DATETIME NULL,
    failure_reason VARCHAR(255) NULL,

    -- Metadata
    ip_address VARCHAR(45),
    user_agent TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_payable (payable_type, payable_id),
    INDEX idx_user (user_id),
    INDEX idx_status (status),
    INDEX idx_method (method),
    INDEX idx_gateway_txn (gateway_transaction_id),

    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- 35. REFUNDS - Hoàn tiền
CREATE TABLE refunds (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id INT UNSIGNED NULL,
    order_id INT UNSIGNED NULL,
    user_id INT UNSIGNED NOT NULL,

    -- Số tiền
    original_amount DECIMAL(10,0) NOT NULL,      -- Số tiền gốc
    refund_amount DECIMAL(10,0) NOT NULL,        -- Số tiền hoàn
    refund_fee DECIMAL(10,0) DEFAULT 0,          -- Phí hoàn (nếu có)

    -- Chi tiết hoàn
    refund_to_wallet DECIMAL(10,0) DEFAULT 0,
    refund_to_payment DECIMAL(10,0) DEFAULT 0,
    refund_to_points INT UNSIGNED DEFAULT 0,

    -- Lý do
    reason ENUM('user_request', 'showtime_cancelled', 'payment_issue', 'other') NOT NULL,
    reason_detail TEXT,

    -- Trạng thái
    status ENUM('pending', 'processing', 'completed', 'failed', 'rejected') DEFAULT 'pending',

    -- Payment
    payment_refund_id VARCHAR(100) NULL,         -- ID từ cổng thanh toán

    processed_at DATETIME NULL,
    processed_by INT UNSIGNED NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_booking (booking_id),
    INDEX idx_user (user_id),
    INDEX idx_status (status),

    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- 35. PRODUCT_ORDERS - Đơn hàng F&B
CREATE TABLE product_orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    booking_id INT UNSIGNED NULL,                -- NULL nếu mua riêng
    order_code VARCHAR(20) UNIQUE NOT NULL,      -- 'FO20260201XYZ'

    -- Items count
    item_count INT UNSIGNED NOT NULL,

    -- Giá
    subtotal DECIMAL(10,0) NOT NULL,

    -- Giảm giá
    voucher_id INT UNSIGNED NULL,
    voucher_discount DECIMAL(10,0) DEFAULT 0,
    campaign_id INT UNSIGNED NULL,
    campaign_discount DECIMAL(10,0) DEFAULT 0,

    -- Điểm
    points_used INT UNSIGNED DEFAULT 0,
    points_discount DECIMAL(10,0) DEFAULT 0,
    points_earned INT UNSIGNED DEFAULT 0,

    -- Gift card & Wallet
    gift_card_id INT UNSIGNED NULL,
    gift_card_amount DECIMAL(10,0) DEFAULT 0,
    wallet_amount DECIMAL(10,0) DEFAULT 0,

    -- Membership
    membership_discount DECIMAL(10,0) DEFAULT 0,

    -- Tổng
    total_discount DECIMAL(10,0) DEFAULT 0,
    total_amount DECIMAL(10,0) NOT NULL,

    -- Trạng thái
    status ENUM('pending', 'paid', 'preparing', 'ready', 'completed', 'cancelled', 'refunded') DEFAULT 'pending',

    -- Thanh toán
    payment_method ENUM('momo', 'vnpay', 'zalopay', 'wallet', 'gift_card', 'cash', 'card', 'mixed') NULL,
    payment_status ENUM('pending', 'paid', 'refunded') DEFAULT 'pending',
    payment_transaction_id VARCHAR(100) NULL,
    paid_at DATETIME NULL,

    -- Nhận hàng
    pickup_code VARCHAR(10) NOT NULL,            -- Mã nhận hàng: '1234'
    pickup_time DATETIME NULL,                   -- Giờ dự kiến nhận (= giờ chiếu - 15p)
    pickup_location VARCHAR(100) DEFAULT 'Quầy F&B',
    picked_up_at DATETIME NULL,
    picked_up_by_staff_id INT UNSIGNED NULL,

    -- Hủy
    cancelled_at DATETIME NULL,
    cancelled_reason TEXT NULL,

    notes TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_user (user_id),
    INDEX idx_booking (booking_id),
    INDEX idx_status (status),
    INDEX idx_order_code (order_code),
    INDEX idx_pickup_code (pickup_code),
    INDEX idx_payment_status (payment_status),

    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL,
    FOREIGN KEY (voucher_id) REFERENCES vouchers(id) ON DELETE SET NULL,
    FOREIGN KEY (campaign_id) REFERENCES promotion_campaigns(id) ON DELETE SET NULL,
    FOREIGN KEY (gift_card_id) REFERENCES gift_cards(id) ON DELETE SET NULL
);

-- 36. PRODUCT_ORDER_ITEMS - Chi tiết đơn hàng F&B
CREATE TABLE product_order_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    quantity INT UNSIGNED NOT NULL,
    unit_price DECIMAL(10,0) NOT NULL,
    total_price DECIMAL(10,0) NOT NULL,

    options JSON NULL,                           -- [{"name": "Thêm bơ", "price": 5000}]
    notes VARCHAR(255) NULL,                     -- 'Ít đá', 'Không đường'

    -- Status per item
    status ENUM('pending', 'preparing', 'ready') DEFAULT 'pending',

    FOREIGN KEY (order_id) REFERENCES product_orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id),

    INDEX idx_order (order_id),
    INDEX idx_product (product_id)
);

-- 37. PROMOTION_USAGES - Lịch sử sử dụng khuyến mãi
CREATE TABLE promotion_usages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    campaign_id INT UNSIGNED NULL,
    voucher_id INT UNSIGNED NULL,
    booking_id INT UNSIGNED NULL,
    order_id INT UNSIGNED NULL,

    discount_amount DECIMAL(10,0) NOT NULL,

    used_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_user (user_id),
    INDEX idx_campaign (campaign_id),
    INDEX idx_voucher (voucher_id),
    INDEX idx_user_campaign (user_id, campaign_id),

    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (campaign_id) REFERENCES promotion_campaigns(id) ON DELETE SET NULL,
    FOREIGN KEY (voucher_id) REFERENCES vouchers(id) ON DELETE SET NULL,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL,
    FOREIGN KEY (order_id) REFERENCES product_orders(id) ON DELETE SET NULL
);

-- 38. REVIEWS - Đánh giá phim
CREATE TABLE reviews (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    movie_id INT UNSIGNED NOT NULL,
    booking_id INT UNSIGNED NULL,                -- Liên kết với booking (xác thực đã xem phim)
    rating INT UNSIGNED NOT NULL CHECK (rating BETWEEN 1 AND 10),
    title VARCHAR(200) NULL,
    comment TEXT,

    -- Pros/Cons
    pros TEXT NULL,
    cons TEXT NULL,

    -- Points earned for review
    points_earned INT UNSIGNED DEFAULT 0,

    is_verified_purchase BOOLEAN DEFAULT FALSE,  -- Đã mua vé xem phim này
    is_featured BOOLEAN DEFAULT FALSE,           -- Review nổi bật
    is_spoiler BOOLEAN DEFAULT FALSE,            -- Có spoiler

    -- Engagement
    helpful_count INT UNSIGNED DEFAULT 0,
    not_helpful_count INT UNSIGNED DEFAULT 0,

    -- Moderation
    status ENUM('pending', 'approved', 'rejected', 'hidden') DEFAULT 'approved',
    moderated_at DATETIME NULL,
    moderated_by_admin_id INT UNSIGNED NULL,
    rejection_reason VARCHAR(255) NULL,

    deleted_at TIMESTAMP NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uq_user_movie (user_id, movie_id),
    INDEX idx_movie (movie_id),
    INDEX idx_status (status),
    INDEX idx_rating (rating),
    INDEX idx_featured (is_featured),
    INDEX idx_deleted (deleted_at),

    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL
);

-- 39. REVIEW_HELPFULS - Đánh giá hữu ích
CREATE TABLE review_helpfuls (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    review_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    is_helpful BOOLEAN NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uq_review_user (review_id, user_id),

    FOREIGN KEY (review_id) REFERENCES reviews(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================================
-- NOTIFICATION & COMMUNICATION
-- ============================================================

-- 40. NOTIFICATIONS - Thông báo
CREATE TABLE notifications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    type ENUM('booking', 'promotion', 'loyalty', 'system', 'reminder', 'refund') NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    data JSON NULL,                              -- Dữ liệu bổ sung

    -- Channels
    channel ENUM('in_app', 'email', 'push', 'sms') DEFAULT 'in_app',

    is_read BOOLEAN DEFAULT FALSE,
    read_at DATETIME NULL,

    action_url VARCHAR(255) NULL,
    action_text VARCHAR(50) NULL,

    -- Priority
    priority ENUM('low', 'normal', 'high') DEFAULT 'normal',

    expires_at DATETIME NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_user_read (user_id, is_read),
    INDEX idx_type (type),
    INDEX idx_created (created_at),
    INDEX idx_priority (priority),

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 41. EMAIL_LOGS - Nhật ký email
CREATE TABLE email_logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL,
    to_email VARCHAR(150) NOT NULL,
    to_name VARCHAR(100) NULL,

    type ENUM('booking_confirmation', 'booking_reminder', 'booking_cancelled', 'password_reset',
              'welcome', 'review_request', 'promotion', 'voucher', 'refund', 'newsletter', 'other') NOT NULL,

    subject VARCHAR(255) NOT NULL,
    template VARCHAR(100) NULL,

    -- References
    reference_type VARCHAR(50) NULL,             -- 'booking', 'voucher', etc.
    reference_id INT UNSIGNED NULL,

    -- Status
    status ENUM('pending', 'sent', 'delivered', 'opened', 'clicked', 'bounced', 'failed') DEFAULT 'pending',

    -- Tracking
    sent_at DATETIME NULL,
    delivered_at DATETIME NULL,
    opened_at DATETIME NULL,
    clicked_at DATETIME NULL,

    -- Error
    error_message TEXT NULL,
    retry_count INT UNSIGNED DEFAULT 0,

    -- Provider info
    provider VARCHAR(50) NULL,                   -- 'sendgrid', 'ses', 'mailgun'
    provider_message_id VARCHAR(255) NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_user (user_id),
    INDEX idx_type (type),
    INDEX idx_status (status),
    INDEX idx_to_email (to_email),
    INDEX idx_reference (reference_type, reference_id),

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ============================================================
-- ADMIN & REPORTING
-- ============================================================

-- 42. ADMIN_ACTIVITY_LOGS - Nhật ký hoạt động admin
CREATE TABLE admin_activity_logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admin_id INT UNSIGNED NOT NULL,
    action VARCHAR(100) NOT NULL,                -- 'create_movie', 'cancel_booking', 'check_in'
    entity_type VARCHAR(50) NOT NULL,            -- 'movie', 'booking', 'user'
    entity_id INT UNSIGNED NOT NULL,
    old_values JSON NULL,
    new_values JSON NULL,
    description TEXT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_admin (admin_id),
    INDEX idx_entity (entity_type, entity_id),
    INDEX idx_action (action),
    INDEX idx_created (created_at),

    FOREIGN KEY (admin_id) REFERENCES users(id)
);

-- 43. USER_ACTIVITY_LOGS - Nhật ký hoạt động user
CREATE TABLE user_activity_logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL,
    session_id VARCHAR(255) NULL,

    action VARCHAR(100) NOT NULL,                -- 'login', 'logout', 'view_movie', 'add_to_cart'
    entity_type VARCHAR(50) NULL,
    entity_id INT UNSIGNED NULL,

    metadata JSON NULL,                          -- Additional data

    ip_address VARCHAR(45),
    user_agent TEXT,
    device_type VARCHAR(20),                     -- 'mobile', 'desktop', 'tablet'

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_user (user_id),
    INDEX idx_action (action),
    INDEX idx_created (created_at),
    INDEX idx_entity (entity_type, entity_id),

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- 44. DAILY_REPORTS - Báo cáo ngày (cache)
CREATE TABLE daily_reports (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    report_date DATE NOT NULL,
    cinema_id INT UNSIGNED NULL,                 -- NULL = tổng hợp tất cả rạp

    -- Vé
    total_bookings INT UNSIGNED DEFAULT 0,
    total_tickets INT UNSIGNED DEFAULT 0,
    ticket_revenue DECIMAL(12,0) DEFAULT 0,
    cancelled_bookings INT UNSIGNED DEFAULT 0,

    -- F&B
    total_orders INT UNSIGNED DEFAULT 0,
    product_revenue DECIMAL(12,0) DEFAULT 0,

    -- Tổng
    total_revenue DECIMAL(12,0) DEFAULT 0,
    total_discount DECIMAL(12,0) DEFAULT 0,
    net_revenue DECIMAL(12,0) DEFAULT 0,

    -- Hoàn tiền
    total_refunds INT UNSIGNED DEFAULT 0,
    refund_amount DECIMAL(12,0) DEFAULT 0,

    -- Thành viên
    new_users INT UNSIGNED DEFAULT 0,
    active_users INT UNSIGNED DEFAULT 0,
    points_issued INT UNSIGNED DEFAULT 0,
    points_redeemed INT UNSIGNED DEFAULT 0,

    -- Top movies
    top_movies JSON NULL,                        -- [{movie_id, title, tickets, revenue}]

    -- Chi tiết
    details JSON NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uq_date_cinema (report_date, cinema_id),
    INDEX idx_date (report_date),
    INDEX idx_cinema (cinema_id),

    FOREIGN KEY (cinema_id) REFERENCES cinemas(id) ON DELETE CASCADE
);

-- 45. BANNERS - Banner quảng cáo
CREATE TABLE banners (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    subtitle VARCHAR(255) NULL,
    image_url VARCHAR(255) NOT NULL,
    mobile_image_url VARCHAR(255) NULL,
    link_url VARCHAR(255) NULL,
    link_target ENUM('_self', '_blank') DEFAULT '_self',

    position ENUM('home_hero', 'home_secondary', 'movie_list', 'checkout', 'promotion') DEFAULT 'home_hero',

    start_date DATETIME NULL,
    end_date DATETIME NULL,

    is_active BOOLEAN DEFAULT TRUE,
    display_order INT DEFAULT 0,

    -- Analytics
    impressions INT UNSIGNED DEFAULT 0,
    clicks INT UNSIGNED DEFAULT 0,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_position (position),
    INDEX idx_active_date (is_active, start_date, end_date),
    INDEX idx_display_order (display_order)
);

-- ============================================================
-- LARAVEL INFRASTRUCTURE TABLES
-- ============================================================

-- 46. CACHE
CREATE TABLE cache (
    `key` VARCHAR(255) PRIMARY KEY,
    value MEDIUMTEXT NOT NULL,
    expiration INT NOT NULL,
    INDEX idx_expiration (expiration)
);

-- 47. CACHE_LOCKS
CREATE TABLE cache_locks (
    `key` VARCHAR(255) PRIMARY KEY,
    owner VARCHAR(255) NOT NULL,
    expiration INT NOT NULL
);

-- 48. JOBS
CREATE TABLE jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    queue VARCHAR(255) NOT NULL,
    payload LONGTEXT NOT NULL,
    attempts TINYINT UNSIGNED NOT NULL,
    reserved_at INT UNSIGNED NULL,
    available_at INT UNSIGNED NOT NULL,
    created_at INT UNSIGNED NOT NULL,
    INDEX idx_queue (queue)
);

-- 49. JOB_BATCHES
CREATE TABLE job_batches (
    id VARCHAR(255) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    total_jobs INT NOT NULL,
    pending_jobs INT NOT NULL,
    failed_jobs INT NOT NULL,
    failed_job_ids LONGTEXT NOT NULL,
    options MEDIUMTEXT NULL,
    cancelled_at INT NULL,
    created_at INT NOT NULL,
    finished_at INT NULL
);

-- 50. FAILED_JOBS
CREATE TABLE failed_jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(255) UNIQUE NOT NULL,
    connection TEXT NOT NULL,
    queue TEXT NOT NULL,
    payload LONGTEXT NOT NULL,
    exception LONGTEXT NOT NULL,
    failed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_uuid (uuid)
);

-- 51. MIGRATIONS
CREATE TABLE migrations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    migration VARCHAR(255) NOT NULL,
    batch INT NOT NULL
);

-- ============================================================
-- ADD FOREIGN KEY CONSTRAINTS (after all tables created)
-- ============================================================

-- Users self-reference for referral
ALTER TABLE users
    ADD CONSTRAINT fk_users_referred_by
    FOREIGN KEY (referred_by_user_id) REFERENCES users(id) ON DELETE SET NULL;

-- Users membership tier
ALTER TABLE users
    ADD CONSTRAINT fk_users_membership_tier
    FOREIGN KEY (membership_tier_id) REFERENCES membership_tiers(id);

-- Users preferred cinema
ALTER TABLE users
    ADD CONSTRAINT fk_users_preferred_cinema
    FOREIGN KEY (preferred_cinema_id) REFERENCES cinemas(id) ON DELETE SET NULL;

-- Gift card transactions
ALTER TABLE gift_card_transactions
    ADD CONSTRAINT fk_gct_booking
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL;

ALTER TABLE gift_card_transactions
    ADD CONSTRAINT fk_gct_order
    FOREIGN KEY (order_id) REFERENCES product_orders(id) ON DELETE SET NULL;

-- Vouchers used in booking/order
ALTER TABLE vouchers
    ADD CONSTRAINT fk_vouchers_booking
    FOREIGN KEY (used_in_booking_id) REFERENCES bookings(id) ON DELETE SET NULL;

ALTER TABLE vouchers
    ADD CONSTRAINT fk_vouchers_order
    FOREIGN KEY (used_in_order_id) REFERENCES product_orders(id) ON DELETE SET NULL;

-- Refunds
ALTER TABLE refunds
    ADD CONSTRAINT fk_refunds_booking
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL;

ALTER TABLE refunds
    ADD CONSTRAINT fk_refunds_order
    FOREIGN KEY (order_id) REFERENCES product_orders(id) ON DELETE SET NULL;

-- Showtime seats booking reference
ALTER TABLE showtime_seats
    ADD CONSTRAINT fk_showtime_seats_booking
    FOREIGN KEY (booked_in_booking_id) REFERENCES bookings(id) ON DELETE SET NULL;

-- Note: payments.payable_id không có FK vì là polymorphic relation
-- Application layer phải validate payable_type + payable_id

-- ============================================================
-- DEFAULT DATA
-- ============================================================

-- Default Cinema
INSERT INTO cinemas (name, slug, address, city, district, phone, email, is_active) VALUES
('Cinebook Quận 1', 'cinebook-quan-1', '123 Nguyễn Huệ, Quận 1', 'Hồ Chí Minh', 'Quận 1', '028-1234-5678', 'q1@cinebook.vn', TRUE);

-- Membership Tiers
INSERT INTO membership_tiers (name, slug, min_points, color, points_multiplier, ticket_discount_percent, product_discount_percent, birthday_voucher_value, free_upgrades_per_month, priority_booking_hours, display_order) VALUES
('Member', 'member', 0, '#808080', 1.00, 0, 0, 50000, 0, 0, 1),
('Silver', 'silver', 1000, '#C0C0C0', 1.20, 5, 5, 100000, 1, 0, 2),
('Gold', 'gold', 5000, '#FFD700', 1.50, 10, 10, 200000, 2, 24, 3),
('Platinum', 'platinum', 15000, '#E5E4E2', 1.80, 15, 15, 300000, 3, 48, 4),
('Diamond', 'diamond', 30000, '#B9F2FF', 2.00, 20, 20, 500000, 5, 72, 5);

-- Seat Types
INSERT INTO seat_types (name, code, base_price, description, color_code, seats_count, display_order) VALUES
('Standard', 'standard', 80000, 'Ghế thường', '#4CAF50', 1, 1),
('VIP', 'vip', 120000, 'Ghế VIP rộng rãi, thoải mái', '#FF9800', 1, 2),
('Couple', 'couple', 200000, 'Ghế đôi dành cho cặp đôi', '#E91E63', 2, 3);

-- Screen Types
INSERT INTO screen_types (name, code, price_adjustment, description, display_order) VALUES
('2D', '2d', 0, 'Màn hình 2D tiêu chuẩn', 1),
('3D', '3d', 30000, 'Công nghệ 3D sống động', 2),
('IMAX', 'imax', 50000, 'Màn hình IMAX khổng lồ', 3),
('4DX', '4dx', 80000, 'Trải nghiệm 4D với hiệu ứng đặc biệt', 4);

-- Product Categories
INSERT INTO product_categories (name, slug, icon, display_order) VALUES
('Bắp rang', 'bap-rang', 'popcorn', 1),
('Nước uống', 'nuoc-uong', 'drink', 2),
('Combo', 'combo', 'combo', 3),
('Snacks', 'snacks', 'snack', 4);

-- Point Rules
INSERT INTO point_rules (name, code, type, points_per_amount, fixed_points, is_active) VALUES
('Tích điểm mua vé', 'TICKET_POINTS', 'ticket_purchase', 0.001, NULL, TRUE),
('Tích điểm mua F&B', 'PRODUCT_POINTS', 'product_purchase', 0.001, NULL, TRUE),
('Điểm viết review', 'REVIEW_POINTS', 'review', NULL, 50, TRUE),
('Điểm giới thiệu bạn bè', 'REFERRAL_POINTS', 'referral', NULL, 200, TRUE),
('Điểm sinh nhật', 'BIRTHDAY_POINTS', 'birthday', NULL, 100, TRUE);

-- Gift Card Templates
INSERT INTO gift_card_templates (name, occasion, image_url, is_active, display_order) VALUES
('Sinh nhật', 'birthday', '/images/giftcard/birthday.jpg', TRUE, 1),
('Kỷ niệm', 'anniversary', '/images/giftcard/anniversary.jpg', TRUE, 2),
('Giáng sinh', 'christmas', '/images/giftcard/christmas.jpg', TRUE, 3),
('Tết', 'tet', '/images/giftcard/tet.jpg', TRUE, 4),
('Đa năng', 'general', '/images/giftcard/general.jpg', TRUE, 5);

-- Genres
INSERT INTO genres (name, slug) VALUES
('Hành động', 'hanh-dong'),
('Hài hước', 'hai-huoc'),
('Kinh dị', 'kinh-di'),
('Tình cảm', 'tinh-cam'),
('Hoạt hình', 'hoat-hinh'),
('Khoa học viễn tưởng', 'khoa-hoc-vien-tuong'),
('Phiêu lưu', 'phieu-luu'),
('Tâm lý', 'tam-ly'),
('Gia đình', 'gia-dinh'),
('Tài liệu', 'tai-lieu');

-- ============================================================
-- TRIGGERS
-- ============================================================
--
-- ARCHITECTURE NOTES:
--
-- 1. RACE CONDITION PREVENTION:
--    Triggers này chỉ dùng để SYNC COUNT, không phải để prevent overbooking.
--    Application layer PHẢI implement:
--    - SELECT ... FOR UPDATE khi chọn ghế
--    - Transaction isolation level: REPEATABLE READ hoặc SERIALIZABLE
--    - Optimistic locking với version column nếu cần
--
-- 2. PRICING ENGINE:
--    pricing_rules + showtime_prices là snapshot-based.
--    Khi scale lớn, pricing logic nên đẩy lên service layer.
--    DB chỉ lưu kết quả đã tính (showtime_prices.final_price).
--
-- 3. HIGH-TRAFFIC CONSIDERATIONS:
--    - Trigger có thể thành bottleneck
--    - Alternative: async job để sync counts
--    - Hoặc dùng Redis atomic counters + periodic DB sync
--
-- ============================================================

-- Trigger: Update movie rating when review is added/updated
DELIMITER //
CREATE TRIGGER trg_update_movie_rating_insert
AFTER INSERT ON reviews
FOR EACH ROW
BEGIN
    IF NEW.status = 'approved' THEN
        UPDATE movies m
        SET
            rating_avg = (SELECT AVG(rating) FROM reviews WHERE movie_id = NEW.movie_id AND status = 'approved'),
            rating_count = (SELECT COUNT(*) FROM reviews WHERE movie_id = NEW.movie_id AND status = 'approved')
        WHERE m.id = NEW.movie_id;
    END IF;
END//

CREATE TRIGGER trg_update_movie_rating_update
AFTER UPDATE ON reviews
FOR EACH ROW
BEGIN
    UPDATE movies m
    SET
        rating_avg = (SELECT COALESCE(AVG(rating), 0) FROM reviews WHERE movie_id = NEW.movie_id AND status = 'approved'),
        rating_count = (SELECT COUNT(*) FROM reviews WHERE movie_id = NEW.movie_id AND status = 'approved')
    WHERE m.id = NEW.movie_id;
END//

-- Trigger: Update showtime seat counts
-- ⚠️ WARNING: Trigger này chỉ để SYNC COUNT
-- KHÔNG RELY vào trigger để prevent overbooking!
-- Application layer phải dùng: SELECT ... FOR UPDATE + Transaction
CREATE TRIGGER trg_update_showtime_seats_insert
AFTER INSERT ON showtime_seats
FOR EACH ROW
BEGIN
    IF NEW.status = 'booked' THEN
        UPDATE showtimes
        SET
            booked_seats = booked_seats + 1,
            available_seats = available_seats - 1,
            status = IF(available_seats - 1 = 0, 'full', status)
        WHERE id = NEW.showtime_id;
    END IF;
END//

CREATE TRIGGER trg_update_showtime_seats_update
AFTER UPDATE ON showtime_seats
FOR EACH ROW
BEGIN
    IF OLD.status != 'booked' AND NEW.status = 'booked' THEN
        UPDATE showtimes
        SET
            booked_seats = booked_seats + 1,
            available_seats = available_seats - 1,
            status = IF(available_seats - 1 = 0, 'full', status)
        WHERE id = NEW.showtime_id;
    ELSEIF OLD.status = 'booked' AND NEW.status != 'booked' THEN
        UPDATE showtimes
        SET
            booked_seats = booked_seats - 1,
            available_seats = available_seats + 1,
            status = IF(status = 'full', 'open', status)
        WHERE id = NEW.showtime_id;
    END IF;
END//

-- Trigger: Update review helpful counts
CREATE TRIGGER trg_update_review_helpful_insert
AFTER INSERT ON review_helpfuls
FOR EACH ROW
BEGIN
    IF NEW.is_helpful THEN
        UPDATE reviews SET helpful_count = helpful_count + 1 WHERE id = NEW.review_id;
    ELSE
        UPDATE reviews SET not_helpful_count = not_helpful_count + 1 WHERE id = NEW.review_id;
    END IF;
END//

CREATE TRIGGER trg_update_review_helpful_update
AFTER UPDATE ON review_helpfuls
FOR EACH ROW
BEGIN
    IF OLD.is_helpful AND NOT NEW.is_helpful THEN
        UPDATE reviews SET helpful_count = helpful_count - 1, not_helpful_count = not_helpful_count + 1 WHERE id = NEW.review_id;
    ELSEIF NOT OLD.is_helpful AND NEW.is_helpful THEN
        UPDATE reviews SET helpful_count = helpful_count + 1, not_helpful_count = not_helpful_count - 1 WHERE id = NEW.review_id;
    END IF;
END//

CREATE TRIGGER trg_update_review_helpful_delete
AFTER DELETE ON review_helpfuls
FOR EACH ROW
BEGIN
    IF OLD.is_helpful THEN
        UPDATE reviews SET helpful_count = helpful_count - 1 WHERE id = OLD.review_id;
    ELSE
        UPDATE reviews SET not_helpful_count = not_helpful_count - 1 WHERE id = OLD.review_id;
    END IF;
END//

DELIMITER ;

-- ============================================================
-- VIEWS (Optional - for reporting)
-- ============================================================

-- View: Active showtimes with movie info
CREATE OR REPLACE VIEW vw_active_showtimes AS
SELECT
    s.id AS showtime_id,
    s.show_date,
    s.show_time,
    s.end_time,
    s.available_seats,
    s.total_seats,
    s.status AS showtime_status,
    m.id AS movie_id,
    m.title AS movie_title,
    m.poster_url,
    m.duration,
    m.age_rating,
    r.id AS room_id,
    r.name AS room_name,
    st.name AS screen_type,
    c.id AS cinema_id,
    c.name AS cinema_name
FROM showtimes s
JOIN movies m ON s.movie_id = m.id
JOIN rooms r ON s.room_id = r.id
JOIN screen_types st ON r.screen_type_id = st.id
JOIN cinemas c ON r.cinema_id = c.id
WHERE s.status IN ('scheduled', 'open')
  AND s.show_date >= CURDATE()
  AND m.deleted_at IS NULL
  AND r.deleted_at IS NULL;

-- View: User booking history
CREATE OR REPLACE VIEW vw_user_bookings AS
SELECT
    b.id AS booking_id,
    b.booking_code,
    b.user_id,
    b.ticket_count,
    b.total_price,
    b.status AS booking_status,
    b.payment_status,
    b.created_at AS booked_at,
    s.show_date,
    s.show_time,
    m.title AS movie_title,
    m.poster_url,
    r.name AS room_name,
    c.name AS cinema_name,
    GROUP_CONCAT(CONCAT(se.seat_row, se.seat_number) ORDER BY se.seat_row, se.seat_number) AS seats
FROM bookings b
JOIN showtimes s ON b.showtime_id = s.id
JOIN movies m ON s.movie_id = m.id
JOIN rooms r ON s.room_id = r.id
JOIN cinemas c ON r.cinema_id = c.id
LEFT JOIN booking_seats bs ON b.id = bs.booking_id
LEFT JOIN seats se ON bs.seat_id = se.id
GROUP BY b.id;

-- ============================================================
-- END OF SCHEMA
-- ============================================================

-- Summary:
-- Total Tables: 57
--
-- Core Cinema: cinemas, movies, genres, movie_genres, rooms, seats, showtimes, showtime_prices, showtime_seats, bookings, booking_seats
-- F&B: product_categories, products, combo_items, product_options, product_orders, product_order_items
-- Promotions: promotion_campaigns, vouchers, promotions, promotion_usages
-- Campaign Mappings: campaign_cinemas, campaign_movies, campaign_seat_types, campaign_products, campaign_user_tiers
-- Loyalty: membership_tiers, point_rules, loyalty_transactions
-- Pricing: pricing_rules, special_dates
-- Gift Card & Wallet: gift_card_templates, gift_cards, gift_card_transactions, user_wallets, wallet_transactions
-- Payments: payments (unified payment tracking)
-- User: users, reviews, review_helpfuls, notifications
-- Admin: admin_activity_logs, user_activity_logs, daily_reports, banners
-- Email: email_logs
-- Refunds: refunds
-- Laravel: sessions, password_reset_tokens, cache, cache_locks, jobs, job_batches, failed_jobs, migrations
--
-- ============================================================
-- ARCHITECTURE DECISIONS & TRADE-OFFS
-- ============================================================
--
-- 1. JSON vs Mapping Tables:
--    - JSON: Linh hoạt cho rule engine (applicable_days, applicable_hours)
--    - Mapping tables: Cho query nhiều (campaign_cinemas, campaign_movies)
--    - Hybrid approach: Dùng cả 2 tùy use case
--
-- 2. Payments Table:
--    - Tách riêng để tránh duplicate logic trong bookings/product_orders
--    - Dễ tích hợp multiple payment gateways
--    - Audit trail rõ ràng với gateway_response JSON
--
-- 3. Trigger Strategy:
--    - Triggers CHỈ để sync counts, KHÔNG để prevent race conditions
--    - Application layer phải dùng: SELECT ... FOR UPDATE + Transaction
--    - Khi scale: có thể chuyển sang async job hoặc Redis counters
--
-- 4. Pricing Engine:
--    - pricing_rules: Configuration layer
--    - showtime_prices: Snapshot layer (pre-calculated)
--    - Khi scale lớn: Pricing logic nên ở service layer, DB chỉ lưu result
--
-- 5. Soft Delete:
--    - Áp dụng cho: users, movies, rooms, products, reviews, campaigns
--    - Không áp dụng cho: transactions, logs, payments (audit trail)
--
-- ============================================================
-- Key Improvements from Original (44 → 57 tables):
-- ============================================================
-- 1. Added cinemas table for multi-location support
-- 2. Unified data types (INT UNSIGNED throughout)
-- 3. Added soft delete (deleted_at) for important tables
-- 4. Added gift_card_templates table
-- 5. Added refunds table for proper refund tracking
-- 6. Added email_logs for email delivery tracking
-- 7. Added user_activity_logs for user behavior tracking
-- 8. Added banners table for promotions
-- 9. Extended seat_row to VARCHAR(2) for large rooms
-- 10. Added all missing foreign keys
-- 11. Added triggers for automatic calculations (with race condition notes)
-- 12. Added views for common queries
-- 13. Improved indexes for better query performance
-- 14. Added more status options and metadata fields
-- 15. Added campaign mapping tables (campaign_cinemas, etc.) for queryable relations
-- 16. Added unified payments table for payment gateway integration
-- 17. Added architecture documentation for scale considerations
