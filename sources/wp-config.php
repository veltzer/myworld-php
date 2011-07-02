<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
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
define('DB_USER', 'mark');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

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
define('AUTH_KEY',         'X:3LlH<GFQ*)V-H;FJz)|v~dHy;%s7i KT(E^joA*eE_dI0hKZ721Gz~YB/8svQM');
define('SECURE_AUTH_KEY',  'c,rQ,:Bv8(B@?d$ gw,60C<*L~|n/Cwo7+}atNY,,d#T|c_b}sahS2;WC(.K>FZU');
define('LOGGED_IN_KEY',    '-t~GP_cOsFVt*]& m6rS)-;if1c?]s$heG+S=&~}s_kj4);vL)oMt--QosTy*pHT');
define('NONCE_KEY',        'gWPMlG:.<&Dk+L19CPM:<#,4cN-Sxr&$GFsWU| eo:+~D;;j5Wza>;_x}jbWsM+e');
define('AUTH_SALT',        '0ULsWt*{},I{8pYX|A`s5s+jl#.w:a0mjphX2;@|k?b|*7iFK2I$Ij-OHqD&`.{G');
define('SECURE_AUTH_SALT', '>E+|],a.,uU+O-s9^%pll-c7jowrB @G[ L&](Wr_0xPKOL0w}y`&5BUxB=_=9/2');
define('LOGGED_IN_SALT',   '/=-+>#K?YTxH@T*`M+zG}W7F?@.+t,vegU8Y>R&`3E4lon)cE-h%7A|1u?&s.5Q|');
define('NONCE_SALT',       '.)4t_{KR_R&oQZ@iDE7=bYj%JC`MvS+L5TXV,pE=,RZ;-/)npHf#Xw6s-F-kv=A5');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

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
