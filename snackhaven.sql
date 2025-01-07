-- MySQL dump 10.13  Distrib 8.0.40, for Win64 (x86_64)
--
-- Host: snackhaven-zaikaman123-008e.b.aivencloud.com    Database: defaultdb
-- ------------------------------------------------------
-- Server version	8.0.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
SET @MYSQLDUMP_TEMP_LOG_BIN = @@SESSION.SQL_LOG_BIN;
SET @@SESSION.SQL_LOG_BIN= 0;

--
-- GTID state at the beginning of the backup 
--

SET @@GLOBAL.GTID_PURGED=/*!80000 '+'*/ '24510cd7-cc3d-11ef-b45a-0e072434234f:1-116';

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `permissions` text,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `admins_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `image_url` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Hamburger','https://images.unsplash.com/photo-1568901346375-23c9450c58cd','2025-01-06 15:01:43','2025-01-06 15:01:43'),(2,'Pizza','https://images.unsplash.com/photo-1513104890138-7c749659a591','2025-01-06 15:01:43','2025-01-06 15:01:43'),(3,'Đồ uống','https://images.unsplash.com/photo-1544145945-f90425340c7e','2025-01-06 15:01:43','2025-01-06 15:01:43'),(4,'Combo','https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5','2025-01-06 15:01:43','2025-01-06 15:01:43');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_messages`
--

DROP TABLE IF EXISTS `contact_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact_messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('new','read','replied') NOT NULL DEFAULT 'new',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_messages`
--

LOCK TABLES `contact_messages` WRITE;
/*!40000 ALTER TABLE `contact_messages` DISABLE KEYS */;
INSERT INTO `contact_messages` VALUES (1,'Thinh Dinh','zaikaman123@gmail.com','0931816175','Hi','hi','2025-01-06 17:21:32','new'),(2,'Thinh Dinh','zaikaman123@gmail.com','0931816175','Hi','asdasd','2025-01-07 06:03:02','new'),(3,'Thinh Dinh','zaikaman123@gmail.com','0931816175','123','123','2025-01-07 06:07:10','new');
/*!40000 ALTER TABLE `contact_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_verifications`
--

DROP TABLE IF EXISTS `email_verifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_verifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `token` varchar(255) NOT NULL,
  `type` enum('registration','password_reset') NOT NULL,
  `expires_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `used` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `token` (`token`),
  KEY `expires_at` (`expires_at`),
  CONSTRAINT `email_verifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_verifications`
--

LOCK TABLES `email_verifications` WRITE;
/*!40000 ALTER TABLE `email_verifications` DISABLE KEYS */;
INSERT INTO `email_verifications` VALUES (10,10,'b0f1ec935be4d397e04a4b7f37788e204c4480976f88ee4932c4715d4593d270','registration','2025-01-07 17:05:55','2025-01-06 17:05:56',1);
/*!40000 ALTER TABLE `email_verifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','processed') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'Classic Burger','Burger bò truyền thống với phô mai, rau xà lách và cà chua',79000.00,'https://images.unsplash.com/photo-1568901346375-23c9450c58cd',1,'2025-01-07 02:38:17'),(2,'Cheese Burger','Burger với 2 lớp phô mai Cheddar béo ngậy',89000.00,'https://images.unsplash.com/photo-1550317138-10000687a72b',1,'2025-01-07 02:38:17'),(3,'Bacon Burger','Burger bò với thịt xông khói giòn tan',99000.00,'https://images.unsplash.com/photo-1553979459-d2229ba7433b',1,'2025-01-07 02:38:17'),(4,'BBQ Burger','Burger với sốt BBQ đặc trưng',95000.00,'https://images.unsplash.com/photo-1572802419224-296b0aeee0d9',1,'2025-01-07 02:38:17'),(5,'Mushroom Burger','Burger với nấm nướng và sốt nấm',89000.00,'https://i.ibb.co/W0NrrMF/mushroom-burger.jpg',1,'2025-01-07 02:38:17'),(6,'Double Beef Burger','Burger với 2 lớp thịt bò đặc biệt',119000.00,'https://i.ibb.co/D1ZssYg/double-beef-burger.jpg',1,'2025-01-07 02:38:17'),(7,'Chicken Burger','Burger gà giòn với sốt mayo',85000.00,'https://i.ibb.co/xHfYyKx/chicken-burger.jpg',1,'2025-01-07 02:38:17'),(8,'Spicy Burger','Burger cay với ớt jalapeño',89000.00,'https://i.ibb.co/zP8YgtC/spicy-burger.jpg',1,'2025-01-07 02:38:17'),(9,'Fish Burger','Burger cá hồi với sốt tartar',95000.00,'https://i.ibb.co/nzwvCx3/fish-burger.jpg',1,'2025-01-07 02:38:17'),(10,'Veggie Burger','Burger chay với patty rau củ',79000.00,'https://images.unsplash.com/photo-1585238342024-78d387f4a707',1,'2025-01-07 02:38:17'),(11,'Hawaiian Burger','Burger với dứa nướng và sốt teriyaki',99000.00,'https://images.unsplash.com/photo-1550317138-10000687a72b',1,'2025-01-07 02:38:17'),(12,'Mexican Burger','Burger phong cách Mexico với bơ và salsa',99000.00,'https://images.unsplash.com/photo-1551615593-ef5fe247e8f7',1,'2025-01-07 02:38:17'),(13,'Supreme Burger','Burger đặc biệt với đầy đủ topping',129000.00,'https://images.unsplash.com/photo-1568901346375-23c9450c58cd',1,'2025-01-07 02:38:17'),(14,'Blue Cheese Burger','Burger với phô mai xanh thơm nồng',109000.00,'https://i.ibb.co/7KRGF3s/blue-cheese-burger.jpg',1,'2025-01-07 02:38:17'),(15,'Crispy Onion Burger','Burger với hành tây giòn',89000.00,'https://images.unsplash.com/photo-1572802419224-296b0aeee0d9',1,'2025-01-07 02:38:17'),(16,'Truffle Burger','Burger với sốt nấm truffle',149000.00,'https://i.ibb.co/6wTBLXc/truffle-burger.png',1,'2025-01-07 02:38:17'),(17,'Ranch Burger','Burger với sốt ranch và thịt xông khói',99000.00,'https://images.unsplash.com/photo-1553979459-d2229ba7433b',1,'2025-01-07 02:38:17'),(18,'Egg Burger','Burger với trứng ốp la',89000.00,'https://images.unsplash.com/photo-1586190848861-99aa4a171e90',1,'2025-01-07 02:38:17'),(19,'Teriyaki Burger','Burger với sốt teriyaki Nhật Bản',99000.00,'https://images.unsplash.com/photo-1550317138-10000687a72b',1,'2025-01-07 02:38:17'),(20,'Monster Burger','Burger siêu to với 3 lớp thịt',159000.00,'https://images.unsplash.com/photo-1568901346375-23c9450c58cd',1,'2025-01-07 02:38:17'),(21,'Margherita Pizza','Pizza truyền thống với sốt cà chua và phô mai Mozzarella',129000.00,'https://images.unsplash.com/photo-1513104890138-7c749659a591',2,'2025-01-07 02:38:17'),(22,'Pepperoni Pizza','Pizza với xúc xích pepperoni',149000.00,'https://images.unsplash.com/photo-1534308983496-4fabb1a015ee',2,'2025-01-07 02:38:17'),(23,'Hawaiian Pizza','Pizza với dứa và thịt nguội',139000.00,'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38',2,'2025-01-07 02:38:17'),(24,'Seafood Pizza','Pizza hải sản với tôm và mực',169000.00,'https://images.unsplash.com/photo-1544982503-9f984c14501a',2,'2025-01-07 02:38:17'),(25,'Vegetarian Pizza','Pizza chay với nhiều loại rau củ',139000.00,'https://images.unsplash.com/photo-1590947132387-155cc02f3212',2,'2025-01-07 02:38:17'),(26,'BBQ Chicken Pizza','Pizza gà với sốt BBQ',159000.00,'https://images.unsplash.com/photo-1513104890138-7c749659a591',2,'2025-01-07 02:38:17'),(27,'Four Cheese Pizza','Pizza với 4 loại phô mai',169000.00,'https://images.unsplash.com/photo-1534308983496-4fabb1a015ee',2,'2025-01-07 02:38:17'),(28,'Meat Lovers Pizza','Pizza với nhiều loại thịt',179000.00,'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38',2,'2025-01-07 02:38:17'),(29,'Mushroom Pizza','Pizza với các loại nấm',149000.00,'https://images.unsplash.com/photo-1544982503-9f984c14501a',2,'2025-01-07 02:38:17'),(30,'Supreme Pizza','Pizza với đầy đủ topping',189000.00,'https://i.ibb.co/cx9Q0rr/supreme-pizza.jpg',2,'2025-01-07 02:38:17'),(31,'Buffalo Pizza','Pizza với gà sốt buffalo cay',159000.00,'https://i.ibb.co/MMN3f5M/buffalo-pizza.jpg',2,'2025-01-07 02:38:17'),(32,'Pesto Pizza','Pizza với sốt pesto và gà',159000.00,'https://i.ibb.co/JyxpH89/pesto-pizza.jpg',2,'2025-01-07 02:38:17'),(33,'Anchovy Pizza','Pizza với cá cơm và ô liu',149000.00,'https://i.ibb.co/tHc3152/anchovy-pizza.jpg',2,'2025-01-07 02:38:17'),(34,'Spinach Pizza','Pizza với rau chân vịt và phô mai',139000.00,'https://i.ibb.co/sq9K8Yw/spinach-pizza.jpg',2,'2025-01-07 02:38:17'),(35,'Garlic Pizza','Pizza với tỏi và phô mai',139000.00,'https://i.ibb.co/M9pcp6F/garlic-pizza.jpg',2,'2025-01-07 02:38:17'),(36,'Truffle Pizza','Pizza với nấm truffle',199000.00,'https://i.ibb.co/VDHNTGS/truffle-pizza.jpg',2,'2025-01-07 02:38:17'),(37,'Mexican Pizza','Pizza phong cách Mexico cay',159000.00,'https://i.ibb.co/cTZ6Dsg/mexico-pizza.jpg',2,'2025-01-07 02:38:17'),(38,'Greek Pizza','Pizza phong cách Hy Lạp',159000.00,'https://i.ibb.co/429mCmv/greek-pizza.jpg',2,'2025-01-07 02:38:17'),(39,'Carbonara Pizza','Pizza với sốt kem và thịt xông khói',169000.00,'https://i.ibb.co/q9tm50n/carbonara-pizza.jpg',2,'2025-01-07 02:38:17'),(40,'Calzone Pizza','Pizza gấp đôi với nhân đặc biệt',179000.00,'https://i.ibb.co/0XZsCVG/calzone-pizza.jpg',2,'2025-01-07 02:38:17'),(41,'Coca Cola','Nước ngọt có ga Coca Cola',25000.00,'https://i.ibb.co/NSfn0xW/coca.png',3,'2025-01-07 02:38:17'),(42,'Pepsi','Nước ngọt có ga Pepsi',25000.00,'https://images.unsplash.com/photo-1553456558-aff63285bdd1',3,'2025-01-07 02:38:17'),(43,'Fanta','Nước ngọt có ga hương cam Fanta',25000.00,'https://images.unsplash.com/photo-1625772299848-391b6a87d7b3',3,'2025-01-07 02:38:17'),(44,'Sprite','Nước ngọt có ga vị chanh Sprite',25000.00,'https://images.unsplash.com/photo-1625772299848-391b6a87d7b3',3,'2025-01-07 02:38:17'),(45,'Trà đào','Trà đào tươi mát',35000.00,'https://i.ibb.co/mhCnZ4D/tra-dao.jpg',3,'2025-01-07 02:38:17'),(46,'Trà vải','Trà vải tươi ngọt',35000.00,'https://i.ibb.co/wW5GC3b/tra-vai.jpg',3,'2025-01-07 02:38:17'),(47,'Sinh tố xoài','Sinh tố xoài tươi',45000.00,'https://images.unsplash.com/photo-1546173159-315724a31696',3,'2025-01-07 02:38:17'),(48,'Sinh tố dâu','Sinh tố dâu tây',45000.00,'https://i.ibb.co/Sv45dzb/cach-lam-sinh-to-dau-xay-chanh-mon-trang-mieng-519364532718.jpg',3,'2025-01-07 02:38:17'),(49,'Nước ép cam','Nước ép cam tươi',39000.00,'https://images.unsplash.com/photo-1613478223719-2ab802602423',3,'2025-01-07 02:38:17'),(50,'Nước ép táo','Nước ép táo tươi',39000.00,'https://i.ibb.co/tpCPzt6/Apple-juice1.jpg',3,'2025-01-07 02:38:17'),(51,'Cà phê đen','Cà phê đen truyền thống',29000.00,'https://images.unsplash.com/photo-1521302080334-4bebac2763a6',3,'2025-01-07 02:38:17'),(52,'Cà phê sữa','Cà phê sữa đá',35000.00,'https://i.ibb.co/DWF0VB1/ca-phe-sua-hat-1.jpg',3,'2025-01-07 02:38:17'),(53,'Trà sữa trân châu','Trà sữa với trân châu đen',45000.00,'https://i.ibb.co/VgYq8Nc/trasuatranchau.jpg',3,'2025-01-07 02:38:17'),(54,'Trà sữa matcha','Trà sữa vị matcha Nhật Bản',45000.00,'https://i.ibb.co/GkQTkYT/trasuamatcha.webp',3,'2025-01-07 02:38:17'),(55,'Smoothie dừa','Smoothie dừa béo ngậy',49000.00,'https://i.ibb.co/RSBQ8Yv/smoothiedua.jpg',3,'2025-01-07 02:38:17'),(56,'Sữa chua đá','Sữa chua đá mát lạnh',39000.00,'https://i.ibb.co/HDD57cW/suachuada.jpg',3,'2025-01-07 02:38:17'),(57,'Nước chanh','Nước chanh tươi',25000.00,'https://i.ibb.co/zQBCyZy/nuocchanh.jpg',3,'2025-01-07 02:38:17'),(58,'Trà gừng','Trà gừng nóng',35000.00,'https://i.ibb.co/W0Csjyy/tragung.jpg',3,'2025-01-07 02:38:17'),(59,'Soda chanh','Soda chanh mát lạnh',35000.00,'https://i.ibb.co/m9Kz5Zc/soda-chanh.jpg',3,'2025-01-07 02:38:17'),(60,'Mojito','Mojito không cồn',49000.00,'https://images.unsplash.com/photo-1544145945-f90425340c7e',3,'2025-01-07 02:38:17'),(61,'Combo Burger Family','Combo 4 burger + 4 nước ngọt + 2 khoai tây chiên',399000.00,'https://i.ibb.co/NTRsgzF/Screenshot-20170505-172503-e1493969237190.png',4,'2025-01-07 02:38:17'),(62,'Combo Pizza Party','Combo 2 pizza cỡ lớn + 4 nước ngọt',449000.00,'https://i.ibb.co/HhyFcyg/specials.png',4,'2025-01-07 02:38:17'),(63,'Combo Couple','2 burger + 2 nước + 1 khoai tây chiên',199000.00,'https://i.ibb.co/WVB72qt/combo3.jpg',4,'2025-01-07 02:38:17'),(64,'Combo Single','1 burger + 1 nước + 1 khoai tây chiên',119000.00,'https://i.ibb.co/br2d6gh/meal-chicken.png',4,'2025-01-07 02:38:17'),(65,'Combo Kids','1 burger nhỏ + 1 nước ngọt + 1 đồ chơi',149000.00,'https://i.ibb.co/br2d6gh/meal-chicken.png',4,'2025-01-07 02:38:17'),(66,'Combo Pizza Box','1 pizza cỡ vừa + 2 nước ngọt',219000.00,'https://i.ibb.co/7KQLptb/menupics-deals-placeholder.jpg',4,'2025-01-07 02:38:17'),(67,'Combo Super Size','2 burger lớn + 2 nước lớn + 2 khoai tây chiên',259000.00,'https://i.ibb.co/vzbhWSL/product-Value-Bundles-2-mobile-20240201.png',4,'2025-01-07 02:38:17'),(68,'Combo Student','1 burger + 1 nước + 1 kem',129000.00,'https://i.ibb.co/1b0yX6S/third-pound-double-cheese-deal.png',4,'2025-01-07 02:38:17'),(69,'Combo Birthday','2 pizza lớn + 6 nước + 1 bánh kem',599000.00,'https://images.unsplash.com/photo-1513104890138-7c749659a591',4,'2025-01-07 02:38:17'),(70,'Combo Team','3 pizza cỡ vừa + 6 nước',499000.00,'https://images.unsplash.com/photo-1513104890138-7c749659a591',4,'2025-01-07 02:38:17'),(71,'Combo Weekend','2 burger + 2 pizza nhỏ + 4 nước',399000.00,'https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5',4,'2025-01-07 02:38:17'),(72,'Combo Movie Night','1 pizza lớn + 4 nước + 2 khoai tây chiên',299000.00,'https://images.unsplash.com/photo-1513104890138-7c749659a591',4,'2025-01-07 02:38:17'),(73,'Combo Breakfast','2 burger + 2 cà phê',179000.00,'https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5',4,'2025-01-07 02:38:17'),(74,'Combo Lunch','1 burger + 1 pizza nhỏ + 2 nước',219000.00,'https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5',4,'2025-01-07 02:38:17'),(75,'Combo Dinner','2 burger + 1 pizza vừa + 4 nước',359000.00,'https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5',4,'2025-01-07 02:38:17'),(76,'Combo Party Plus','3 pizza lớn + 8 nước + 4 khoai tây chiên',799000.00,'https://images.unsplash.com/photo-1513104890138-7c749659a591',4,'2025-01-07 02:38:17'),(77,'Combo Value','1 burger + 1 pizza nhỏ + 1 nước',169000.00,'https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5',4,'2025-01-07 02:38:17'),(78,'Combo Premium','2 burger premium + 2 nước + 2 dessert',299000.00,'https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5',4,'2025-01-07 02:38:17'),(79,'Combo Business','4 burger + 2 pizza nhỏ + 6 nước',499000.00,'https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5',4,'2025-01-07 02:38:17'),(80,'Combo Mega','3 pizza lớn + 3 burger + 8 nước + 4 khoai tây chiên',899000.00,'https://images.unsplash.com/photo-1513104890138-7c749659a591',4,'2025-01-07 02:38:17');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reviews` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `rating` int DEFAULT NULL,
  `review` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_chk_1` CHECK ((`rating` between 1 and 5))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `address` text,
  `role` enum('customer','admin') DEFAULT 'customer',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `verified` tinyint(1) DEFAULT '0',
  `verification_token` varchar(255) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expires` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (10,'zaikaman','$2y$12$C9z4sKlcewv0HFWWvFZAO.Q2QMOnicbyoAApfREhtfikxakaBah8S','zaikaman123@gmail.com',NULL,NULL,NULL,NULL,'customer','2025-01-06 17:05:56',1,NULL,NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
SET @@SESSION.SQL_LOG_BIN = @MYSQLDUMP_TEMP_LOG_BIN;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-01-07 13:47:49
