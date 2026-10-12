<?php
/**
 * 301-редиректы со старого сайта (Next.js) на новые адреса.
 * Срабатывает только когда WordPress не нашёл страницу (404), поэтому рабочие страницы не трогает.
 * Учтены и старые правила Next.js-сайта:
 *  - /en/... и /uk/... ведут на украинскую версию (английской больше нет, /uk/ всегда был без префикса);
 *  - /product/{id} (ещё более старый адрес) ведёт сразу на проект, без промежуточного /service/{id};
 *  - /blog/... ведёт на раздел статей своего языка.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tm_redirect_map() {
	return array(
		'/ru/about' => '/ru/o-nas/',
		'/ru/contacts' => '/ru/kontakty/',
		'/privacy-policy' => '/polityka-konfidentsiinosti/',
		'/ru/privacy-policy' => '/ru/politika-konfidentsialnosti/',
		'/service/torgovi-mebli-ta-stelazhi-na-zamovlennia-kharkiv' => '/service/inshi-mebli-na-zamovlennia-kharkiv/',
		'/ru/service/kuhnya-na-zakaz-kharkov' => '/ru/service/kuhni-na-zakaz-harkov/',
		'/ru/service/shkaf-kupe-na-zakaz-kharkov' => '/ru/service/shkafy-kupe-na-zakaz-harkov/',
		'/ru/service/torgovaya-mebel-i-stellazhi-na-zakaz-kharkov' => '/ru/service/drugaya-mebel-na-zakaz-harkov/',
		'/ru/service/mebel-dlia-spalni-na-zakaz-kharkov' => '/ru/service/mebel-dlya-spalni-na-zakaz-harkov/',
		'/service/1' => '/project/suchasna-kukhnia-v-svitlykh-tonakh-z-lakonichnym-dyzainom/',
		'/ru/service/1' => '/ru/project/sovremennaya-kukhnya-v-svetlykh-tonakh-s-lakonichnym/',
		'/service/2' => '/project/elehantna-kukhnia-z-kontrastnymy-fasadamy-ta-suchasnym/',
		'/ru/service/2' => '/ru/project/elegantnaya-kukhnya-s-kontrastnymi-fasadami-i-sovremennym/',
		'/service/3' => '/project/kutova-kukhnia-z-matovymy-fasadamy-ta-khromovanym-trymachem/',
		'/ru/service/3' => '/ru/project/uglovaya-kukhnya-s-matovymi-fasadami-i-khromirovannym/',
		'/service/4' => '/project/bila-kukhnia-z-matovoiu-stilnytseiu-pid-derevo/',
		'/ru/service/4' => '/ru/project/belaya-kukhnya-s-matovoy-stoleshnitsey-pod-derevo/',
		'/service/5' => '/project/kukhnia-z-salatovymy-fasadamy-pid-derevo-ta-led-pidsvitkoiu/',
		'/ru/service/5' => '/ru/project/kukhnya-s-salatovymi-fasadami-pod-derevo-i-led-podsvetkoy/',
		'/service/6' => '/project/shafa-kupe-z-dzerkalnymy-dvertsiatamy-ta-sklianymy/',
		'/ru/service/6' => '/ru/project/shkaf-kupe-s-zerkalnymi-dveryami-i-steklyannymi-polkami/',
		'/service/7' => '/project/shafa-komod-u-matovomu-bilomu-kolori-z-derevianymy-bokovymy/',
		'/ru/service/7' => '/ru/project/shkaf-komod-v-matovom-belom-tsvete-s-derevyannymi-bokovymi/',
		'/service/8' => '/project/vbudovana-shafa-kupe-z-rozsuvnymy-dvertsiatamy-ta-vriznymy/',
		'/ru/service/8' => '/ru/project/vstroennyy-shkaf-kupe-s-razdvizhnymi-dveryami-i-vreznymi/',
		'/service/9' => '/project/vbudovana-shafa-kupe-z-velykymy-dzerkalamy-ta-chotyrma/',
		'/ru/service/9' => '/ru/project/vstroennyy-shkaf-kupe-s-bolshimi-zerkalami-i-chetyrmya/',
		'/service/10' => '/project/kompaktna-shafa-kupe-pid-derevo-z-dzerkalamy/',
		'/ru/service/10' => '/ru/project/kompaktnyy-shkaf-kupe-pod-derevo-s-zerkalami-i-dekorativnymi/',
		'/service/11' => '/project/shafa-kupe-pid-temne-derevo-z-chornymy-reikamy-dzerkalamy/',
		'/ru/service/11' => '/ru/project/shkaf-kupe-pod-temnoe-derevo-s-chernymi-reykami-zerkalami/',
		'/service/12' => '/project/stelazh-iz-chervonymy-yashchykamy-ta-kriplenniamy/',
		'/ru/service/12' => '/ru/project/stellazh-s-krasnymi-yashchikami-i-krepleniyami-dlya-optiki/',
		'/service/13' => '/project/spalnia-z-pidiomnym-lizhkom-vbudovanymy-shafamy-ta-tochkovym/',
		'/ru/service/13' => '/ru/project/spalnya-s-podemnoy-krovatyu-vstroennymi-shkafami/',
		'/service/14' => '/project/dvospalne-bile-lizhko-z-pidiomnym-mekhanizmom-i-dvoma/',
		'/ru/service/14' => '/ru/project/dvuspalnaya-belaya-krovat-s-podemnym-mekhanizmom-i-dvumya/',
		'/service/15' => '/project/bilyi-stelazh-perehorodka-do-steli-dlia-spalni/',
		'/ru/service/15' => '/ru/project/belyy-stellazh-peregorodka-do-potolka-dlya-spalni/',
		'/service/16' => '/project/stinka-v-styli-minimalizm-z-matovym-sklom-na-shafi-ta-tumbi/',
		'/ru/service/16' => '/ru/project/stenka-v-stile-minimalizm-s-matovym-steklom-na-shkafu/',
		'/service/17' => '/project/kukhnia-z-verkhnimy-fasadamy-pid-kamin-i-nyzhnimy-elementamy/',
		'/ru/service/17' => '/ru/project/kukhnya-s-verkhnimi-fasadami-pod-kamen-i-nizhnimi-elementami/',
		'/service/83f2d61d-f883-4073-9d3e-d947c657090a' => '/project/oblashtuvannia-mansardy-v-styli-shale-vbudovani-shafy/',
		'/ru/service/83f2d61d-f883-4073-9d3e-d947c657090a' => '/ru/project/obustroystvo-mansardy-v-stile-shale-vstroennye-shkafy/',
		'/service/9e371ac7-b547-40a5-8adf-564c92009db9' => '/project/elehantna-shafa-kupe-pid-dub/',
		'/ru/service/9e371ac7-b547-40a5-8adf-564c92009db9' => '/ru/project/elegantnyy-shkaf-kupe-pod-dub/',
		'/service/c171a41d-02ce-4228-88d2-39b7aa9fc683' => '/project/kukhnia-z-robochoiu-zonoiu-bilia-vikna-ta-kontrastnymy/',
		'/ru/service/c171a41d-02ce-4228-88d2-39b7aa9fc683' => '/ru/project/kukhnya-s-rabochey-zonoy-u-okna-i-kontrastnymi-fasadami/',
		'/service/d15d6f3c-3e3d-433e-a6f4-ed6e30487164' => '/project/harderobna-kimnata-z-shafoiu-kupe-stylne-rishennia/',
		'/ru/service/d15d6f3c-3e3d-433e-a6f4-ed6e30487164' => '/ru/project/garderobnaya-komnata-so-shkafom-kupe-stilnoe-reshenie/',
		'/service/d886a4df-6599-49f4-83b0-1670a27ffffa' => '/project/komplekt-mebliv-u-bilomu-matovomu-kolori-dlia-spalni/',
		'/ru/service/d886a4df-6599-49f4-83b0-1670a27ffffa' => '/ru/project/komplekt-mebeli-v-belom-matovom-tsvete-v-spalnyu/',
		'/service/da26c0fb-d3d2-496a-851b-8a98dd67e60c' => '/project/suchasna-vbudovana-shafa-kupe-v-styli-loft/',
		'/ru/service/da26c0fb-d3d2-496a-851b-8a98dd67e60c' => '/ru/project/sovremennyy-vstroennyy-shkaf-kupe-v-stile-loft/',
	);
}

add_action( 'template_redirect', function () {
	if ( ! is_404() ) {
		return;
	}

	// Адрес без домена и слэшей по краям, маленькими буквами, по частям: ru / service / 5
	$path = (string) wp_parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH );
	$segs = array_values( array_filter( explode( '/', strtolower( rawurldecode( $path ) ) ), 'strlen' ) );

	$lang    = '';    // '' = украинская версия, '/ru' = русская
	$changed = false; // адрес уже отличается от запрошенного, значит редирект нужен в любом случае

	if ( isset( $segs[0] ) && in_array( $segs[0], array( 'en', 'uk' ), true ) ) {
		array_shift( $segs );
		$changed = true;
	} elseif ( isset( $segs[0] ) && 'ru' === $segs[0] ) {
		array_shift( $segs );
		$lang = '/ru';
	}

	// Старый блог: ведём в статьи своего языка.
	if ( isset( $segs[0] ) && 'blog' === $segs[0] ) {
		wp_safe_redirect( home_url( $lang . '/guides/' ), 301, 'T-Mebel redirects' );
		exit;
	}

	// Совсем старые адреса /product/{id} → /service/{id}, дальше по таблице.
	if ( isset( $segs[0], $segs[1] ) && 'product' === $segs[0] ) {
		$segs[0] = 'service';
		$changed = true;
	}

	$key    = rtrim( $lang . '/' . implode( '/', $segs ), '/' );
	$key    = '' === $key ? '/' : $key;
	$map    = tm_redirect_map();
	$target = $map[ $key ] ?? null;

	if ( ! $target && $changed ) {
		$target = '/' === $key ? '/' : $key . '/';
	}

	if ( $target ) {
		wp_safe_redirect( home_url( $target ), 301, 'T-Mebel redirects' );
		exit;
	}
}, 1 );