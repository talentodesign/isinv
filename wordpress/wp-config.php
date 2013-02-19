<?php
/** 
 * As configurações básicas do WordPress.
 *
 * Esse arquivo contém as seguintes configurações: configurações de MySQL, Prefixo de Tabelas,
 * Chaves secretas, Idioma do WordPress, e ABSPATH. Você pode encontrar mais informações
 * visitando {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. Você pode obter as configurações de MySQL de seu servidor de hospedagem.
 *
 * Esse arquivo é usado pelo script ed criação wp-config.php durante a
 * instalação. Você não precisa usar o site, você pode apenas salvar esse arquivo
 * como "wp-config.php" e preencher os valores.
 *
 * @package WordPress
 */

// ** Configurações do MySQL - Você pode pegar essas informações com o serviço de hospedagem ** //
/** O nome do banco de dados do WordPress */
define('DB_NAME', 'isinv');

/** Usuário do banco de dados MySQL */
define('DB_USER', 'isinv');

/** Senha do banco de dados MySQL */
define('DB_PASSWORD', 'designer2802');

/** nome do host do MySQL */
define('DB_HOST', '186.202.122.28');

/** Conjunto de caracteres do banco de dados a ser usado na criação das tabelas. */
define('DB_CHARSET', 'utf8');

/** O tipo de collate do banco de dados. Não altere isso se tiver dúvidas. */
define('DB_COLLATE', '');

/**#@+
 * Chaves únicas de autenticação e salts.
 *
 * Altere cada chave para um frase única!
 * Você pode gerá-las usando o {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * Você pode alterá-las a qualquer momento para desvalidar quaisquer cookies existentes. Isto irá forçar todos os usuários a fazerem login novamente.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'hL9`^|S$Tj&U^gP9gL-#g}+pbk9WbzgLD*{+[j@Jf<^4+0GqsVY[=#*c;&>Dy1@O');
define('SECURE_AUTH_KEY',  'd(xgB(2zG+$&DxS@~M0A*h#I)rW:udOma`f6T#z5F2K.A$Fr@anfN9+G&Z$^N&nz');
define('LOGGED_IN_KEY',    ';f&BahuUI_$(xe_(J//J2H6w r8B5nC~7eo>*&~`P z|YVkG<4hlI;#S6?^w|@k/');
define('NONCE_KEY',        '4K`A!w2bx=<{BpW4:y$_&=[uf0w=Hg$r4gFcIjhv]]psrE])SVkKl_Aj)`l|e%v+');
define('AUTH_SALT',        '^,U/*ssnIa6PDVhiXst/F1GKQ@&Ted0oXts)M==~)oI`9qM-QY{:r|MR@D.+4A1$');
define('SECURE_AUTH_SALT', ' N1|{f,F(FM{|Dg1K |4}cVtrO27/_l#a>il>x69c`Pa-tPhrZ>Lh3L-(1}ff@ZD');
define('LOGGED_IN_SALT',   'ytAk4e&B}Ta}3#[z|RR2w:$).3h&noYkQm(}8I5,HTqiLP/HNpIvKbmRCm2_$c*L');
define('NONCE_SALT',       '5&l}$-298UV,-5`fc.8w@}oGPXk{Oq+?^0 U+$@d+r|YfQo_-/^_FC`|H-R&afgr');

/**#@-*/

/**
 * Prefixo da tabela do banco de dados do WordPress.
 *
 * Você pode ter várias instalações em um único banco de dados se você der para cada um um único
 * prefixo. Somente números, letras e sublinhados!
 */
$table_prefix  = 'wp_';

/**
 * O idioma localizado do WordPress é o inglês por padrão.
 *
 * Altere esta definição para localizar o WordPress. Um arquivo MO correspondente ao
 * idioma escolhido deve ser instalado em wp-content/languages. Por exemplo, instale
 * pt_BR.mo em wp-content/languages e altere WPLANG para 'pt_BR' para habilitar o suporte
 * ao português do Brasil.
 */
define('WPLANG', 'pt_BR');

/**
 * Para desenvolvedores: Modo debugging WordPress.
 *
 * altere isto para true para ativar a exibição de avisos durante o desenvolvimento.
 * é altamente recomendável que os desenvolvedores de plugins e temas usem o WP_DEBUG
 * em seus ambientes de desenvolvimento.
 */
define('WP_DEBUG', false);

/* Isto é tudo, pode parar de editar! :) */

/** Caminho absoluto para o diretório WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');
	
/** Configura as variáveis do WordPress e arquivos inclusos. */
require_once(ABSPATH . 'wp-settings.php');
