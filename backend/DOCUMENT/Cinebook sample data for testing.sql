-- ============================================================
-- CINEBOOK DATABASE - SAMPLE DATA
-- Correct order to avoid Foreign Key errors
-- ============================================================

-- ==================== 1. SCREEN TYPES ====================
INSERT INTO screen_types (id, name, price) VALUES
(1, '2D', 0),
(2, '3D', 40000),
(3, 'IMAX 4D', 60000);

-- ==================== 2. SEAT TYPES ====================
-- base_price includes seat price
INSERT INTO seat_types (id, name, base_price, description) VALUES
(1, 'standard', 80000, 'Standard seat'),
(2, 'vip', 120000, 'VIP seat - best position'),
(3, 'couple', 120000, 'Couple seat for two');

-- ==================== 3. ROOMS ====================
-- Depends on: screen_types
INSERT INTO rooms (id, name, total_rows, seats_per_row, screen_type_id) VALUES
(1, 'Room 1', 8, 18, 1), -- 2D
(2, 'Room 2', 8, 18, 1), -- 2D
(3, 'Room 3', 8, 18, 2), -- 3D
(4, 'Room 4', 8, 18, 3); -- IMAX 4D

-- ==================== 4. GENRES ====================
INSERT INTO genres (id, name) VALUES
(1, 'Action'),
(2, 'Adventure'),
(3, 'Animation'),
(4, 'Comedy'),
(5, 'Crime'),
(6, 'Drama'),
(7, 'Family'),
(8, 'Fantasy'),
(9, 'Horror'),
(10, 'Romance'),
(11, 'Sci-Fi'),
(12, 'Thriller'),
(13, 'War'),
(14, 'Mystery');

-- ==================== 5. MOVIES ====================
INSERT INTO movies (id, title, language, director, cast, duration, release_date, age_rating, status, poster_url, trailer_url, description, rating_avg) VALUES

-- ================= NOW SHOWING (15 movies) =================
(1, 'Avengers: Endgame', 'English', 'Anthony Russo, Joe Russo', 'Robert Downey Jr., Chris Evans, Chris Hemsworth, Scarlett Johansson, Mark Ruffalo', 181, '2025-12-20', 'T13', 'now_showing', 'https://i.postimg.cc/NGPSNkJj/Avengers-Endgame.jpg', 'https://www.youtube.com/watch?v=TcMBFSGVi1c', 'After the devastating events caused by Thanos, the universe is left in ruins. With half of all life erased, the remaining Avengers struggle to cope with loss, guilt, and failure. When a new opportunity emerges to reverse the catastrophe, the team reunites for one final mission. This emotional and action-packed conclusion delivers epic battles, heartfelt sacrifices, and a powerful farewell to beloved heroes, serving as the culmination of over a decade of storytelling in the Marvel Cinematic Universe.', 4.8),

(2, 'John Wick: Chapter 4', 'English', 'Chad Stahelski', 'Keanu Reeves, Donnie Yen, Bill Skarsgård, Laurence Fishburne, Ian McShane', 169, '2026-01-05', 'T18', 'now_showing', 'https://i.postimg.cc/zvJdvVGY/John_Wick_Chapter_4.jpg', 'https://www.youtube.com/watch?v=qEVUtrk8_B4', 'John Wick uncovers a path to defeating the High Table, but before earning his freedom, he must confront a powerful new enemy with alliances spanning the globe. From New York to Paris, the assassin faces relentless battles that push his skills and endurance to the limit. With breathtaking choreography, intense gun-fu action, and deeper exploration of John''s code and motivations, this chapter elevates the franchise to its most ambitious level yet.', 4.7),

(3, 'Parasite', 'Korean', 'Bong Joon-ho', 'Song Kang-ho, Lee Sun-kyun, Cho Yeo-jeong, Choi Woo-shik, Park So-dam', 132, '2025-12-10', 'T16', 'now_showing', 'https://i.postimg.cc/N0fZJCc9/Parasite.jpg', 'https://www.youtube.com/watch?v=5xH0HfJHsaY', 'Greed and class discrimination threaten the symbiotic relationship between the wealthy Park family and the struggling Kim family. As deception deepens, tensions escalate into shocking consequences. Blending dark comedy, suspense, and social commentary, this Oscar-winning film offers a sharp and unsettling reflection on inequality in modern society.', 4.9),

(4, 'Train to Busan', 'Korean', 'Yeon Sang-ho', 'Gong Yoo, Ma Dong-seok, Jung Yu-mi, Kim Su-an, Choi Woo-shik', 118, '2025-12-18', 'T18', 'now_showing', 'https://i.postimg.cc/65JgJpXY/Train_to_Busan.jpg', 'https://www.youtube.com/watch?v=pyWuHv2-Abk', 'As a zombie outbreak spreads rapidly across South Korea, passengers trapped on a high-speed train to Busan must fight for survival. Amid the chaos, personal sacrifices and human compassion emerge. The film combines intense action, emotional storytelling, and social critique, redefining the zombie genre with heart and urgency.', 4.6),

(5, 'The Dark Knight', 'English', 'Christopher Nolan', 'Christian Bale, Heath Ledger, Aaron Eckhart, Gary Oldman, Michael Caine', 152, '2025-12-25', 'T16', 'now_showing', 'https://i.postimg.cc/d0HpzRs7/The_Dark_Knight.jpg', 'https://www.youtube.com/watch?v=EXeTwQWrcwY', 'Batman faces his greatest psychological and moral challenge when the Joker emerges, spreading chaos throughout Gotham City. As lines between heroism and vigilantism blur, the Dark Knight must confront the true cost of justice. Featuring an iconic performance by Heath Ledger, this film is a profound exploration of order, chaos, and sacrifice.', 4.9),

(6, 'Avatar', 'English', 'James Cameron', 'Sam Worthington, Zoe Saldana, Sigourney Weaver, Stephen Lang, Michelle Rodriguez', 162, '2025-12-15', 'T13', 'now_showing', 'https://i.postimg.cc/kD3y0GRB/Avatar.jpg', 'https://www.youtube.com/watch?v=5PSNL1qE6VY', 'Jake Sully, a paraplegic former Marine, is sent to the distant moon Pandora as part of the Avatar Program. As he becomes immersed in the Na''vi culture, he finds himself torn between his mission and protecting a world he grows to love. With groundbreaking visuals and an immersive alien ecosystem, Avatar delivers a powerful message about colonialism, environmentalism, and identity.', 4.6),

(7, 'La La Land', 'English', 'Damien Chazelle', 'Ryan Gosling, Emma Stone, John Legend, Rosemarie DeWitt, J.K. Simmons', 128, '2026-01-10', 'T13', 'now_showing', 'https://i.postimg.cc/GmRWYhgB/La_La_Land.jpg', 'https://www.youtube.com/watch?v=0pdqf4P9MB8', 'Set in modern-day Los Angeles, this musical romance follows a jazz pianist and an aspiring actress as they fall in love while pursuing their dreams. As ambition and reality collide, the film explores the sacrifices required to achieve success and the bittersweet nature of love.', 4.5),

(8, 'Your Name', 'Japanese', 'Makoto Shinkai', 'Ryunosuke Kamiki, Mone Kamishiraishi, Masami Nagasawa, Etsuko Ichihara, Ryo Narita', 112, '2025-12-22', 'T13', 'now_showing', 'https://i.postimg.cc/NfqSqjc7/Your_Name.jpg', 'https://www.youtube.com/watch?v=xU47nhruN-Q', 'Two teenagers living in different parts of Japan mysteriously begin swapping bodies. As they struggle to understand this strange connection, a deeper bond forms across time and space. Visually stunning and emotionally resonant, the film blends romance, fantasy, and destiny.', 4.7),

(9, 'Spirited Away', 'Japanese', 'Hayao Miyazaki', 'Rumi Hiiragi, Miyu Irino, Mari Natsuki, Takashi Naito, Yasuko Sawaguchi', 125, '2025-12-28', 'P', 'now_showing', 'https://i.postimg.cc/hGPNyZBq/Spirited_Away.jpg', 'https://www.youtube.com/watch?v=ByXuk9QqQkk', 'Chihiro wanders into a mysterious spirit world ruled by gods, witches, and strange creatures. Forced to work in a bathhouse to save her parents, she embarks on a journey of courage and self-discovery. A timeless animated masterpiece filled with imagination and emotional depth.', 4.8),

(10, 'Intouchables', 'French', 'Olivier Nakache, Éric Toledano', 'François Cluzet, Omar Sy, Anne Le Ny, Audrey Fleurot, Clotilde Mollet', 112, '2026-01-12', 'P', 'now_showing', 'https://i.postimg.cc/jSTpNd3S/Intouchables.jpg', 'https://www.youtube.com/watch?v=34WIbmXkewU', 'Based on a true story, this heartwarming comedy follows the unlikely friendship between a wealthy quadriplegic and his caregiver from a disadvantaged background. Through humor and honesty, the film explores dignity, empathy, and the joy of human connection.', 4.6),

(11, 'Toy Story', 'English', 'John Lasseter', 'Tom Hanks, Tim Allen, Don Rickles, Jim Varney, Annie Potts', 81, '2025-12-20', 'P', 'now_showing', 'https://i.postimg.cc/1RN2jNLg/Toy_Story.jpg', 'https://www.youtube.com/watch?v=KYz2wyBy3kc', 'When a new toy named Buzz Lightyear arrives, Woody feels threatened and jealous. As rivalry turns into friendship, the toys learn about loyalty and teamwork. Toy Story marked the beginning of a new era in animation with its charm, humor, and heartfelt storytelling.', 4.6),

(12, 'The Conjuring', 'English', 'James Wan', 'Vera Farmiga, Patrick Wilson, Lili Taylor, Ron Livingston, Shanley Caswell', 112, '2026-01-08', 'T18', 'now_showing', 'https://i.postimg.cc/25cMPdk8/The_Conjuring.jpg', 'https://www.youtube.com/watch?v=k10ETZ41q5o', 'Paranormal investigators Ed and Lorraine Warren assist a family terrorized by a dark presence in their farmhouse. Drawing from real-life case files, the film delivers relentless suspense, atmospheric dread, and masterful horror storytelling.', 4.5),

(13, 'Furie', 'Vietnamese', 'Le Van Kiet', 'Ngo Thanh Van, Phan Thanh Nhien, Pham Anh Khoa, Mai Cat Vi, Nguyen Thanh Hoa', 98, '2025-12-18', 'T18', 'now_showing', 'https://i.postimg.cc/Ghv0HsQZ/Furie.jpg', 'https://www.youtube.com/watch?v=XiS8wL8jz3k', 'A former gangster living a quiet life is forced back into the criminal underworld when her daughter is kidnapped. Featuring intense hand-to-hand combat and emotional stakes, Furie showcases Vietnamese action cinema on an international level.', 4.4),

(14, 'Forrest Gump', 'English', 'Robert Zemeckis', 'Tom Hanks, Robin Wright, Gary Sinise, Sally Field, Mykelti Williamson', 142, '2025-12-05', 'P', 'now_showing', 'https://i.postimg.cc/Fs0t7JZB/Forrest_Gump.jpg', 'https://www.youtube.com/watch?v=bLvqoHBptjg', 'Through innocence and perseverance, Forrest Gump experiences extraordinary moments across several decades of American history. The film is a heartfelt exploration of destiny, love, and the simple truths that shape a meaningful life.', 4.7),



-- ================= COMING SOON (10 movies) =================
(16, 'Dune: Part Two', 'English', 'Denis Villeneuve', 'Timothée Chalamet, Zendaya, Rebecca Ferguson, Josh Brolin, Austin Butler', 165, '2026-02-15', 'T13', 'coming_soon', 'https://i.postimg.cc/KvtXRgNp/Dune.jpg', 'https://www.youtube.com/watch?v=n9xhJrPXop4', 'Paul Atreides embraces his destiny among the Fremen while leading a resistance against the forces that destroyed his family. As political intrigue, prophecy, and war collide, Paul must choose between love and the fate of the universe. The film expands the rich world-building of Arrakis with breathtaking visuals, complex characters, and epic scale, continuing one of the most ambitious science-fiction sagas of modern cinema.', 4.6),

(17, 'Oppenheimer', 'English', 'Christopher Nolan', 'Cillian Murphy, Emily Blunt, Robert Downey Jr., Matt Damon, Florence Pugh', 180, '2026-02-20', 'T16', 'coming_soon', 'https://i.postimg.cc/52mZmdDh/Oppenheimer.jpg', 'https://www.youtube.com/watch?v=uYPbbksJxIg', 'This biographical drama chronicles the life of J. Robert Oppenheimer, the brilliant physicist behind the Manhattan Project. As scientific triumph turns into moral conflict, the film explores ambition, responsibility, and the devastating consequences of innovation. Told through Nolan''s signature nonlinear storytelling, the film is both intellectually gripping and emotionally intense.', 4.7),

(18, 'Weathering With You', 'Japanese', 'Makoto Shinkai', 'Kotaro Daigo, Nana Mori, Tsubasa Honda, Shun Oguri, Sakura Ando', 114, '2026-02-25', 'T13', 'coming_soon', 'https://i.postimg.cc/15h2h3QD/Weathering_With_You.jpg', 'https://www.youtube.com/watch?v=Q6iK6DjV_iE', 'A runaway high school boy meets a mysterious girl who possesses the ability to manipulate weather. As their bond deepens, they must confront the consequences of altering nature itself. With stunning animation and heartfelt storytelling, the film blends romance, fantasy, and environmental themes into a deeply emotional experience.', 4.5),

(19, 'Finding Nemo', 'English', 'Andrew Stanton', 'Albert Brooks, Ellen DeGeneres, Alexander Gould, Willem Dafoe, Brad Garrett', 100, '2026-03-01', 'P', 'coming_soon', 'https://i.postimg.cc/Fs0t7JZP/Finding_Nemo.jpg', 'https://www.youtube.com/watch?v=SPHfeNgogVs', 'After his son Nemo is captured by a diver, an overprotective clownfish embarks on a perilous journey across the ocean. Along the way, he encounters unforgettable characters and learns the importance of trust and letting go. A heartwarming animated adventure filled with humor, emotion, and stunning underwater visuals.', 4.6),

(20, 'Decision to Leave', 'Korean', 'Park Chan-wook', 'Park Hae-il, Tang Wei, Lee Jung-hyun, Go Kyung-pyo, Park Yong-woo', 138, '2026-03-05', 'T16', 'coming_soon', 'https://i.postimg.cc/PfgsXMmM/Decision_to_Leave.jpg', 'https://www.youtube.com/watch?v=Z9FJxZ2kTfs', 'A meticulous detective investigating a suspicious death becomes emotionally entangled with the deceased man''s wife. As obsession grows, the boundary between duty and desire begins to blur. Stylishly directed with layered storytelling, the film is a haunting romantic thriller exploring guilt, longing, and moral ambiguity.', 4.4),

(21, 'Paddington', 'English', 'Paul King', 'Ben Whishaw, Hugh Bonneville, Sally Hawkins, Nicole Kidman, Julie Walters', 95, '2026-03-10', 'P', 'coming_soon', 'https://i.postimg.cc/JzQ9QVS9/Paddington.jpg', 'https://www.youtube.com/watch?v=7bZFr2IA0Bo', 'A polite and curious bear from Peru travels to London in search of a new home. Taken in by the Brown family, Paddington''s kindness and optimism bring warmth and laughter to everyone he meets. A charming family film celebrating acceptance, empathy, and the meaning of home.', 4.3),

(22, 'Rurouni Kenshin', 'Japanese', 'Keishi Otomo', 'Takeru Satoh, Emi Takei, Munetaka Aoki, Yosuke Eguchi, Koji Kikkawa', 134, '2026-03-15', 'T16', 'coming_soon', 'https://i.postimg.cc/Jh4S6YL1/Rurouni_Kenshin.jpg', 'https://www.youtube.com/watch?v=YFWDv6bC1h4', 'A former assassin vows never to kill again while protecting the innocent during Japan''s turbulent Meiji era. Haunted by his past, Kenshin Himura must confront old enemies and inner demons. The film delivers stylish sword fights combined with emotional depth and moral reflection.', 4.4),

(23, 'Blue Is the Warmest Color', 'French', 'Abdellatif Kechiche', 'Adèle Exarchopoulos, Léa Seydoux, Salim Kechiouche, Aurélien Recoing, Mona Walravens', 180, '2026-03-20', 'T18', 'coming_soon', 'https://i.postimg.cc/Kc6SG5nr/Blue_Is_the_Warmest_Colo.jpg', 'https://www.youtube.com/watch?v=EO0abB0jH9c', 'This intimate coming-of-age romance follows Adèle as she navigates love, identity, and heartbreak through her intense relationship with Emma. Told with raw honesty and emotional realism, the film explores desire, vulnerability, and the complexity of human connection.', 4.2),

(24, 'Intimate Strangers', 'Korean', 'Lee Jae-kyoo', 'Cho Jin-woong, Kim Ji-soo, Park Sung-woong, Lee Seo-jin, Yum Jung-ah', 115, '2026-03-25', 'T16', 'coming_soon', 'https://i.postimg.cc/pr20rpLb/Intimate_Strangers.jpg', 'https://www.youtube.com/watch?v=kbmG5C8F9W8', 'During a dinner gathering, seven friends decide to share every message and phone call they receive. What begins as a playful game soon reveals hidden secrets that challenge trust, relationships, and personal boundaries. A gripping drama that reflects modern intimacy and digital vulnerability.', 4.3),

(25, 'The Medium', 'Thai', 'Banjong Pisanthanakun', 'Narilya Gulmongkolpech, Sawanee Utoomma, Sirani Yankittikan, Yassaka Chaisorn, Boonsong Nakphoo', 130, '2026-04-01', 'T18', 'coming_soon', 'https://i.postimg.cc/NFpdHBKg/The_Medium.jpg', 'https://www.youtube.com/watch?v=wDtJ3M4arIc', 'A documentary crew follows a family of shamans in rural Thailand, uncovering terrifying supernatural possession rooted in ancient beliefs. Blending realism with escalating horror, the film delivers a deeply unsettling experience that explores faith, inheritance, and spiritual terror.', 4.1),

-- ================= ENDED (7 movies) =================
(26, 'Titanic', 'English', 'James Cameron', 'Leonardo DiCaprio, Kate Winslet, Billy Zane, Kathy Bates, Frances Fisher', 195, '1997-12-19', 'T13', 'ended', 'https://i.postimg.cc/d1gX0qTq/Titanic.jpg', 'https://www.youtube.com/watch?v=kVrqfYjkTdQ', 'A timeless epic romance set aboard the ill-fated RMS Titanic. As social class divides and human ambition collide, two young lovers fight to hold on to hope amid disaster. The film combines sweeping emotion, historical spectacle, and unforgettable performances, making it one of the most iconic films in cinematic history.', 4.8),

(27, 'Forrest Gump', 'English', 'Robert Zemeckis', 'Tom Hanks, Robin Wright, Gary Sinise, Mykelti Williamson, Sally Field', 142, '1994-07-06', 'P', 'ended', 'https://i.postimg.cc/Fs0t7JZB/Forrest_Gump.jpg', 'https://www.youtube.com/watch?v=bLvqoHBptjg', 'Forrest Gump, a kind-hearted man with limited intelligence, unwittingly influences several major historical events in the United States. Through love, loss, and perseverance, the film celebrates innocence, resilience, and the unpredictable journey of life.', 4.7),

(28, 'The Shawshank Redemption', 'English', 'Frank Darabont', 'Tim Robbins, Morgan Freeman, Bob Gunton, William Sadler, Clancy Brown', 142, '1994-09-23', 'T13', 'ended', 'https://i.postimg.cc/ZRXgqTyk/The_Shawshank_Redemption.jpg', 'https://www.youtube.com/watch?v=NmzuHjWmXOc', 'Two imprisoned men bond over decades, finding solace and redemption through acts of decency and hope. This powerful drama explores friendship, freedom, and the human spirit, earning its reputation as one of the greatest films ever made.', 4.9),

(29, 'The Wailing', 'Korean', 'Na Hong-jin', 'Kwak Do-won, Hwang Jung-min, Chun Woo-hee, Kunimura Jun, Kim Hwan-hee', 156, '2016-05-12', 'T18', 'ended', 'https://i.postimg.cc/76jcLxTk/The_Wailing.jpg', 'https://www.youtube.com/watch?v=43uAputjI4k', 'A mysterious illness spreads through a remote village following the arrival of a stranger. As paranoia and supernatural terror escalate, a police officer struggles to uncover the horrifying truth. The film masterfully blends folklore, horror, and psychological tension.', 4.5),

(30, 'Call Me by Your Name', 'English', 'Luca Guadagnino', 'Timothée Chalamet, Armie Hammer, Michael Stuhlbarg, Amira Casar, Esther Garrel', 132, '2017-11-24', 'T16', 'ended', 'https://i.postimg.cc/44Dkf1zv/Call_Me_by_Your_Name.jpg', 'https://www.youtube.com/watch?v=Z9AYPxH5NTM', 'During a summer in northern Italy, a deep and transformative romance blossoms between a teenage boy and a visiting scholar. Tenderly directed, the film captures first love, emotional awakening, and the bittersweet nature of growing up.', 4.6),

(31, 'Belle', 'Japanese', 'Mamoru Hosoda', 'Kaho Nakamura, Takeru Satoh, Koji Yakusho, Ikura, Ryō Narita', 121, '2021-07-16', 'P', 'ended', 'https://i.postimg.cc/tRKQqzhW/Belle.jpg', 'https://www.youtube.com/watch?v=izIycj3j4Ow', 'A shy teenage girl discovers a massive virtual world where she can reinvent herself as a global icon. Through music and digital identity, she confronts personal trauma and finds the courage to connect with others in real life.', 4.4),

(32, 'The Nun II', 'English', 'Michael Chaves', 'Taissa Farmiga, Jonas Bloquet, Storm Reid, Anna Popplewell, Bonnie Aarons', 110, '2023-09-08', 'T18', 'ended', 'https://i.postimg.cc/MTnNxcXz/The_Nun_II.jpg', 'https://www.youtube.com/watch?v=QF-oyCwaArU', 'A new chapter in the Conjuring Universe follows Sister Irene as she confronts a demonic entity terrorizing Europe. Dark, atmospheric, and suspenseful, the film expands the mythology of Valak with chilling consequences.', 4.0);

-- ==================== 6. MOVIE_GENRES ====================
-- Depends on: movies, genres
-- Links movies with genres
INSERT INTO movie_genres (movie_id, genre_id) VALUES
-- Avengers: Endgame (1) - Action, Adventure, Sci-Fi
(1, 1), (1, 2), (1, 11),
-- John Wick: Chapter 4 (2) - Action, Crime, Thriller
(2, 1), (2, 5), (2, 12),
-- Parasite (3) - Drama, Thriller, Comedy
(3, 6), (3, 12), (3, 4),
-- Train to Busan (4) - Action, Horror, Thriller
(4, 1), (4, 9), (4, 12),
-- The Dark Knight (5) - Action, Crime, Drama
(5, 1), (5, 5), (5, 6),
-- Avatar (6) - Action, Adventure, Sci-Fi, Fantasy
(6, 1), (6, 2), (6, 11), (6, 8),
-- La La Land (7) - Romance, Drama, Comedy
(7, 10), (7, 6), (7, 4),
-- Your Name (8) - Animation, Romance, Fantasy
(8, 3), (8, 10), (8, 8),
-- Spirited Away (9) - Animation, Adventure, Family, Fantasy
(9, 3), (9, 2), (9, 7), (9, 8),
-- Intouchables (10) - Drama, Comedy
(10, 6), (10, 4),
-- Toy Story (11) - Animation, Adventure, Family, Comedy
(11, 3), (11, 2), (11, 7), (11, 4),
-- The Conjuring (12) - Horror, Mystery, Thriller
(12, 9), (12, 14), (12, 12),
-- Furie (13) - Action, Crime, Thriller
(13, 1), (13, 5), (13, 12),
-- Forrest Gump (14) - Drama, Romance
(14, 6), (14, 10),
-- Peninsula (15) - Action, Horror, Thriller
(15, 1), (15, 9), (15, 12),
-- Dune: Part Two (16) - Action, Adventure, Sci-Fi
(16, 1), (16, 2), (16, 11),
-- Oppenheimer (17) - Drama, Thriller, War
(17, 6), (17, 12), (17, 13),
-- Weathering With You (18) - Animation, Romance, Fantasy
(18, 3), (18, 10), (18, 8),
-- Finding Nemo (19) - Animation, Adventure, Family, Comedy
(19, 3), (19, 2), (19, 7), (19, 4),
-- Decision to Leave (20) - Romance, Mystery, Thriller
(20, 10), (20, 14), (20, 12),
-- Paddington (21) - Adventure, Comedy, Family
(21, 2), (21, 4), (21, 7),
-- Rurouni Kenshin (22) - Action, Adventure, Drama
(22, 1), (22, 2), (22, 6),
-- Blue Is the Warmest Color (23) - Drama, Romance
(23, 6), (23, 10),
-- Intimate Strangers (24) - Drama, Comedy, Thriller
(24, 6), (24, 4), (24, 12),
-- The Medium (25) - Horror, Mystery
(25, 9), (25, 14),
-- Titanic (26) - Drama, Romance
(26, 6), (26, 10),
-- Forrest Gump (27) - Drama, Romance
(27, 6), (27, 10),
-- The Shawshank Redemption (28) - Drama, Crime
(28, 6), (28, 5),
-- The Wailing (29) - Horror, Mystery, Thriller
(29, 9), (29, 14), (29, 12),
-- Call Me by Your Name (30) - Drama, Romance
(30, 6), (30, 10),
-- Belle (31) - Animation, Drama, Fantasy
(31, 3), (31, 6), (31, 8),
-- The Nun II (32) - Horror, Mystery, Thriller
(32, 9), (32, 14), (32, 12);

-- ==================== 7. USERS ====================
-- 16 users: 1 admin + 15 regular users (to match review user_ids)
INSERT INTO users (name, email, password, role) VALUES
('Admin', 'admin@cinebook.com', '123456', 'admin'),
('User One', 'user1@gmail.com', '123456', 'user'),
('User Two', 'user2@gmail.com', '123456', 'user'),
('User Three', 'user3@gmail.com', '123456', 'user'),
('User Four', 'user4@gmail.com', '123456', 'user'),
('User Five', 'user5@gmail.com', '123456', 'user'),
('User Six', 'user6@gmail.com', '123456', 'user'),
('User Seven', 'user7@gmail.com', '123456', 'user'),
('User Eight', 'user8@gmail.com', '123456', 'user'),
('User Nine', 'user9@gmail.com', '123456', 'user'),
('User Ten', 'user10@gmail.com', '123456', 'user'),
('User Eleven', 'user11@gmail.com', '123456', 'user'),
('User Twelve', 'user12@gmail.com', '123456', 'user'),
('User Thirteen', 'user13@gmail.com', '123456', 'user'),
('User Fourteen', 'user14@gmail.com', '123456', 'user'),
('User Fifteen', 'user15@gmail.com', '123456', 'user');

-- ==================== 8. SEATS ====================
-- Depends on: rooms, seat_types
-- Clear existing seats to avoid duplicate errors
SET FOREIGN_KEY_CHECKS = 0;
DELETE FROM seats;
SET FOREIGN_KEY_CHECKS = 1;

-- Create seats for all 4 rooms: 8 rows (A-H), 18 seats per row
INSERT INTO seats (room_id, seat_row, seat_number, seat_code, seat_type_id)
SELECT
    r.id,
    sr.row_char,
    sn.seat_number,
    CONCAT(sr.row_char, sn.seat_number),
    1 -- standard by default
FROM rooms r
JOIN (
    SELECT 'A' AS row_char UNION ALL
    SELECT 'B' UNION ALL
    SELECT 'C' UNION ALL
    SELECT 'D' UNION ALL
    SELECT 'E' UNION ALL
    SELECT 'F' UNION ALL
    SELECT 'G' UNION ALL
    SELECT 'H'
) sr
JOIN (
    SELECT 1 AS seat_number UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL
    SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL
    SELECT 9 UNION ALL SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12 UNION ALL
    SELECT 13 UNION ALL SELECT 14 UNION ALL SELECT 15 UNION ALL SELECT 16 UNION ALL
    SELECT 17 UNION ALL SELECT 18
) sn
WHERE r.id BETWEEN 1 AND 4;

-- Update middle seats (C-E, 7-12) to VIP
UPDATE seats
SET seat_type_id = 2
WHERE seat_row IN ('C','D','E')
  AND seat_number BETWEEN 7 AND 12;

-- Set row H as couple seats (pairs)
UPDATE seats
SET seat_type_id = 3
WHERE seat_row = 'H';

-- ==================== 9. REVIEWS ====================
-- Depends on: users, movies
-- Reviews for now_showing movies (movie_id 1-15)
INSERT INTO reviews (user_id, movie_id, rating, comment, created_at, updated_at) VALUES
-- Movie 1: Avengers: Endgame
(2, 1, 5, 'Absolutely epic! A masterpiece for Marvel fans.', NOW(), NOW()),
(3, 1, 4, 'Good action, but a bit too long.', NOW(), NOW()),
(7, 1, 3, 'Average, expected more from the ending.', NOW(), NOW()),
(8, 1, 2, 'Too many characters, hard to follow.', NOW(), NOW()),
(9, 1, 1, 'Disappointing, not worth the hype.', NOW(), NOW()),
(10, 1, 5, 'Loved every moment, emotional and thrilling.', NOW(), NOW()),
(11, 1, 4, 'Great cast, but some scenes dragged.', NOW(), NOW()),
(12, 1, 3, 'It was okay, not the best Marvel.', NOW(), NOW()),
(13, 1, 2, 'Plot holes ruined it for me.', NOW(), NOW()),
(14, 1, 1, 'Worst Marvel movie I have seen.', NOW(), NOW()),
(15, 1, 5, 'Perfect conclusion to the saga.', NOW(), NOW()),
(16, 1, 4, 'Enjoyed the action, but too long.', NOW(), NOW()),

-- Movie 2: John Wick: Chapter 4
(2, 2, 5, 'John Wick delivers again! Intense action.', NOW(), NOW()),
(3, 2, 4, 'Stylish but a bit repetitive.', NOW(), NOW()),
(7, 2, 3, 'Just another action movie.', NOW(), NOW()),
(8, 2, 2, 'Too violent for my taste.', NOW(), NOW()),
(9, 2, 1, 'Plot is weak, only fights.', NOW(), NOW()),
(10, 2, 5, 'Keanu Reeves is awesome!', NOW(), NOW()),
(11, 2, 4, 'Great stunts, but story lacking.', NOW(), NOW()),
(12, 2, 3, 'Average, nothing special.', NOW(), NOW()),
(13, 2, 2, 'Expected more story.', NOW(), NOW()),
(14, 2, 1, 'Boring, too much violence.', NOW(), NOW()),
(15, 2, 5, 'Best in the series.', NOW(), NOW()),
(16, 2, 4, 'Loved the choreography.', NOW(), NOW()),

-- Movie 3: Parasite
(2, 3, 5, 'Parasite is a true masterpiece.', NOW(), NOW()),
(3, 3, 4, 'Great social commentary, but slow start.', NOW(), NOW()),
(7, 3, 3, 'Interesting, but overrated.', NOW(), NOW()),
(8, 3, 2, 'Did not get the hype.', NOW(), NOW()),
(9, 3, 1, 'Confusing and boring.', NOW(), NOW()),
(10, 3, 5, 'Deserved the Oscar!', NOW(), NOW()),
(11, 3, 4, 'Unique story, well directed.', NOW(), NOW()),
(12, 3, 3, 'Not bad, but not great.', NOW(), NOW()),
(13, 3, 2, 'Too dark for my taste.', NOW(), NOW()),
(14, 3, 1, 'Worst movie I watched this year.', NOW(), NOW()),
(15, 3, 5, 'Brilliant and thought-provoking.', NOW(), NOW()),
(16, 3, 4, 'Loved the twists.', NOW(), NOW()),

-- Movie 4: Train to Busan
(2, 4, 5, 'Best zombie movie ever!', NOW(), NOW()),
(3, 4, 4, 'Emotional and scary.', NOW(), NOW()),
(7, 4, 3, 'Just another zombie flick.', NOW(), NOW()),
(8, 4, 2, 'Predictable plot.', NOW(), NOW()),
(9, 4, 1, 'Not scary at all.', NOW(), NOW()),
(10, 4, 5, 'Great characters and suspense.', NOW(), NOW()),
(11, 4, 4, 'Loved the father-daughter story.', NOW(), NOW()),
(12, 4, 3, 'Average, nothing new.', NOW(), NOW()),
(13, 4, 2, 'Too emotional for a zombie movie.', NOW(), NOW()),
(14, 4, 1, 'Boring, fell asleep.', NOW(), NOW()),
(15, 4, 5, 'Highly recommended!', NOW(), NOW()),
(16, 4, 4, 'Exciting plot.', NOW(), NOW()),

-- Movie 5: The Dark Knight
(2, 5, 5, 'The Dark Knight is legendary.', NOW(), NOW()),
(3, 5, 4, 'Heath Ledger was amazing.', NOW(), NOW()),
(7, 5, 3, 'Good, but too long.', NOW(), NOW()),
(8, 5, 2, 'Did not like the Joker.', NOW(), NOW()),
(9, 5, 1, 'Overrated, not my style.', NOW(), NOW()),
(10, 5, 5, 'Best Batman movie.', NOW(), NOW()),
(11, 5, 4, 'Great drama and action.', NOW(), NOW()),
(12, 5, 3, 'Average superhero film.', NOW(), NOW()),
(13, 5, 2, 'Too dark for Batman.', NOW(), NOW()),
(14, 5, 1, 'Disappointing.', NOW(), NOW()),
(15, 5, 5, 'A true classic.', NOW(), NOW()),
(16, 5, 4, 'Loved the cinematography.', NOW(), NOW()),

-- Movie 6: Avatar
(2, 6, 5, 'Avatar visuals are stunning.', NOW(), NOW()),
(3, 6, 4, 'Loved the world of Pandora.', NOW(), NOW()),
(7, 6, 3, 'Story is just okay.', NOW(), NOW()),
(8, 6, 2, 'Too long and slow.', NOW(), NOW()),
(9, 6, 1, 'Boring, only effects.', NOW(), NOW()),
(10, 6, 5, 'Beautiful and immersive.', NOW(), NOW()),
(11, 6, 4, 'Great 3D effects.', NOW(), NOW()),
(12, 6, 3, 'Not as good as expected.', NOW(), NOW()),
(13, 6, 2, 'Weak plot.', NOW(), NOW()),
(14, 6, 1, 'Did not finish the movie.', NOW(), NOW()),
(15, 6, 5, 'A visual masterpiece.', NOW(), NOW()),
(16, 6, 4, 'Enjoyed the adventure.', NOW(), NOW()),

-- Movie 7: La La Land
(2, 7, 5, 'La La Land is magical.', NOW(), NOW()),
(3, 7, 4, 'Great music and dance.', NOW(), NOW()),
(7, 7, 3, 'Nice, but not memorable.', NOW(), NOW()),
(8, 7, 2, 'Did not like the ending.', NOW(), NOW()),
(9, 7, 1, 'Boring musical.', NOW(), NOW()),
(10, 7, 5, 'Loved the romance.', NOW(), NOW()),
(11, 7, 4, 'Beautiful cinematography.', NOW(), NOW()),
(12, 7, 3, 'Average love story.', NOW(), NOW()),
(13, 7, 2, 'Too much singing.', NOW(), NOW()),
(14, 7, 1, 'Fell asleep.', NOW(), NOW()),
(15, 7, 5, 'Highly recommended.', NOW(), NOW()),
(16, 7, 4, 'Great soundtrack.', NOW(), NOW()),

-- Movie 8: Your Name
(2, 8, 5, 'Your Name is beautiful.', NOW(), NOW()),
(3, 8, 4, 'Amazing animation.', NOW(), NOW()),
(7, 8, 3, 'Good, but confusing.', NOW(), NOW()),
(8, 8, 2, 'Did not get the story.', NOW(), NOW()),
(9, 8, 1, 'Overrated.', NOW(), NOW()),
(10, 8, 5, 'Loved the music.', NOW(), NOW()),
(11, 8, 4, 'Touching story.', NOW(), NOW()),
(12, 8, 3, 'Just okay.', NOW(), NOW()),
(13, 8, 2, 'Too slow.', NOW(), NOW()),
(14, 8, 1, 'Not my type.', NOW(), NOW()),
(15, 8, 5, 'Visually stunning.', NOW(), NOW()),
(16, 8, 4, 'Highly recommended.', NOW(), NOW()),

-- Movie 9: Spirited Away
(2, 9, 5, 'Spirited Away is magical.', NOW(), NOW()),
(3, 9, 4, 'Beautiful animation.', NOW(), NOW()),
(7, 9, 3, 'Weird but interesting.', NOW(), NOW()),
(8, 9, 2, 'Did not like the fantasy.', NOW(), NOW()),
(9, 9, 1, 'Too childish.', NOW(), NOW()),
(10, 9, 5, 'Great fantasy world.', NOW(), NOW()),
(11, 9, 4, 'Loved the characters.', NOW(), NOW()),
(12, 9, 3, 'Average story.', NOW(), NOW()),
(13, 9, 2, 'Not for adults.', NOW(), NOW()),
(14, 9, 1, 'Boring.', NOW(), NOW()),
(15, 9, 5, 'A classic Ghibli film.', NOW(), NOW()),
(16, 9, 4, 'Wonderful story.', NOW(), NOW()),

-- Movie 10: Intouchables
(2, 10, 5, 'Intouchables is heartwarming.', NOW(), NOW()),
(3, 10, 4, 'Great chemistry between leads.', NOW(), NOW()),
(7, 10, 3, 'Funny but predictable.', NOW(), NOW()),
(8, 10, 2, 'Not as good as expected.', NOW(), NOW()),
(9, 10, 1, 'Did not enjoy it.', NOW(), NOW()),
(10, 10, 5, 'Loved the story.', NOW(), NOW()),
(11, 10, 4, 'Very inspiring.', NOW(), NOW()),
(12, 10, 3, 'Just okay.', NOW(), NOW()),
(13, 10, 2, 'Too slow.', NOW(), NOW()),
(14, 10, 1, 'Boring.', NOW(), NOW()),
(15, 10, 5, 'A feel-good movie.', NOW(), NOW()),
(16, 10, 4, 'Great acting.', NOW(), NOW()),

-- Movie 11: Toy Story
(2, 11, 5, 'Toy Story is a classic.', NOW(), NOW()),
(3, 11, 4, 'Loved the toys.', NOW(), NOW()),
(7, 11, 3, 'Good for kids.', NOW(), NOW()),
(8, 11, 2, 'Not funny.', NOW(), NOW()),
(9, 11, 1, 'Too childish.', NOW(), NOW()),
(10, 11, 5, 'Great animation.', NOW(), NOW()),
(11, 11, 4, 'Very funny and sweet.', NOW(), NOW()),
(12, 11, 3, 'Average story.', NOW(), NOW()),
(13, 11, 2, 'Did not like the characters.', NOW(), NOW()),
(14, 11, 1, 'Boring.', NOW(), NOW()),
(15, 11, 5, 'Wonderful story.', NOW(), NOW()),
(16, 11, 4, 'Highly recommended.', NOW(), NOW()),

-- Movie 12: The Conjuring
(2, 12, 5, 'The Conjuring is scary!', NOW(), NOW()),
(3, 12, 4, 'Very suspenseful.', NOW(), NOW()),
(7, 12, 3, 'Just another horror movie.', NOW(), NOW()),
(8, 12, 2, 'Not scary at all.', NOW(), NOW()),
(9, 12, 1, 'Boring and predictable.', NOW(), NOW()),
(10, 12, 5, 'Loved the story.', NOW(), NOW()),
(11, 12, 4, 'Creepy and intense.', NOW(), NOW()),
(12, 12, 3, 'Average horror.', NOW(), NOW()),
(13, 12, 2, 'Too many jump scares.', NOW(), NOW()),
(14, 12, 1, 'Did not like it.', NOW(), NOW()),
(15, 12, 5, 'Best horror film.', NOW(), NOW()),
(16, 12, 4, 'Highly recommended.', NOW(), NOW()),

-- Movie 13: Furie
(2, 13, 5, 'Furie is action-packed.', NOW(), NOW()),
(3, 13, 4, 'Great Vietnamese film.', NOW(), NOW()),
(7, 13, 3, 'Good fights, weak story.', NOW(), NOW()),
(8, 13, 2, 'Too violent.', NOW(), NOW()),
(9, 13, 1, 'Bad acting.', NOW(), NOW()),
(10, 13, 5, 'Loved the fight scenes.', NOW(), NOW()),
(11, 13, 4, 'Very emotional.', NOW(), NOW()),
(12, 13, 3, 'Just okay.', NOW(), NOW()),
(13, 13, 2, 'Not my style.', NOW(), NOW()),
(14, 13, 1, 'Boring.', NOW(), NOW()),
(15, 13, 5, 'Highly recommended.', NOW(), NOW()),
(16, 13, 4, 'Great martial arts.', NOW(), NOW()),

-- Movie 14: Forrest Gump
(2, 14, 5, 'Forrest Gump is inspiring.', NOW(), NOW()),
(3, 14, 4, 'Tom Hanks was perfect.', NOW(), NOW()),
(7, 14, 3, 'Good, but slow.', NOW(), NOW()),
(8, 14, 2, 'Not my type of movie.', NOW(), NOW()),
(9, 14, 1, 'Boring and long.', NOW(), NOW()),
(10, 14, 5, 'Very touching.', NOW(), NOW()),
(11, 14, 4, 'Loved the soundtrack.', NOW(), NOW()),
(12, 14, 3, 'Just okay.', NOW(), NOW()),
(13, 14, 2, 'Too sentimental.', NOW(), NOW()),
(14, 14, 1, 'Did not enjoy it.', NOW(), NOW()),
(15, 14, 5, 'A classic film.', NOW(), NOW()),
(16, 14, 4, 'Wonderful story.', NOW(), NOW()),

-- Movie 15: Peninsula
(2, 15, 5, 'Peninsula is a great sequel!', NOW(), NOW()),
(3, 15, 4, 'Good action, different vibe.', NOW(), NOW()),
(7, 15, 3, 'Good, but not as good as original.', NOW(), NOW()),
(8, 15, 2, 'Missing the emotional depth.', NOW(), NOW()),
(9, 15, 1, 'Not as good as Train to Busan.', NOW(), NOW()),
(10, 15, 5, 'Loved the action scenes.', NOW(), NOW()),
(11, 15, 4, 'Great visuals and stunts.', NOW(), NOW()),
(12, 15, 3, 'Average sequel.', NOW(), NOW()),
(13, 15, 2, 'Too much CGI.', NOW(), NOW()),
(14, 15, 1, 'Disappointing follow-up.', NOW(), NOW()),
(15, 15, 5, 'Exciting zombie action!', NOW(), NOW()),
(16, 15, 4, 'Solid entertainment.', NOW(), NOW());

-- ==================== 10. SHOWTIMES ====================
-- Depends on: movies, rooms
-- Now showing movies: 30/01/2026 - 02/02/2026
-- Room 1: 2D, Room 2: 2D, Room 3: 3D, Room 4: IMAX 4D

INSERT INTO showtimes (movie_id, room_id, show_date, show_time) VALUES
-- Movie 1: Avengers: Endgame (181 min)
(1, 1, '2026-01-30', '10:00:00'),
(1, 3, '2026-01-30', '14:30:00'),
(1, 4, '2026-01-31', '13:00:00'),
(1, 2, '2026-02-01', '19:00:00'),
(1, 4, '2026-02-02', '16:00:00'),

-- Movie 2: John Wick: Chapter 4 (169 min)
(2, 2, '2026-01-30', '11:00:00'),
(2, 4, '2026-01-30', '18:00:00'),
(2, 1, '2026-01-31', '15:00:00'),
(2, 3, '2026-02-01', '20:00:00'),
(2, 2, '2026-02-02', '14:00:00'),

-- Movie 3: Parasite (132 min)
(3, 3, '2026-01-30', '10:30:00'),
(3, 1, '2026-01-31', '13:30:00'),
(3, 4, '2026-01-31', '19:30:00'),
(3, 2, '2026-02-01', '16:00:00'),

-- Movie 4: Train to Busan (118 min)
(4, 1, '2026-01-30', '14:00:00'),
(4, 4, '2026-01-30', '21:00:00'),
(4, 3, '2026-01-31', '17:00:00'),
(4, 2, '2026-02-01', '11:00:00'),
(4, 1, '2026-02-02', '20:00:00'),

-- Movie 5: The Dark Knight (152 min)
(5, 2, '2026-01-30', '15:00:00'),
(5, 4, '2026-01-31', '10:00:00'),
(5, 1, '2026-02-01', '14:00:00'),
(5, 3, '2026-02-02', '19:00:00'),

-- Movie 6: Avatar (162 min)
(6, 4, '2026-01-30', '10:00:00'),
(6, 3, '2026-01-30', '17:30:00'),
(6, 1, '2026-01-31', '19:00:00'),
(6, 4, '2026-02-01', '14:00:00'),
(6, 2, '2026-02-02', '10:30:00'),

-- Movie 7: La La Land (128 min)
(7, 1, '2026-01-30', '17:30:00'),
(7, 2, '2026-01-31', '10:00:00'),
(7, 3, '2026-02-01', '13:00:00'),
(7, 1, '2026-02-02', '16:30:00'),

-- Movie 8: Your Name (112 min)
(8, 3, '2026-01-30', '13:00:00'),
(8, 2, '2026-01-30', '19:00:00'),
(8, 1, '2026-01-31', '10:00:00'),
(8, 4, '2026-02-01', '17:30:00'),
(8, 3, '2026-02-02', '11:00:00'),

-- Movie 9: Spirited Away (125 min)
(9, 1, '2026-01-30', '20:30:00'),
(9, 4, '2026-01-31', '16:00:00'),
(9, 2, '2026-02-01', '13:30:00'),
(9, 3, '2026-02-02', '14:00:00'),

-- Movie 10: Intouchables (112 min)
(10, 2, '2026-01-30', '13:00:00'),
(10, 1, '2026-01-31', '16:30:00'),
(10, 3, '2026-02-01', '10:00:00'),
(10, 4, '2026-02-02', '19:00:00'),

-- Movie 11: Toy Story (81 min)
(11, 3, '2026-01-30', '09:00:00'),
(11, 1, '2026-01-30', '12:00:00'),
(11, 2, '2026-01-31', '13:00:00'),
(11, 4, '2026-02-01', '10:30:00'),
(11, 1, '2026-02-02', '09:30:00'),

-- Movie 12: The Conjuring (112 min)
(12, 4, '2026-01-30', '22:00:00'),
(12, 2, '2026-01-31', '21:00:00'),
(12, 3, '2026-02-01', '22:00:00'),
(12, 1, '2026-02-02', '21:30:00'),

-- Movie 13: Furie (98 min)
(13, 1, '2026-01-30', '15:30:00'),
(13, 3, '2026-01-31', '10:30:00'),
(13, 2, '2026-01-31', '18:00:00'),
(13, 4, '2026-02-01', '20:30:00'),
(13, 1, '2026-02-02', '13:00:00'),

-- Movie 14: Forrest Gump (142 min)
(14, 2, '2026-01-30', '09:30:00'),
(14, 4, '2026-01-31', '13:30:00'),
(14, 1, '2026-02-01', '10:00:00'),
(14, 3, '2026-02-02', '16:30:00'),

-- Movie 15: Peninsula (116 min)
(15, 3, '2026-01-30', '20:00:00'),
(15, 1, '2026-01-31', '21:30:00'),
(15, 4, '2026-02-01', '18:00:00'),
(15, 2, '2026-02-02', '20:30:00');

-- Coming soon movies showtimes (February 2026)
-- Movie 16: Dune: Part Two (165 min) - Release: 15/02/2026
-- Movie 17: Oppenheimer (180 min) - Release: 20/02/2026
-- Movie 18: Weathering With You (114 min) - Release: 25/02/2026

INSERT INTO showtimes (movie_id, room_id, show_date, show_time) VALUES
-- Movie 16: Dune: Part Two (165 min) - from 15/02/2026
(16, 4, '2026-02-15', '10:00:00'),
(16, 3, '2026-02-15', '14:00:00'),
(16, 4, '2026-02-15', '18:30:00'),
(16, 1, '2026-02-15', '21:00:00'),
(16, 4, '2026-02-16', '10:30:00'),
(16, 2, '2026-02-16', '14:30:00'),
(16, 3, '2026-02-16', '19:00:00'),
(16, 4, '2026-02-16', '22:00:00'),
(16, 1, '2026-02-17', '13:00:00'),
(16, 4, '2026-02-17', '17:00:00'),
(16, 3, '2026-02-17', '20:30:00'),
(16, 2, '2026-02-18', '10:00:00'),
(16, 4, '2026-02-18', '14:00:00'),
(16, 1, '2026-02-18', '18:30:00'),
(16, 3, '2026-02-19', '11:00:00'),
(16, 4, '2026-02-19', '15:00:00'),
(16, 2, '2026-02-19', '19:30:00'),
(16, 4, '2026-02-20', '10:00:00'),
(16, 1, '2026-02-20', '14:00:00'),
(16, 3, '2026-02-20', '18:00:00'),

-- Movie 17: Oppenheimer (180 min) - from 20/02/2026
(17, 1, '2026-02-20', '10:00:00'),
(17, 4, '2026-02-20', '14:30:00'),
(17, 2, '2026-02-20', '19:00:00'),
(17, 3, '2026-02-21', '10:00:00'),
(17, 4, '2026-02-21', '14:00:00'),
(17, 1, '2026-02-21', '18:30:00'),
(17, 2, '2026-02-21', '21:00:00'),
(17, 4, '2026-02-22', '11:00:00'),
(17, 3, '2026-02-22', '15:30:00'),
(17, 1, '2026-02-22', '20:00:00'),
(17, 2, '2026-02-23', '10:00:00'),
(17, 4, '2026-02-23', '14:00:00'),
(17, 3, '2026-02-23', '18:30:00'),
(17, 1, '2026-02-24', '13:00:00'),
(17, 4, '2026-02-24', '17:30:00'),
(17, 2, '2026-02-24', '21:00:00'),
(17, 3, '2026-02-25', '10:30:00'),
(17, 4, '2026-02-25', '15:00:00'),
(17, 1, '2026-02-25', '19:30:00'),
(17, 2, '2026-02-26', '14:00:00'),
(17, 4, '2026-02-26', '18:30:00'),
(17, 3, '2026-02-27', '11:00:00'),
(17, 1, '2026-02-27', '15:30:00'),
(17, 4, '2026-02-27', '20:00:00'),
(17, 2, '2026-02-28', '10:00:00'),
(17, 3, '2026-02-28', '14:30:00'),
(17, 4, '2026-02-28', '19:00:00'),

-- Movie 18: Weathering With You (114 min) - from 25/02/2026
(18, 3, '2026-02-25', '10:00:00'),
(18, 1, '2026-02-25', '13:00:00'),
(18, 4, '2026-02-25', '16:00:00'),
(18, 2, '2026-02-25', '19:00:00'),
(18, 3, '2026-02-25', '21:30:00'),
(18, 1, '2026-02-26', '10:30:00'),
(18, 4, '2026-02-26', '13:30:00'),
(18, 2, '2026-02-26', '16:30:00'),
(18, 3, '2026-02-26', '19:30:00'),
(18, 1, '2026-02-26', '22:00:00'),
(18, 2, '2026-02-27', '10:00:00'),
(18, 3, '2026-02-27', '13:00:00'),
(18, 4, '2026-02-27', '16:00:00'),
(18, 1, '2026-02-27', '19:00:00'),
(18, 2, '2026-02-27', '21:30:00'),
(18, 3, '2026-02-28', '10:30:00'),
(18, 4, '2026-02-28', '13:30:00'),
(18, 1, '2026-02-28', '16:30:00'),
(18, 2, '2026-02-28', '19:30:00'),
(18, 3, '2026-02-28', '22:00:00');

-- ==================== 11. SHOWTIME_PRICES ====================
-- Depends on: showtimes, seat_types, rooms, screen_types
-- Price = seat_type.base_price + screen_type.price
INSERT INTO showtime_prices (showtime_id, seat_type_id, price)
SELECT 
    st.id as showtime_id,
    stype.id as seat_type_id,
    (stype.base_price + scr.price) as price
FROM showtimes st
JOIN rooms r ON st.room_id = r.id
JOIN screen_types scr ON r.screen_type_id = scr.id
CROSS JOIN seat_types stype;

-- ==================== 12. SHOWTIME_SEATS ====================
-- Depends on: showtimes, seats
-- Create available seats for all showtimes
INSERT INTO showtime_seats (showtime_id, seat_id, status)
SELECT 
    st.id as showtime_id,
    s.id as seat_id,
    'available' as status
FROM showtimes st
JOIN seats s ON st.room_id = s.room_id;

-- ==================== 13. BOOKINGS ====================
-- Depends on: users, showtimes
-- Sample bookings for showtimes on 30/01/2026
INSERT INTO bookings (user_id, showtime_id, status, payment_status, total_price, created_at, updated_at) 
SELECT 2, st.id, 'confirmed', 'paid', 240000, NOW(), NOW()
FROM showtimes st WHERE st.movie_id = 1 AND st.show_date = '2026-01-30' LIMIT 1;

INSERT INTO bookings (user_id, showtime_id, status, payment_status, total_price, created_at, updated_at) 
SELECT 3, st.id, 'pending', 'pending', 160000, NOW(), NOW()
FROM showtimes st WHERE st.movie_id = 2 AND st.show_date = '2026-01-30' LIMIT 1;

INSERT INTO bookings (user_id, showtime_id, status, payment_status, total_price, created_at, updated_at) 
SELECT 4, st.id, 'cancelled', 'pending', 120000, NOW(), NOW()
FROM showtimes st WHERE st.movie_id = 3 AND st.show_date = '2026-01-30' LIMIT 1;

INSERT INTO bookings (user_id, showtime_id, status, payment_status, total_price, created_at, updated_at) 
SELECT 5, st.id, 'confirmed', 'paid', 320000, NOW(), NOW()
FROM showtimes st WHERE st.movie_id = 4 AND st.show_date = '2026-01-30' LIMIT 1;

INSERT INTO bookings (user_id, showtime_id, status, payment_status, total_price, created_at, updated_at) 
SELECT 6, st.id, 'confirmed', 'paid', 160000, NOW(), NOW()
FROM showtimes st WHERE st.movie_id = 5 AND st.show_date = '2026-01-30' LIMIT 1;

INSERT INTO bookings (user_id, showtime_id, status, payment_status, total_price, created_at, updated_at) 
SELECT 7, st.id, 'pending', 'pending', 240000, NOW(), NOW()
FROM showtimes st WHERE st.movie_id = 6 AND st.show_date = '2026-01-30' LIMIT 1;

INSERT INTO bookings (user_id, showtime_id, status, payment_status, total_price, created_at, updated_at) 
SELECT 8, st.id, 'confirmed', 'paid', 360000, NOW(), NOW()
FROM showtimes st WHERE st.movie_id = 7 AND st.show_date = '2026-01-30' LIMIT 1;

INSERT INTO bookings (user_id, showtime_id, status, payment_status, total_price, created_at, updated_at) 
SELECT 9, st.id, 'cancelled', 'pending', 120000, NOW(), NOW()
FROM showtimes st WHERE st.movie_id = 8 AND st.show_date = '2026-01-30' LIMIT 1;

INSERT INTO bookings (user_id, showtime_id, status, payment_status, total_price, created_at, updated_at) 
SELECT 10, st.id, 'confirmed', 'paid', 200000, NOW(), NOW()
FROM showtimes st WHERE st.movie_id = 9 AND st.show_date = '2026-01-30' LIMIT 1;

INSERT INTO bookings (user_id, showtime_id, status, payment_status, total_price, created_at, updated_at) 
SELECT 11, st.id, 'pending', 'pending', 280000, NOW(), NOW()
FROM showtimes st WHERE st.movie_id = 10 AND st.show_date = '2026-01-30' LIMIT 1;

-- ==================== 14. BOOKING_SEATS ====================
-- Depends on: bookings, showtimes, seats
-- Sample booking seats (2 seats per non-cancelled booking)
INSERT INTO booking_seats (booking_id, showtime_id, seat_id, price, qr_code, qr_status)
SELECT b.id, b.showtime_id, s.id, 80000, CONCAT('QR-', b.id, '-', b.showtime_id, '-', s.id), 'active'
FROM bookings b
JOIN showtimes st ON b.showtime_id = st.id
JOIN seats s ON s.room_id = st.room_id AND s.seat_row = 'A' AND s.seat_number <= 2
WHERE b.status != 'cancelled'
LIMIT 20;

-- ==================== 15. UPDATE SHOWTIME_SEATS STATUS ====================
-- Update status to 'booked' for seats that have been booked
UPDATE showtime_seats ss
JOIN booking_seats bs ON ss.showtime_id = bs.showtime_id AND ss.seat_id = bs.seat_id
JOIN bookings b ON bs.booking_id = b.id
SET ss.status = 'booked'
WHERE b.status IN ('confirmed', 'pending');

--===================== 16. PROMOTIONS ====================
-- Depends on: none

INSERT INTO promotions (category, icon, title, description, details_title, details_items, cta_text, cta_link, validity_text, status, display_order) VALUES

-- CINEMA GIFTS
('cinema-gifts', '🍿', 'Free Premium Popcorn Combo', 'Get a free premium popcorn combo with every ticket purchase!', 'What\'s Included:', '["Large Popcorn (Butter or Caramel)", "2 Medium Soft Drinks", "Movie Snack Coupon for next visit"]', 'Claim Now', '/now-showing', 'Valid until: March 31, 2026', 'active', 1),

('cinema-gifts', '🎬', 'Exclusive Movie Merchandise', 'Buy 2 tickets and get 1 free collectible item from our exclusive merchandise collection!', 'Available Items:', '["Limited Edition Movie Posters", "Character Figurines", "Branded T-Shirts", "Movie Soundtrack CDs"]', 'Shop Now', '/now-showing', 'Limited stock available', 'active', 2),

('cinema-gifts', '🎁', 'Weekend Combo Deal', 'Special combo packages for weekend movie marathons!', 'Package Includes:', '["2 Movie Tickets (Any showtime)", "Family Size Popcorn", "4 Soft Drinks", "Free Parking Pass"]', 'Get Deal', '/now-showing', 'Available: Friday - Sunday', 'active', 3),

-- MEMBER REWARDS
('member-rewards', '🎂', 'Birthday Special', 'Celebrate your birthday with us! Get a free movie ticket on your special day.', 'Birthday Benefits:', '["1 Free Movie Ticket (Any movie, any time)", "Free Medium Popcorn & Drink", "20% off on additional tickets", "Priority Seat Selection"]', 'Register Now', '/register', 'Valid for 7 days around your birthday', 'active', 4),

('member-rewards', '⭐', 'VIP Membership', 'Join our VIP program and enjoy exclusive benefits year-round!', 'VIP Perks:', '["10% off all ticket purchases", "Free seat upgrades (subject to availability)", "Early access to new releases", "Exclusive member-only screenings", "Free snack vouchers monthly"]', 'Join VIP', '/register', 'Annual membership: 200.000d', 'active', 5),

('member-rewards', '🎯', 'Loyalty Points', 'Earn points with every purchase and redeem for free tickets and snacks!', 'How It Works:', '["Earn 1 point per 10.000 spent", "100 points = Free movie ticket", "50 points = Free snack combo", "Double points on your birthday month"]', 'Start Earning', '/register', 'Points never expire', 'active', 6),

-- STUDENT DEALS
('student-deals', '🎓', 'Student Discount', 'Show your student ID and enjoy 20% off every Tuesday and Wednesday!', 'Eligibility:', '["Valid student ID required", "High school and college students", "Applies to all showtimes", "Can be combined with matinee pricing"]', 'Get Student Card', '/now-showing', 'Available: Every Tuesday & Wednesday', 'active', 7),

('student-deals', '📚', 'Study Break Special', 'Take a break from studying! Special pricing for students during exam season.', 'Special Offer:', '["40.000d tickets for all movies before 5 PM", "Free study room rental (2 hours)", "Discounted coffee and snacks", "Valid during midterm and finals weeks"]', 'Book Now', '/now-showing', 'During academic exam periods', 'active', 8),

('student-deals', '👥', 'Student Group Discount', 'Bring your friends! Groups of 5+ students get additional 15% off.', 'Group Benefits:', '["Minimum 5 students required", "15% off regular student price", "Reserved group seating", "Group snack packages available"]', 'Book Group', '/now-showing', 'Advance booking required', 'active', 9),

-- SEASONAL OFFERS
('seasonal', '🎃', 'Holiday Special', 'Celebrate holidays with special movie packages and themed events!', 'Holiday Perks:', '["Buy 1 Get 1 Free on major holidays", "Special themed movie marathons", "Holiday-themed snack combos", "Free photo booth access"]', 'View Events', '/upcoming-movies', 'Major holidays throughout the year', 'active', 10),

('seasonal', '❄️', 'Winter Season Pass', 'Stay warm with unlimited movies during winter season!', 'Pass Includes:', '["Unlimited movie tickets (Dec - Feb)", "20% off all concessions", "Priority booking for blockbusters", "Exclusive winter-themed events"]', 'Buy Pass', '/register', 'Season pass: 700.000d', 'active', 11),

('seasonal', '💝', 'Valentine\'s Special', 'Perfect date package for couples this Valentine\'s Day!', 'Romance Package:', '["2 Premium Couple Seats", "Champagne & Chocolate Box", "Complimentary roses", "Private theater experience available"]', 'Book Romance', '/now-showing', 'February 10-16, 2026', 'active', 12);


-- ============================================================
-- END OF DATA
-- ============================================================
