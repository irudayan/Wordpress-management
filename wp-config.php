<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'management' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'kUM*c`M=m.y { qq+x?depW@>b/ormd[32mtnl~ Pg{q)CLy/SM`md~[S%gjUNqi' );
define( 'SECURE_AUTH_KEY',  '|?(w  X)SZF6^H~y[o-]#~fG`e4Lx?iK.Jw$*:&f%%2bhVh/T+B4+ZM%1N9_#aeI' );
define( 'LOGGED_IN_KEY',    '3+3X)5W(BDOuQk%,+KM]#n~1.4}em{1xAV^`[@3V]yYgvZ!H*I?2?.Jq},$aHs~B' );
define( 'NONCE_KEY',        '!oEM~&%*e0< nsz?)e1 asplw3<Y|=*^gxq?$Dd*h-%w?XkRHS)*0eFB(P5sKRjq' );
define( 'AUTH_SALT',        '{s*365LJ%aK-Ztj6>:?77ZAe!:^tsu4{:gYxzDjgO)~vuNmRk,;Gvn&94K=ODQ&3' );
define( 'SECURE_AUTH_SALT', 'h J; [+}lePXv0r}Qo@m[xF)3+AioF_t+D_**bh=Rt[`RiS{{_ v:L7dwSK%j ;H' );
define( 'LOGGED_IN_SALT',   '?Fp#20}jl+F&m{Uj5:g7-c4L_?| ]!dnu7(xHV,G3<qg7l[&hSO`r!e}7r+z[Eqf' );
define( 'NONCE_SALT',       '@ZW;#1LfGU@2vuI2T<A}yp:mWOm}L2t]iFp ~t!,y>,65KBE*@f`hT&rI&AY1x`r' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'cms_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
