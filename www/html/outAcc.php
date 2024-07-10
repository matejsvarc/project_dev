<?php
session_start(); // Zahájení session

session_unset(); // Odstranění všech session proměnných
session_destroy(); // Zničení session

header('Location: index.php'); // Přesměrování na index.php
exit(); // Ukončení skriptu pro jistotu