<?php
/**
 * La configuration de base de votre installation WordPress.
 *
 * Ce fichier contient les réglages de configuration suivants : réglages MySQL,
 * préfixe de table, clés secrètes, langue utilisée, et ABSPATH.
 * Vous pouvez en savoir plus à leur sujet en allant sur
 * {@link http://codex.wordpress.org/fr:Modifier_wp-config.php Modifier
 * wp-config.php}. C’est votre hébergeur qui doit vous donner vos
 * codes MySQL.
 *
 * Ce fichier est utilisé par le script de création de wp-config.php pendant
 * le processus d’installation. Vous n’avez pas à utiliser le site web, vous
 * pouvez simplement renommer ce fichier en "wp-config.php" et remplir les
 * valeurs.
 *
 * @package WordPress
 */

// ** Réglages MySQL - Votre hébergeur doit vous fournir ces informations. ** //
/** Nom de la base de données de WordPress. */
define('DB_NAME', 'db754286704');

/** Utilisateur de la base de données MySQL. */
define('DB_USER', 'dbo754286704');

/** Mot de passe de la base de données MySQL. */
define('DB_PASSWORD', 'pharaon04');

/** Adresse de l’hébergement MySQL. */
define('DB_HOST', 'db754286704.db.1and1.com');

/** Jeu de caractères à utiliser par la base de données lors de la création des tables. */
define('DB_CHARSET', 'utf8mb4');

/** Type de collation de la base de données.
  * N’y touchez que si vous savez ce que vous faites.
  */
define('DB_COLLATE', '');

/**#@+
 * Clés uniques d’authentification et salage.
 *
 * Remplacez les valeurs par défaut par des phrases uniques !
 * Vous pouvez générer des phrases aléatoires en utilisant
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ le service de clefs secrètes de WordPress.org}.
 * Vous pouvez modifier ces phrases à n’importe quel moment, afin d’invalider tous les cookies existants.
 * Cela forcera également tous les utilisateurs à se reconnecter.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '@@n#!_I.](1}50B9YV-2bxmDDv&4$:xF;AT}8K2XnTddSIQm@o~;;l7(A$QgbV>u');
define('SECURE_AUTH_KEY',  '<}qhiu|2j^<{:&[`njHU$e/iCA?#%vk>$7ZG:ODqtR<l JX:)+;6I4vm}R}/);B}');
define('LOGGED_IN_KEY',    'TA!!s0}a(:`cZb-qF 7a[@Pb,C}yb8)$j?I&fL!3rM.{g{Gl={Vv/ jU?#(,ky b');
define('NONCE_KEY',        '12^0t6O>|0J|&UIvM`kCo>;0yA_e`I Pws~46VO^ d^SLVRG{fy]c-hcr4 W|LnF');
define('AUTH_SALT',        '-H:*/TQ)*D-NO.y;{[y@~FZ;C^}l0 cKH5D.@ybk+e(KX|gkc_a4m1)1$E#D24W:');
define('SECURE_AUTH_SALT', '.IdY%TfVGr_]v65]]^/,1(%w)i5~Om N+fl4Nx(`Ik_cW>m,b5@V?LXH> WBu7!0');
define('LOGGED_IN_SALT',   'd)HY>{B$^Fux22~K/Itr>1bx1Jy$ ^H#__K~^(.f%!AK+OcIZI(w9fgF/U>9*))*');
define('NONCE_SALT',       'RWA!>Bwy;SV&{.q@oXe<v8M_@_mMA0!}~)p:Rq!ibB&1jAEw=38U,tF2<XF07*^^');
/**#@-*/

/**
 * Préfixe de base de données pour les tables de WordPress.
 *
 * Vous pouvez installer plusieurs WordPress sur une seule base de données
 * si vous leur donnez chacune un préfixe unique.
 * N’utilisez que des chiffres, des lettres non-accentuées, et des caractères soulignés !
 */
$table_prefix  = 'wp_';

/**
 * Pour les développeurs : le mode déboguage de WordPress.
 *
 * En passant la valeur suivante à "true", vous activez l’affichage des
 * notifications d’erreurs pendant vos essais.
 * Il est fortemment recommandé que les développeurs d’extensions et
 * de thèmes se servent de WP_DEBUG dans leur environnement de
 * développement.
 *
 * Pour plus d’information sur les autres constantes qui peuvent être utilisées
 * pour le déboguage, rendez-vous sur le Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* C’est tout, ne touchez pas à ce qui suit ! */

/** Chemin absolu vers le dossier de WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Réglage des variables de WordPress et de ses fichiers inclus. */
require_once(ABSPATH . 'wp-settings.php');