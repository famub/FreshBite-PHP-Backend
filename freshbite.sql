-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 09, 2026 at 07:26 8
-- Server version: 5.7.24
-- PHP Version: 8.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `freshbite`
--

-- --------------------------------------------------------

--
-- Table structure for table `blockeduser`
--

CREATE TABLE `blockeduser` (
  `id` int(11) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `emailAddress` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `blockeduser`
--

INSERT INTO `blockeduser` (`id`, `firstName`, `lastName`, `emailAddress`) VALUES
(1, 'layla', 'Hassan', 'layla@gmail.com'),
(2, 'Omar', 'Saleh', 'omar@gmail.com'),
(3, 'Ahmed', 'Fahad', 'ahmed@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE `comment` (
  `id` int(11) NOT NULL,
  `recipeID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `comment` text NOT NULL,
  `date` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `comment`
--
 
INSERT INTO `comment` (`id`, `recipeID`, `userID`, `comment`, `date`) VALUES
(1, 1, 4, 'I like how simple yet satisfying this bowl is. The chicken looks perfectly cooked.', '2026-03-09 04:37:25'),
(2, 1, 5, 'Loved the mediterranean touch, especially with the fresh veggies.', '2026-03-09 04:37:25'),
(3, 2, 5, 'So fresh! I really like how everything comes together perfectly. Great choice for a healthy meal.', '2026-03-09 04:37:25'),
(4, 3, 3, 'Loved the balance between the creamy dressing and the crunchy veggies. Definitely making this again.', '2026-03-09 04:37:25'),
(5, 4, 4, 'Tried this for lunch and it was so refreshing! Light but still filling, definitely making it again', '2026-03-09 04:37:25'),
(6, 4, 3, 'Perfect quick meal for busy days. Healthy, tasty, and doesn’t feel boring at all.', '2026-03-09 04:37:25');

-- --------------------------------------------------------

--
-- Table structure for table `favourites`
--

CREATE TABLE `favourites` (
  `userID` int(11) NOT NULL,
  `recipeID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `favourites`
--

INSERT INTO `favourites` (`userID`, `recipeID`) VALUES
(4, 1),
(4, 2),
(5, 2),
(3, 4);

-- --------------------------------------------------------

--
-- Table structure for table `ingredients`
--

CREATE TABLE `ingredients` (
  `id` int(11) NOT NULL,
  `recipeID` int(11) NOT NULL,
  `ingredientName` varchar(100) NOT NULL,
  `ingredientQuantity` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ingredients`
--

INSERT INTO `ingredients` (`id`, `recipeID`, `ingredientName`, `ingredientQuantity`) VALUES
(1, 1, 'Mixed greens (spinach and romaine)', '2 cups'),
(2, 1, 'Grilled chicken breast', '1 piece'),
(3, 1, 'Cherry tomatoes', '1/2 cup'),
(4, 1, 'English cucumber', '1/2 cup'),
(5, 1, 'Red onion', '1/4 piece'),
(6, 1, 'Kalamata olives', '1/3 cup'),
(7, 1, 'Feta cheese, crumbled', '1/4 cup'),
(8, 1, 'Dried oregano', '1 tsp'),
(9, 1, 'Olive oil', 'to taste'),
(10, 1, 'Lemon juice', 'to taste'),
(11, 1, 'Salt and pepper', 'to taste'),
(12, 1, 'Pita bread, warm', '2 triangles'),
(13, 2, 'Mixed leafy greens (spinach, arugula, or kale)', '2 cups'),
(14, 2, 'Cooked quinoa (white or tricolor)', '1/2 cup'),
(15, 2, 'Sweet potato, peeled, cubed, and roasted', '1 medium'),
(16, 2, 'Chickpeas (garbanzo beans), rinsed', '1/2 cup'),
(17, 2, 'Avocado, sliced', '1/2 piece'),
(18, 2, 'Pomegranate seeds', '1/4 cup'),
(19, 2, 'Pumpkin seeds (pepitas)', '1 tbsp'),
(20, 2, 'Feta or Goat cheese, crumbled (optional)', '2 tbsp'),
(21, 3, 'Crab sticks', '500g'),
(22, 3, 'Fresh cucumber', '200g'),
(23, 3, 'Carrot', '150g'),
(24, 3, 'Canned corn', '100g'),
(25, 3, 'Mayonnaise', '1/2 cup'),
(26, 3, 'Sesame oil', '1 tbsp'),
(27, 3, 'Lemon juice', '1 tbsp'),
(28, 3, 'Ground black pepper', '1/2 tsp'),
(29, 3, 'Sesame seeds', '1 tbsp'),
(30, 4, 'Tuna, drained', '1 can'),
(31, 4, 'Chickpeas, rinsed', '1/2 cup'),
(32, 4, 'Cucumber, diced', '1 medium'),
(33, 4, 'Bell pepper, chopped', '1/2 piece'),
(34, 4, 'Red onion, finely chopped', '1/4 piece'),
(35, 4, 'Sun-dried tomatoes, chopped', '2-3 tbsp'),
(36, 4, 'Olives, sliced', '2 tbsp'),
(37, 4, 'Fresh parsley, chopped', '2 tbsp'),
(38, 4, 'Garlic powder', '1/4 tsp'),
(39, 4, 'Dried oregano', '1/2 tsp'),
(40, 4, 'Salt (or to taste)', '1/4 tsp'),
(41, 4, 'Olive oil', '2 tbsp');

-- --------------------------------------------------------

--
-- Table structure for table `instructions`
--

CREATE TABLE `instructions` (
  `id` int(11) NOT NULL,
  `recipeID` int(11) NOT NULL,
  `step` text NOT NULL,
  `stepOrder` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `instructions`
--

INSERT INTO `instructions` (`id`, `recipeID`, `step`, `stepOrder`) VALUES
(1, 1, 'Season the chicken breast with salt, pepper, and oregano. Grill over medium heat for 6-7 minutes per side until golden brown. Let it rest, then slice.', 1),
(2, 1, 'Wash all vegetables. Slice the cucumbers into rounds, halve the tomatoes, and slice the onions into thin rings.', 2),
(3, 1, 'Place the mixed greens at the bottom of the bowl. Arrange the veggies and chicken in distinct sections on top.', 3),
(4, 1, 'Sprinkle the feta cheese and olives over the center.', 4),
(5, 1, 'Drizzle with olive oil and lemon juice just before eating. Serve with warm pita bread on the side.', 5),
(6, 2, 'Toss the sweet potato cubes in olive oil, salt, and pepper. Roast at 200°C (400°F) for 20-25 minutes until tender.', 1),
(7, 2, 'Place the washed mixed greens at the bottom of the bowl as a base.', 2),
(8, 2, 'Arrange the cooked quinoa and roasted sweet potatoes on one side.', 3),
(9, 2, 'Place the chickpeas, avocado slices, and pomegranate seeds in sections next to each other.', 4),
(10, 2, 'Sprinkle the pumpkin seeds and crumbled cheese over the top.', 5),
(11, 2, 'Whisk the tahini dressing ingredients together until smooth and drizzle over the bowl.', 6),
(12, 3, 'Unfold the crab sticks and cut the sheets into thin strips.', 1),
(13, 3, 'Cut the cucumber lengthwise and remove the seeds with a spoon, grate or cut into thin strips.', 2),
(14, 3, 'Grate or cut the carrot into thin strips.', 3),
(15, 3, 'Add the mayonnaise, sesame oil, lemon juice and ground black pepper into a bowl and combine until smooth.', 4),
(16, 3, 'Into a deep bowl lay out the crab sticks, carrot, cucumber and corn, add the sauce and toss neatly.', 5),
(17, 3, 'Sprinkle the crab sticks kani salad with sesame seeds and serve to the table.', 6),
(18, 4, 'Drain the tuna well and place it in a large bowl.', 1),
(19, 4, 'Add the chickpeas, cucumber, pepper, red onion, sun-dried tomatoes, olives, and parsley.', 2),
(20, 4, 'Season with garlic powder, oregano, and salt.', 3),
(21, 4, 'Drizzle olive oil over the salad.', 4),
(22, 4, 'Mix everything thoroughly until all ingredients are evenly combined.', 5),
(23, 4, 'Taste and adjust seasoning if needed.', 6),
(24, 4, 'Serve immediately, or refrigerate for about 30 minutes to allow the flavors to meld together.', 7);

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `userID` int(11) NOT NULL,
  `recipeID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`userID`, `recipeID`) VALUES
(4, 1),
(5, 1),
(4, 2),
(3, 3),
(3, 4);

-- --------------------------------------------------------

--
-- Table structure for table `recipe`
--

CREATE TABLE `recipe` (
  `id` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `categoryID` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `recipePhoto` varchar(255) NOT NULL,
  `videoFilePath` varchar(255) DEFAULT 'no video for recipe'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `recipe`
--

INSERT INTO `recipe` (`id`, `userID`, `categoryID`, `name`, `description`, `recipePhoto`, `videoFilePath`) VALUES
(1, 3, 2, 'Mediterranean Grilled Chicken Bowl', 'A balanced Mediterranean bowl made with grilled chicken breast, fresh mixed greens, cucumbers, tomatoes, olives, and feta cheese. Lightly seasoned and finished with olive oil and lemon juice for a healthy, protein-rich meal.', 'noBackground-Grilled-Chicken.png', 'https://youtube.com/shorts/FBp-XB6rSO8?si=tuvsZnvep4gYsZso'),
(2, 3, 3, 'Sweet Potato & Quinoa Power Bowl', 'A nutritious vegetarian power bowl featuring roasted sweet potatoes, quinoa, chickpeas, avocado, and pomegranate seeds. Topped with a creamy tahini-lemon dressing, this bowl is rich in fiber, plant-based protein, and natural flavors.', 'Sweet-Potato-Quinoa.png', 'no video for recipe'),
(3, 4, 1, 'Japanese Crab Salad', 'A light and refreshing crab salad with a creamy, slightly tangy dressing. This dish combines crisp textures with a smooth sesame-flavored sauce, making it perfect as a quick appetizer, side dish, or a chilled meal on warm days. Easy to prepare and great for gatherings or everyday meals.', 'crabSalad.webp', 'https://youtu.be/5QBGJSNNpoU?si=Psudu3OCIYgSk3NB'),
(4, 5, 1, 'Mediterranean Tuna Salad', 'A light and refreshing Mediterranean-style tuna salad that combines fresh flavors with a simple, healthy preparation. Perfect for a quick meal, this salad is easy to make, satisfying, and ideal for anyone looking for a nutritious and delicious option.', 'TunaSalad.jpg', 'https://youtu.be/haqAfg4OI_M?si=OnP54TRbE5KuIQiz');

-- --------------------------------------------------------

--
-- Table structure for table `recipecategory`
--

CREATE TABLE `recipecategory` (
  `id` int(11) NOT NULL,
  `categoryName` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `recipecategory`
--

INSERT INTO `recipecategory` (`id`, `categoryName`) VALUES
(1, 'Seafood salad'),
(2, 'Meat salad'),
(3, 'Vegan salad');

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

CREATE TABLE `report` (
  `id` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `recipeID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `report`
--

INSERT INTO `report` (`id`, `userID`, `recipeID`) VALUES
(1, 3, 1),
(2, 5, 4),
(3, 4, 3);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(5) NOT NULL,
  `userType` enum('user','admin') NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `emailAddress` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `chefPhoto` varchar(255) DEFAULT 'avatar.webp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `userType`, `firstName`, `lastName`, `emailAddress`, `password`, `chefPhoto`) VALUES
(1, 'admin', 'khalid', 'ِAbdullah', 'khalid@email.com', '$2y$10$YcnsjwJZU0cHD.x9yx95n.MXPk2pBJ0a6iNt.Rv7PuOcMpHgrC1SG', NULL),
(2, 'admin', 'Maha', 'Saleh', 'maha@gmail.com', '$2y$10$ozWa2kg/oCn2WdSJzgVRw.sYkXSE8So.Q5rrNFZk72L6OYcLTecCS', NULL),
(3, 'user', 'Samar', 'Mohemed', 'samar@gmail.com', '$2y$10$prAKH.l.sVsLdUbPt/Z3t.KRMPfsS4FtxIG0SQpPI966fbhpY5LHu', 'chef.avif'),
(4, 'user', 'Gordan', 'Ramsay', 'Gordan@gmail.com', '$2y$10$fCGAoeKs.u9zy3InKcrao.8rYTV/zUAvuG9yZPDX5WC0AL/lpHD0y', 'gordan.jpeg'),
(5, 'user', 'Ina', 'Garten', 'Ina@gmail.com', '$2y$10$BcIDI.8elyw2pSMkd3Rjje2hcZUGjVhg7BuGrBm2gMjCOmPYnfQLi', 'ina-chef.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blockeduser`
--
ALTER TABLE `blockeduser`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `emailAddress` (`emailAddress`);

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipeID` (`recipeID`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `favourites`
--
ALTER TABLE `favourites`
  ADD PRIMARY KEY (`userID`,`recipeID`),
  ADD KEY `recipeID` (`recipeID`);

--
-- Indexes for table `ingredients`
--
ALTER TABLE `ingredients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipeID` (`recipeID`);

--
-- Indexes for table `instructions`
--
ALTER TABLE `instructions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipeID` (`recipeID`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`userID`,`recipeID`),
  ADD KEY `recipeID` (`recipeID`);

--
-- Indexes for table `recipe`
--
ALTER TABLE `recipe`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userID` (`userID`),
  ADD KEY `categoryID` (`categoryID`);

--
-- Indexes for table `recipecategory`
--
ALTER TABLE `recipecategory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userID` (`userID`),
  ADD KEY `recipeID` (`recipeID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `emailAddress` (`emailAddress`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blockeduser`
--
ALTER TABLE `blockeduser`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `comment`
--
ALTER TABLE `comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `instructions`
--
ALTER TABLE `instructions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `recipe`
--
ALTER TABLE `recipe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `recipecategory`
--
ALTER TABLE `recipecategory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `report`
--
ALTER TABLE `report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`recipeID`) REFERENCES `recipe` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comment_ibfk_2` FOREIGN KEY (`userID`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `favourites`
--
ALTER TABLE `favourites`
  ADD CONSTRAINT `favourites_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favourites_ibfk_2` FOREIGN KEY (`recipeID`) REFERENCES `recipe` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ingredients`
--
ALTER TABLE `ingredients`
  ADD CONSTRAINT `ingredients_ibfk_1` FOREIGN KEY (`recipeID`) REFERENCES `recipe` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `instructions`
--
ALTER TABLE `instructions`
  ADD CONSTRAINT `instructions_ibfk_1` FOREIGN KEY (`recipeID`) REFERENCES `recipe` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`recipeID`) REFERENCES `recipe` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `recipe`
--
ALTER TABLE `recipe`
  ADD CONSTRAINT `recipe_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `recipe_ibfk_2` FOREIGN KEY (`categoryID`) REFERENCES `recipecategory` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `report`
--
ALTER TABLE `report`
  ADD CONSTRAINT `report_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `report_ibfk_2` FOREIGN KEY (`recipeID`) REFERENCES `recipe` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
