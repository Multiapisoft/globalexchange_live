-- Runs only on FIRST MySQL volume init.
-- hiuser is also created via MYSQL_USER / MYSQL_PASSWORD in compose.
-- Add more CREATE DATABASE lines when you onboard new projects.

CREATE DATABASE IF NOT EXISTS `globalexchange_live`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

-- Grant shared app user on this DB (user may already exist from compose env)
GRANT ALL PRIVILEGES ON `globalexchange_live`.* TO 'hiuser'@'%';

-- Example for next projects (uncomment / copy as needed):
-- CREATE DATABASE IF NOT EXISTS `other_project_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- GRANT ALL PRIVILEGES ON `other_project_db`.* TO 'hiuser'@'%';

FLUSH PRIVILEGES;
