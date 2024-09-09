
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `product` (
  `id` int NOT NULL,
  `name` varchar(60) NOT NULL,
  `quantity` int NOT NULL,
  `description` text NOT NULL,
  `img` varchar(255) NOT NULL,
  `price` int NOT NULL,
  `tags` text NOT NULL,
  `popularity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(60) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'admin', 'mates.svarc@gmail.com', '$2y$10$9tnQjDBDYShUa2MtlTtGueRgXb8BOZ8GMO3KdMq1.aA7vkNuNn9j6', 'admin', '2024-07-10 17:45:20'),
(5, 'pepik', 'pepik@gmail.com', '$2y$10$lxWxJN9i6RPvl/PdjtacbOWLNqttCV9zFG.YHC4bZa5FIohnY2/8G', 'uzivatel', '2024-09-09 14:38:08');


ALTER TABLE `product`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
ALTER TABLE `product`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;
