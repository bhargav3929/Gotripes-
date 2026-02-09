-- =============================================
-- Emirates + Activities Data Import for Hostinger (MySQL)
-- Import this via phpMyAdmin
-- =============================================

-- 1. INSERT EMIRATES
INSERT INTO `tbl_emirates` (`emiratesID`, `emiratesName`, `emiratesDescription`, `emiratesImage`, `isActive`, `createdBy`, `createdDate`) VALUES
(1, 'Dubai', 'Dubai is a city and emirate in the United Arab Emirates luxury shopping, ultramodern architecture and a lively nightlife scene.', 'assets/emirates/dubai.jpg', 1, 'Antigravity', NOW()),
(2, 'Abu Dhabi', 'The capital of the UAE, Abu Dhabi is known for its rich culture, stunning mosques, and high-end island resorts.', 'assets/emirates/abu-dhabi.jpg', 1, 'Antigravity', NOW()),
(3, 'Sharjah', 'Known as the Cultural Capital of the UAE, Sharjah is home to numerous museums, heritage sites, and traditional souks.', 'assets/emirates/sharjah.jpg', 1, 'Antigravity', NOW()),
(4, 'Ajman', 'The smallest emirate, Ajman offers beautiful beaches, a rich maritime history, and a peaceful atmosphere.', 'assets/emirates/ajman.jpg', 1, 'Antigravity', NOW()),
(5, 'Fujairah', 'The only emirate on the Gulf of Oman, Fujairah is famous for its mountains, diving spots, and historic forts.', 'assets/emirates/fujairah.jpg', 1, 'Antigravity', NOW()),
(6, 'Ras Al Khaimah', 'RAK is known for its diverse landscapes, from desert dunes to the highest mountains in the UAE.', 'assets/emirates/ras-al-khaimah.jpg', 1, 'Antigravity', NOW()),
(7, 'Umm Al Quwain', 'A quiet emirate offering traditional experiences, mangrove forests, and coastal adventures.', 'assets/emirates/umm-al-quwain.jpg', 1, 'Antigravity', NOW());

-- 2. INSERT ACTIVITIES
INSERT INTO `tbl_UAEActivities` (`activityID`, `activityName`, `activityLocation`, `activityImage`, `activityCurrency`, `activityPrice`, `emiratesID`, `isActive`, `createdBy`, `createdDate`) VALUES
(1, 'Burj Khalifa Top View', 'Downtown Dubai', 'assets/activities/1762343053_QAbWrA1hQD.webp', '$', 179.00, 1, 1, 'Antigravity', NOW()),
(2, 'Desert Safari with BBQ Dinner', 'Dubai Desert', 'assets/activities/1762343123_zx77m9stOi.webp', '$', 150.00, 1, 1, 'Antigravity', NOW()),
(3, 'Dubai Marina Dinner Cruise', 'Dubai Marina', 'assets/activities/1762344006_UCCWfivdmW.png', '$', 120.00, 1, 1, 'Antigravity', NOW()),
(4, 'Sheikh Zayed Grand Mosque Tour', 'Abu Dhabi City', 'assets/activities/1762344279_O1CARAFXLX.jpg', '$', 50.00, 2, 1, 'Antigravity', NOW()),
(5, 'Ferrari World Abu Dhabi', 'Yas Island', 'assets/activities/1762344407_QvRm9GPEFY.jpg', '$', 310.00, 2, 1, 'Antigravity', NOW()),
(6, 'Sharjah Art Museum Visit', 'Arts Area, Sharjah', 'assets/activities/1762344766_iZy8U3t1vX.jpg', '$', 30.00, 3, 1, 'Antigravity', NOW()),
(7, 'Ajman Museum Experience', 'Ajman City', 'assets/activities/1762344876_oQtHQfzyi1.jpg', '$', 20.00, 4, 1, 'Antigravity', NOW()),
(8, 'Snoopy Island Snorkeling', 'Al Aqah, Fujairah', 'assets/activities/1762345119_IFUAGZDkvz.jpg', '$', 150.00, 5, 1, 'Antigravity', NOW()),
(9, 'Jebel Jais Flight Zipline', 'Jebel Jais', 'assets/activities/1762345169_8FjnOiOQiw.jpg', '$', 350.00, 6, 1, 'Antigravity', NOW()),
(10, 'Dreamland Aqua Park', 'Umm Al Quwain', 'assets/activities/1762345268_PAbISv6fds.jpg', '$', 160.00, 7, 1, 'Antigravity', NOW());

-- 3. INSERT ACTIVITY DETAILS
INSERT INTO `tbl_UAEActivityDetails` (`detailsID`, `detailsOverview`, `detailsIminfo`, `detailsHighlights`, `isActive`, `createdBy`, `createdDate`, `activityID`) VALUES
(1, 'Experience the world''s tallest building with breathtaking views from the 124th and 125th floors.', 'Standard important information for Burj Khalifa Top View', 'Experience the best of Burj Khalifa Top View', 1, 'Antigravity', NOW(), 1),
(2, 'An adventurous journey through the golden dunes of Dubai followed by a traditional Arabic dinner.', 'Standard important information for Desert Safari with BBQ Dinner', 'Experience the best of Desert Safari with BBQ Dinner', 1, 'Antigravity', NOW(), 2),
(3, 'Enjoy a romantic dinner on a traditional wooden dhow while cruising through the stunning Dubai Marina.', 'Standard important information for Dubai Marina Dinner Cruise', 'Experience the best of Dubai Marina Dinner Cruise', 1, 'Antigravity', NOW(), 3),
(4, 'Visit one of the world''s largest and most beautiful mosques, a masterpiece of Islamic architecture.', 'Standard important information for Sheikh Zayed Grand Mosque Tour', 'Experience the best of Sheikh Zayed Grand Mosque Tour', 1, 'Antigravity', NOW(), 4),
(5, 'The first Ferrari-branded theme park in the world, featuring the fastest rollercoaster.', 'Standard important information for Ferrari World Abu Dhabi', 'Experience the best of Ferrari World Abu Dhabi', 1, 'Antigravity', NOW(), 5),
(6, 'Explore the rich artistic heritage of the region with an extensive collection of Arabic art.', 'Standard important information for Sharjah Art Museum Visit', 'Experience the best of Sharjah Art Museum Visit', 1, 'Antigravity', NOW(), 6),
(7, 'Discover the history of Ajman in this beautifully restored fort that served as the ruler''s palace.', 'Standard important information for Ajman Museum Experience', 'Experience the best of Ajman Museum Experience', 1, 'Antigravity', NOW(), 7),
(8, 'Dive into crystal clear waters and explore the vibrant marine life around the famous Snoopy Island.', 'Standard important information for Snoopy Island Snorkeling', 'Experience the best of Snoopy Island Snorkeling', 1, 'Antigravity', NOW(), 8),
(9, 'Soar through the air on the world''s longest zipline, located at the highest point in the UAE.', 'Standard important information for Jebel Jais Flight Zipline', 'Experience the best of Jebel Jais Flight Zipline', 1, 'Antigravity', NOW(), 9),
(10, 'One of the largest water parks in the region, offering a wide range of water slides and attractions.', 'Standard important information for Dreamland Aqua Park', 'Experience the best of Dreamland Aqua Park', 1, 'Antigravity', NOW(), 10);
