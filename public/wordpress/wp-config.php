<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link https://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wordpress');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

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
define('AUTH_KEY',         '06+RJ>#_3i5RUE]WGZ(&yIA4#{qA`wx8ff++-RpNbYPY;(>2tQ*.Fv7iG{O7SVz~');
define('SECURE_AUTH_KEY',  'H(VP7F{p!z;.|NV5_sw_%-:i+(q A4nYUZRw-E6*HIa$v,2dGD+A!{82Um<-Ba,M');
define('LOGGED_IN_KEY',    'G%if%5D*+]7ACl_~AERHmy88A^,$pO!?.^0|`>J,mBJ|{IpN?zqKC$|~N@#kvmX`');
define('NONCE_KEY',        'kMo8/9j;(c!-1no[bk+alMzF%lL6*/{W4u|)r Pca`7pz?4m&x$/HgX}*TXhfT:7');
define('AUTH_SALT',        'if`3|,;-9@nn6__Fa{/Q#LWa@)3s{o~kQ?,1%!E8$h -ZL;G +R`eT8Se$&z)YD?');
define('SECURE_AUTH_SALT', '|3lMY|%+]n~2Z32YQ>%,Fqge1H[7VLqlTw@|#9^:5^wg%rKKkN+%fz{vE@U)Yu|y');
define('LOGGED_IN_SALT',   'ok~_k(28?nTpllIOMd%PCFE8E^hKm1{N8z=)Crhp@f!a38s-NDWnQ|&zRU.#n-w7');
define('NONCE_SALT',       '$n^.gP(U:4~?/N&q`OCF A9a=y!=&%s`s6_z}+F(o95EV9NmW)#67qP2oF%4G2xJ');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
