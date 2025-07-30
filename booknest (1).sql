-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 30, 2025 at 07:11 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `booknest`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES
(1, 'Biography and Autobiography', '2025-07-29 16:06:36'),
(2, 'Comics', '2025-07-29 16:06:36'),
(3, 'Fantasy', '2025-07-29 16:06:36'),
(4, 'Horror', '2025-07-29 16:06:36'),
(5, 'Mystery and Thriller', '2025-07-29 16:06:36'),
(6, 'Philosophy', '2025-07-29 16:06:36');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `review_id` int(11) DEFAULT NULL,
  `commenter_name` varchar(100) DEFAULT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `review_id`, `commenter_name`, `comment`, `created_at`) VALUES
(4, 56, 'yao', 'Hk', '2025-07-30 01:54:09');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `event_date` date NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `title`, `content`, `image_path`, `category_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'Adman Madman', 'Adman Madman by Prahlad Kakar & Rupangi Sharma is an engaging memoir exploring advertising, creativity, and personal growth.', 'uploads/images/Adman Madman by Prahlad Kakar & Rupangi Sharma.jpg', 1, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(2, 'Anandibai Joshee: A Life in Poems', 'Anandibai Joshee: A Life in Poems by Shikha Malviya poetically captures the inspiring journey of India’s first female doctor.', 'uploads/images/Anandibai Joshee A Life in Poems by Shikha Malviya.jpg', 1, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(3, 'Gujarmal Modi – Sahsi Udyogpati', 'Gujarmal Modi – Sahsi Udyogpati explores the remarkable life of industrialist Gujarmal Modi and his contributions to Indian business.', 'uploads/images/Gujarmal Modi – Sahsi Udyogpati by Sonu Bhasin & Dheeraj Kumar Agarwal.jpg', 1, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(4, 'Indian Lives – Kamaladevi Chattopadhyay: The Art of Freedom', 'Indian Lives by Nico Slate details the life of Kamaladevi Chattopadhyay, a pioneering freedom fighter and social reformer.', 'uploads/images/Indian Lives – Kamaladevi Chattopadhyay The Art of Freedom by Nico Slate.jpg', 1, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(5, 'Insatiable: My Hunger for Life', 'Insatiable: My Hunger for Life by Shobhaa Dé is a candid memoir reflecting on her experiences, relationships, and career.', 'uploads/images/Insatiable My Hunger for Life by Shobhaa Dé.jpg', 1, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(6, 'My Journey - Transforming Dreams Into Actions', 'My Journey by A.P.J. Abdul Kalam offers personal insights into his life, struggles, and achievements.', 'uploads/images/MY JOURNEY - TRANSFORMING DREAMS INTO ACTIONS by apj abdul kalam.jpg', 1, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(7, 'Being Ritu: The Unforgettable Story of Ritu Nanda', 'Being Ritu by Sathya Saran chronicles the inspiring journey of Ritu Nanda, an entrepreneur and insurance advisor.', 'uploads/images/Ritu Nanda by Sathya Saran.jpg', 1, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(8, 'Snakes, Drugs and Rock ‘n’ Roll: My Early Years', 'A thrilling autobiography exploring Whitaker’s adventures in wildlife conservation and love for reptiles.', 'uploads/images/Snakes, Drugs and Rock ‘n’ Roll My Early Years by Romulus Whitaker & Janaki Lenin.jpg', 1, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(9, 'The Last Courtesan: Writing My Mother’s Memoir', 'A poignant memoir delving into the life of the author’s mother, a former courtesan.', 'uploads/images/The Last Courtesan Writing My Mother’s Memoir by Manish Gaekwad.jpg', 1, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(10, 'The Scrapper’s Way', 'The Scrapper’s Way by Damodar Padhi highlights personal and professional resilience in overcoming challenges.', 'uploads/images/The Scrapper’s Way by Damodar Padhi.jpg', 1, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(11, 'Udaan: Air Deccan ka Safar', 'Udaan: Air Deccan ka Safar narrates the story of Air Deccan and its impact on Indian aviation.', 'uploads/images/Udaan Air Deccan ka Safar by Capt. G R Gopinath.jpg', 1, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(12, 'Wings of Fire: An Autobiography', 'Wings of Fire is an inspiring autobiography of A.P.J. Abdul Kalam, India’s Missile Man.', 'uploads/images/Wings Of Fire An Autobiography by apj abdul kalam and arun tiwari.jpg', 1, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(13, 'Ye Jeevan Khel Mein', 'An autobiographical work by Girish Karnad reflecting on his life, theater, and culture.', 'uploads/images/Ye Jeevan Khel Mein by Girish Karnad, Srinath Perur & Madhu Joshi.jpg', 1, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(14, 'Alien Black, White & Blood', 'Alien Black, White & Blood is a gripping anthology featuring intense, action-packed Alien stories. Showcasing different creators, it brings fresh horror and sci-fi storytelling while exploring Xenomorph terror. With stunning black-and-white visuals and bold narratives, this volume is a must-read for fans of suspenseful, high-stakes encounters in the Alien universe.', 'uploads/images/Alien Black, White & Blood by collin kelly.jpg', 2, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(15, 'Boyfriends, Vol. 3', 'Boyfriends, Vol. 3 continues the heartwarming journey of four diverse students navigating love and friendship. This installment deepens their relationships, presenting humorous and touching moments that celebrate LGBTQ+ representation. With charming artwork and an engaging storyline, it captures romance, personal growth, and the joys of young love in a delightful way.', 'uploads/images/Boyfriends, Vol. 3 by refrainbow.jpg', 2, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(16, 'Cursed Princess Club, Vol. 4', 'Cursed Princess Club, Vol. 4 follows Gwendolyn as she embraces self-acceptance in a magical world. Facing new challenges, friendships grow stronger, and secrets unfold in this whimsical, humorous series. Blending fantasy with powerful messages of individuality, it offers a delightful journey of resilience, love, and self-discovery for readers of all ages.', 'uploads/images/Cursed Princess Club, Vol. 4 by lambcat.jpg', 2, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(17, 'Falling in Love on the Path to Hell Volume One', 'Falling in Love on the Path to Hell tells a high-stakes tale where crime and romance collide. As the protagonists navigate danger, betrayal, and unexpected emotions, the story delivers thrilling action and intense drama. This volume captivates with its gripping narrative, offering an unforgettable blend of love and suspense.', 'uploads/images/Falling in Love on the Path to Hell Volume One by gerry duggan.jpg', 2, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(18, 'Poison Ivy, Vol. 4 Origin of Species', 'Poison Ivy, Vol. 4 explores the transformation of one of DC’s most intriguing antiheroes. As Ivy struggles with identity and power, she reshapes the world through her unique vision. This volume delivers a compelling blend of eco-terrorism, intrigue, and breathtaking art, making it an essential read for fans of complex characters.', 'uploads/images/Poison Ivy, Vol. 4 Origin of Species by g. willow wilson.jpg', 2, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(19, 'Secret Class', 'Secret Class is a gripping story filled with intrigue, unexpected relationships, and hidden emotions. Blending drama with deep character exploration, it delivers engaging storytelling and striking artwork. As secrets unfold, tension builds, keeping readers captivated until the last page. This volume is perfect for fans of bold, mature-themed comics.', 'uploads/images/secret class by Wang kang cheol.jpg', 2, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(20, 'Star Wars The High Republic Adventures Phase III Volume', 'Set in the Star Wars High Republic era, this volume takes readers on a thrilling adventure filled with Jedi, dangerous conflicts, and galaxy-spanning mysteries. Featuring compelling characters and action-packed storytelling, it captures the essence of Star Wars, offering fans an exciting journey into a new and unexplored time period.', 'uploads/images/Star Wars The High Republic Adventures Phase III Volume by daniel jose older.jpg', 2, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(21, 'The One Hand and The Six Fingers', 'The One Hand and The Six Fingers is a dark, gripping tale of mystery and suspense. Blending supernatural elements with noir storytelling, it follows characters entangled in dangerous secrets. With an immersive narrative and striking visuals, this volume is a must-read for fans of thrilling and thought-provoking graphic novels.', 'uploads/images/The One Hand and The Six Fingers by ram v, dan wratters.jpg', 2, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(22, 'Ultimate Wolverine (2025-) #1', 'Ultimate Wolverine #1 reimagines the legendary mutant in a brand-new story, exploring his raw power, relentless nature, and deep inner struggles. With dynamic action, emotional depth, and stunning artwork, this fresh take on Wolverine’s origins and battles offers an intense and exhilarating experience for longtime fans and new readers alike.', 'uploads/images/ultimate wolverine.jpg', 2, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(23, 'Very Bad at Math', 'Very Bad at Math is a witty and heartfelt story about overcoming fears and self-doubt. With humor and relatable moments, it follows a protagonist navigating personal challenges while discovering strengths. Hope Larson delivers charming illustrations and a touching narrative, making this a delightful read for those who enjoy uplifting and fun stories.', 'uploads/images/Very Bad at Math by hope larson.jpg', 2, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(24, 'A Language of Dragons', 'A Language of Dragons explores ancient magic, forgotten tongues, and an epic quest. As scholars uncover secrets of dragon speech, kingdoms tremble. With breathtaking world-building, intriguing lore, and compelling characters, this fantasy novel offers an unforgettable journey into a world where words hold immense power and mythical creatures shape destinies.', 'uploads/images/A Language of Dragons by s.f. williamson.jpg', 3, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(25, 'At the Bottom of the Garden', 'At the Bottom of the Garden is a haunting fairy tale blending reality with eerie folklore. Mysterious creatures lurk where childhood memories fade, and dark secrets are buried. With lyrical prose and spine-chilling suspense, this novel explores the thin veil between fantasy and nightmare, leaving readers spellbound and deeply unsettled.', 'uploads/images/at the bottom of the garden by camilla bruce.jpg', 3, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(26, 'Breath of the Dragon', 'Breath of the Dragon is an empowering book that blends philosophy, martial arts wisdom, and personal growth. Inspired by Bruce Lee’s teachings, it delves into strength, resilience, and transformation. With inspiring anecdotes and deep insights, this book is a must-read for those seeking inner power and wisdom through self-discovery.', 'uploads/images/Breath of the dragon by shannon lee.jpg', 3, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(27, 'Death of the Author', 'Death of the Author is a mind-bending, genre-defying tale exploring identity, creation, and storytelling itself. As reality blurs, a writer’s journey takes unexpected turns, challenging the boundaries between fiction and truth. With masterful prose and thought-provoking themes, this novel offers an unforgettable exploration of what it means to be human.', 'uploads/images/Death of the Author by Nnedi okorafor.jpg', 3, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(28, 'Four Ruined Realms', 'Four Ruined Realms is a sweeping fantasy epic filled with magic, betrayal, and war. As four shattered kingdoms fight for survival, unlikely heroes rise. With intricate political intrigue, breathtaking landscapes, and a gripping narrative, this novel takes readers on an immersive journey through a world on the brink of chaos.', 'uploads/images/Four Ruined Realms by mai corland.jpg', 3, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(29, 'Honeysuckle and Bone', 'Honeysuckle and Bone is a beautifully eerie tale weaving magic, folklore, and haunting family secrets. As the protagonist uncovers a legacy tied to dark forces, reality twists around her. With poetic storytelling and vivid imagery, this novel enchants readers, blurring the line between the natural world and supernatural mysteries.', 'uploads/images/Honeysuckle and Bone by trishsa tobias.jpg', 3, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(30, 'I Am Legend', 'I Am Legend is a chilling masterpiece of horror and survival. The last man on Earth battles against vampire-like creatures in a world overtaken by darkness. With psychological depth and gripping tension, Matheson’s novel redefines the vampire myth, delivering a haunting, thought-provoking tale of isolation, humanity, and the unknown.', 'uploads/images/I Am Legend by Richard Matheson.jpg', 3, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(31, 'The Dryad Storm', 'The Dryad Storm is an enchanting fantasy novel filled with elemental magic, ancient forests, and a war brewing between mystical beings. As a young heroine uncovers her true power, she must decide where her loyalties lie. With breathtaking world-building and compelling characters, this novel delivers a thrilling, magical adventure.', 'uploads/images/The Dryad Storm by laurie forest.jpg', 3, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(32, 'The In-Between Bookstore', 'The In-Between Bookstore is a whimsical, heartfelt fantasy about a mysterious shop bridging worlds. As a young book lover stumbles upon its secrets, they find themselves caught in a magical adventure. With charming characters, enchanting settings, and a love for stories, this novel is a dream for book enthusiasts.', 'uploads/images/The In-Between Bookstore by edward underhill.jpg', 3, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(33, 'The Southern Book Club’s Guide to Slaying Vampires', 'The Southern Book Club’s Guide to Slaying Vampires is a thrilling, darkly humorous novel where a group of women uncovers terrifying secrets. When a mysterious stranger arrives, their peaceful community faces horrifying truths. Blending horror, humor, and strong female characters, this book offers an unforgettable vampire story with a twist.', 'uploads/images/The Southern Book Club’s Guide to Slaying Vampires by Grady Hendrix.jpg', 3, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(34, 'Vampires of El Norte', 'Vampires of El Norte is a gripping supernatural tale set in 19th-century Mexico. As a forbidden love story unfolds amid war and dark creatures, the novel blends history, romance, and horror. With atmospheric storytelling and compelling characters, this book is a mesmerizing, spine-tingling adventure for fans of gothic fiction.', 'uploads/images/Vampires of El Norte by Isabel Cañas.jpg', 3, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(35, 'Woman, Eating', 'Woman, Eating is a fresh, literary take on the vampire genre, following a young half-vampire struggling with identity and hunger. As she navigates modern life, themes of isolation, culture, and self-acceptance emerge. With poetic prose and deep introspection, this novel offers a unique, haunting exploration of what it means to belong.', 'uploads/images/Woman, Eating by Claire Kohda.jpg', 3, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(36, 'Cabin at the End of the World', 'Cabin at the End of the World is a chilling thriller about a family held hostage by four strangers. As an apocalypse looms, they face an impossible choice. With intense psychological horror, gripping tension, and shocking twists, this novel explores fear, sacrifice, and the fragility of reality, leaving readers breathless.', 'uploads/images/Cabin at the End of the World by Paul G. Tremblay.png', 4, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(37, 'Devolution', 'Devolution is a terrifying blend of survival horror and cryptid lore. A high-tech eco-community collapses as Bigfoot-like creatures emerge from the shadows. Told through journal entries and interviews, Max Brooks delivers a gripping, unsettling tale of isolation, primal instincts, and the brutal struggle to stay alive in a world gone wrong.', 'uploads/images/Devolution by Max Brooks.png', 4, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(38, 'Horrorstör', 'Horrorstör is a uniquely terrifying horror-comedy set in a haunted IKEA-like furniture store. Employees working the night shift uncover eerie occurrences, leading to an escalating nightmare. Blending humor, suspense, and supernatural horror, this novel delivers a fast-paced, spine-chilling experience wrapped in a clever and creative format that mimics a store catalog.', 'uploads/images/Horrorstör by Grady Hendrix.png', 4, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(39, 'I’m Thinking of Ending Things', 'I’m Thinking of Ending Things is a psychological thriller filled with eerie tension and existential dread. A couple’s road trip takes a disturbing turn, unraveling reality itself. With unsettling prose and shocking revelations, this novel challenges perception, sanity, and the nature of human relationships, leaving readers haunted long after finishing.', 'uploads/images/I’m Thinking of Ending Things by Iain Reid.png', 4, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(40, 'Our Winter Monster', 'Our Winter Monster is a haunting, atmospheric horror novel set in a snow-covered town plagued by an ancient terror. As paranoia grows and the monster lurks closer, survivors must confront both supernatural and human horrors. With chilling suspense, rich storytelling, and an eerie winter backdrop, this tale is truly unforgettable.', 'uploads/images/Our Winter Monster by dennis a mahoney.jpg', 4, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(41, 'Tender Is the Flesh', 'Tender Is the Flesh is a chilling dystopian horror novel where cannibalism becomes legal. As a man working in the meat industry begins questioning morality, he faces horrifying realities. With disturbing themes, sharp social critique, and gut-wrenching storytelling, this book explores human depravity and ethical nightmares in a terrifying future.', 'uploads/images/Tender Is the Flesh by Agustina Bazterrica.png', 4, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(42, 'The Exorcist', 'The Exorcist is a horror classic that tells the terrifying story of a young girl possessed by a demon. As priests battle supernatural forces, the novel delves into faith, fear, and the nature of evil. With unforgettable imagery and chilling suspense, this tale remains one of the scariest ever written.', 'uploads/images/The Exorcist by william peter blatty.avif', 4, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(43, 'The Raft', 'The Raft is a short but terrifying horror story about four college students stranded on a floating raft, stalked by an unknown entity lurking beneath the water. With claustrophobic tension, gruesome imagery, and relentless horror, this tale is a haunting example of King’s ability to turn ordinary fears into nightmares.', 'uploads/images/The Raft by Stephen King.png', 4, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(44, 'The Ruins', 'The Ruins is a brutal psychological horror novel about a group of tourists trapped in the Mexican jungle, tormented by an ancient, predatory force. As paranoia, desperation, and terror set in, survival becomes a horrifying ordeal. With relentless suspense and graphic horror, this novel delivers an unrelenting, nerve-shredding experience.', 'uploads/images/The Ruins by Scott Smith.png', 4, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(45, 'The Shining Girls', 'The Shining Girls is a genre-bending thriller where a time-traveling serial killer stalks “shining” young women across decades. When one survivor fights back, a gripping game of cat and mouse unfolds. With mind-bending twists, eerie suspense, and a strong female lead, this novel delivers a hauntingly original take on horror.', 'uploads/images/The Shining Girls by Lauren Beukes.png', 4, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(46, 'A Sea of Unspoken Things', 'A Sea of Unspoken Things is a suspenseful novel filled with buried secrets, haunting pasts, and mysterious disappearances. When a woman returns to her childhood home, she unravels a dark mystery that threatens everything she knows. With lyrical writing and gripping twists, this novel explores loss, deception, and the power of truth.', 'uploads/images/A Sea of Unspoken Things by adrienne young.jpg', 5, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(47, 'As Good As Dead', 'As Good As Dead is the thrilling conclusion to the bestselling Good Girl’s Guide to Murder series. When Pip finds herself the target of a stalker, she realizes a past case may not be over. With shocking twists and heart-pounding suspense, this novel keeps readers on the edge until the end.', 'uploads/images/As Good As Dead by holly jackson.jpg', 5, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(48, 'Frieda McFadden', 'Freida McFadden delivers another gripping psychological thriller filled with twists and mind games. This novel explores unreliable narrators, shocking betrayals, and the dark secrets people keep hidden. With unpredictable storytelling and an eerie atmosphere, McFadden takes readers on a chilling journey where nothing is as it seems.', 'uploads/images/frieda mcfadden by Freida McFadden.jpg', 5, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(49, 'Missing in Flight', 'Missing in Flight is a high-stakes thriller where a routine flight turns into a nightmare. When a passenger vanishes midair, an investigator uncovers a deeper conspiracy. With intense action, compelling characters, and a mystery that keeps unraveling, this novel is perfect for fans of aviation thrillers and pulse-pounding suspense.', 'uploads/images/Missing in Flight by audrey j cole.jpg', 5, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(50, 'Penitence', 'Penitence is a haunting psychological thriller that delves into guilt, redemption, and hidden pasts. A woman seeking refuge in a small town soon finds herself entangled in a chilling mystery. As secrets unfold, she realizes danger is closer than she thought. With gripping tension and eerie suspense, this novel is unforgettable.', 'uploads/images/penitence by Kristin Koval.jpg', 5, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(51, 'Tell Me What You Did', 'Tell Me What You Did is an electrifying thriller filled with deception and psychological suspense. A chilling message forces a journalist to confront a crime from her past. As she digs deeper, lies unravel and danger looms. With unexpected twists and a gripping pace, this novel keeps readers hooked until the final revelation.', 'uploads/images/Tell Me What You Did by carter wilson.jpg', 5, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(52, 'The Amendment', 'The Amendment is a jaw-dropping psychological thriller exploring toxic relationships and deadly secrets. A couple’s weekend getaway turns sinister when they realize they are not alone. As trust shatters, survival becomes the only priority. With tension, manipulation, and shocking twists, this novel delivers a heart-pounding experience.', 'uploads/images/The Amendment by kiersten modglin.jpg', 5, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(53, 'The Lost House', 'The Lost House is a chilling mystery where an abandoned home holds dark secrets. When a group of friends enters, they uncover terrifying clues about its past. With an eerie atmosphere, shocking revelations, and suspense that builds relentlessly, this novel is a must-read for fans of gothic thrillers and haunted mysteries.', 'uploads/images/The Lost House by melissa larsen.jpg', 5, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(54, 'The Perfect Home', 'The Perfect Home is a psychological thriller about a dream house that hides nightmarish secrets. A couple moves into their ideal home, only to discover unsettling truths. As paranoia sets in, they must unravel the house’s dark past. With intense suspense and psychological depth, this novel keeps readers captivated.', 'uploads/images/The Perfect Home by daniel kenitz.jpg', 5, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(55, 'We Are Watching', 'We Are Watching is a gripping thriller where surveillance, obsession, and paranoia collide. When a woman realizes she’s being watched, she unravels a terrifying conspiracy. With masterful suspense, relentless twists, and psychological intensity, this novel forces readers to question reality. A chilling exploration of trust, privacy, and hidden dangers.', 'uploads/images/We Are Watching by alison gaylin.jpg', 5, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(56, 'A History of Western Philosophy', 'A History of Western Philosophy is a comprehensive analysis of philosophical thought from ancient Greece to modern times. Bertrand Russell explores major philosophers, their ideas, and how they shaped the world. With clarity and wit, this book remains an essential introduction to Western philosophical traditions and intellectual history.', 'uploads/images/A History of Western Philosophy by bertrand russell.jpg', 6, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(57, 'Beyond Good and Evil', 'Beyond Good and Evil challenges traditional moral values, urging readers to rethink truth, morality, and human nature. Friedrich Nietzsche’s provocative insights dissect philosophical assumptions, encouraging independent thought. This masterpiece questions conventional wisdom and explores the complexities of human will, power, and perspective.', 'uploads/images/Beyond Good and Evil by friedrich nietzsche.jpg', 6, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(58, 'Critique of Pure Reason', 'Critique of Pure Reason is Immanuel Kant’s groundbreaking work on metaphysics and epistemology. It examines human perception, knowledge, and the limits of reason. Kant introduces the concept of a priori knowledge, shaping modern philosophy and influencing debates on reality, consciousness, and the nature of existence.', 'uploads/images/Critique of Pure Reason by immanuel kant.jpg', 6, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(59, 'Letter from a Stoic', 'Letter from a Stoic offers timeless wisdom on resilience, virtue, and self-discipline. Seneca’s letters provide philosophical guidance on living a meaningful life, handling adversity, and achieving inner peace. His Stoic teachings remain relevant, offering practical insights into personal growth and emotional strength.', 'uploads/images/letter from a stoic by senecca.jpg', 6, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(60, 'Man’s Search for Meaning1', '<p>Man&rsquo;s Search for Meaning explores Viktor Frankl&rsquo;s experiences in Nazi concentration camps and his psychological insights on finding purpose. Combining existential philosophy with logotherapy, this book highlights resilience, hope, and the power of meaning in life&rsquo;s most difficult moments.</p>', 'uploads/images/Man’s Search for Meaning by victor E. frankal.jpg', 6, 1, '2025-07-29 16:08:10', '2025-07-29 21:50:03'),
(61, 'Meditations', 'Meditations is Marcus Aurelius’ personal reflections on Stoicism, wisdom, and leadership. Written as a guide for self-improvement, it offers profound insights on resilience, virtue, and mindfulness. His thoughts remain a cornerstone of Stoic philosophy and personal development.', 'uploads/images/Meditations by maecus aurelius.jpg', 6, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(62, 'Tao Te Ching', 'Tao Te Ching is a foundational text of Taoist philosophy, emphasizing harmony, balance, and simplicity. Lao Tzu’s poetic verses explore the nature of existence, wisdom, and leadership. This timeless classic offers profound spiritual guidance and a path to inner peace.', 'uploads/images/Tao Te Ching by lao tzu.jpg', 6, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(63, 'The Art of War', 'The Art of War is an ancient Chinese military treatise by Sun Tzu. It provides strategies on warfare, leadership, and strategic thinking, applicable beyond battlefields to business, politics, and life. This timeless guide offers wisdom on victory, discipline, and adaptability.', 'uploads/images/The Art of War by sun tzu.jpg', 6, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(64, 'The Nicomachean Ethics', 'The Nicomachean Ethics explores Aristotle’s thoughts on virtue, happiness, and moral philosophy. He examines human purpose, ethical living, and the pursuit of the good life. This foundational text remains essential for understanding classical philosophy and ethical reasoning.', 'uploads/images/The Nicomachean Ethics by aristotle.jpg', 6, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(65, 'The Prince', 'The Prince is a political treatise on power, leadership, and strategy. Niccolò Machiavelli’s work explores pragmatic governance, manipulation, and the realities of ruling. His insights on ambition, deception, and control continue to influence politics and leadership strategies.', 'uploads/images/the prince by Niccolò Machiavelli.jpg', 6, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(66, 'The Republic', 'The Republic is Plato’s philosophical dialogue on justice, politics, and the ideal state. He explores the nature of governance, morality, and education, introducing the concept of philosopher-kings. This classic work continues to shape political philosophy and discussions on justice.', 'uploads/images/The Republic by plato.jpg', 6, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10'),
(67, 'The Stranger', 'The Stranger is a masterpiece of existentialist philosophy, exploring absurdity, fate, and alienation. Albert Camus’ novel follows Meursault, a detached protagonist confronting life’s meaninglessness. This thought-provoking work challenges conventional morality and explores human indifference.', 'uploads/images/The Stranger by albert camus.jpg', 6, 1, '2025-07-29 16:08:10', '2025-07-29 16:08:10');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','editor','visitor') NOT NULL DEFAULT 'editor',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$wNzLG5m0l04Zoxpldj2jfeoj/MMMNQOLZ.qhMS2hAaBPFEBoGx5VW', 'admin', '2025-07-29 14:05:24'),
(3, 'kkk', '$2y$10$gYAuMgt1tSRpgPxYPhGt/uwHDknjR//Ru80YQz59FdZSkvQTYRnkO', 'editor', '2025-07-30 01:20:03'),
(4, 'yao', '$2y$10$TwL4NJ41LicvcSeTf8qVqOg6QDhMYQx2Xdfrh/5yiDsHn/Cj1L1Au', 'editor', '2025-07-30 01:20:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `review_id` (`review_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`review_id`) REFERENCES `reviews` (`id`);

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
