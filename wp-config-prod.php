<?php
define('WP_CACHE', true); // WP-Optimize Cache
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */
// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
#define('WPCACHEHOME', '');
define('DB_NAME', 'wapro_wordpress');
/** MySQL database username */
define('DB_USER', 'wapro');
/** MySQL database password */
define('DB_PASSWORD', 'Ze4Cy3UOYE5w49AJ0SFqIeUgbzC0NqtR');
/** MySQL hostname */
define('DB_HOST', 'localhost');
/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');
/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');
/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY', 'G1lmlMcp1v+uUo0LjZ7YgdC2IYLEmXpq3kZse4bf09QS6SdXb3lyz/Ma6Y5dSl1c');
define('SECURE_AUTH_KEY', 'VTD5QAcKxmVbTuzLN2BRLZxZ4hlIN4uGA/UUfG4jVuLOjakag57J/fVrYdTofVgB');
define('LOGGED_IN_KEY', 'pJhIn7yrj8i1DxwLyj/09K4qvb5htg52TzTwD3AA+qhO79tYjDZDfqDaht7vcoT+');
define('NONCE_KEY', 'YcL4sxFgaJ4L0xAv9weaIPCMQ0jEyiPAEF8f/yJCLaRQEgYAp5gMPFLfn8GSwSGB');
define('AUTH_SALT', 'R+rTa/zggY2eWp3sXwpE6DP3AmjiRMKtEMevRUkavjNmx4s6D5DuuCA5RQJOylPG');
define('SECURE_AUTH_SALT', 'iKafbhXLJ3oJy54SRCiFskIvrSxj46a5nF+aTc/4+fOHwPIG8gRdoPffs90ypH4c');
define('LOGGED_IN_SALT', '/rwxFO9QZMglSlVECHHOVz5AhajG5mIcd6Kvpn5a/nptKA6REYLRRj4sZ+tEn2Mv');
define('NONCE_SALT', 'ZYHCS02UARoKB3TJkY7ioAQZVy61KKCYJzpGlcS/kdVGOj87YzzU8PDsOmjuffjg');
/**#@-*/
/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';
define( 'WPW3ALL_MAIN_DBPREFIX', $table_prefix );
/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
### AUTOUPDATE
### AUTOUPDATE
### DEBUG
define('WP_DEBUG', true); // Or false
if (WP_DEBUG) {
  define('WP_DEBUG_LOG', true);
  define('WP_DEBUG_DISPLAY', false);
}
### DEBUG
### WP_HTTP_BLOCK_EXTERNAL
define('WP_HTTP_BLOCK_EXTERNAL', false);
### WP_HTTP_BLOCK_EXTERNAL
//Disable File Edits
define('DISALLOW_FILE_EDIT', false);
//Disable Mod Security
define('FL_BUILDER_MODSEC_FIX', true);
//Allow uploads all type of files
define('ALLOW_UNFILTERED_UPLOADS', true);
### MULTISITE
define('WP_ALLOW_MULTISITE', true);
define('MULTISITE', true);
define('SUBDOMAIN_INSTALL', false);
define('DOMAIN_CURRENT_SITE', 'wapro.pl');
define('PATH_CURRENT_SITE', '/');
define('SITE_ID_CURRENT_SITE', 1);
define('BLOG_ID_CURRENT_SITE', 1);
define('SUNRISE', 'on');
### MULTISITE
/* That's all, stop editing! Happy blogging. */
/** Absolute path to the WordPress directory. */
if (!defined('ABSPATH'))
  define('ABSPATH', dirname(__FILE__) . '/');
/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');