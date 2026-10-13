<?php
/**
 * Калькулятор кухни: настройки цен в админке.
 *
 * Шаг 1: только хранение и редактирование чисел.
 * Формулу и сайт подключаем следующими шагами.
 *
 * Использование в других файлах:
 *   $s = tm_calc_settings();   // массив всех значений (с учётом значений по умолчанию)
 *   $s['carcass_price_m2'];
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TM_CALC_OPTION = 'tm_calc_settings';

/**
 * Описание всех полей: группа => [ ключ => [подпись, значение по умолчанию, единица] ].
 * Значения по умолчанию взяты из средних цен Viyar (октябрь 2026) и кухни-ориентира.
 */
function tm_calc_fields() {
	return array(
		'Корпус (ДСП)'             => array(
			'carcass_price_m2'       => array( 'Цена ДСП 18 мм', 730, 'грн за м²' ),
			'carcass_waste'          => array( 'Запас на отходы', 15, '%' ),
			'carcass_m2_per_m_lower' => array( 'ДСП на 1 м нижнего ряда', 3.0, 'м²' ),
			'carcass_m2_per_m_upper' => array( 'ДСП на 1 м верхнего ряда', 2.0, 'м²' ),
			'edge_cut_percent'       => array( 'Кромка и порезка (от корпуса и фасадов)', 21, '%' ),
		),
		'Фасады'                   => array(
			'facade_price_basic'       => array( 'Фасад: базовый (МДФ AGT простой)', 2100, 'грн за м²' ),
			'facade_price_standard'    => array( 'Фасад: стандарт (МДФ AGT)', 3250, 'грн за м²' ),
			'facade_price_premium'     => array( 'Фасад: премиум (NIEMANN, глянец, металлик)', 3950, 'грн за м²' ),
			'facade_m2_per_m_lower'    => array( 'Фасадов на 1 м нижнего ряда', 0.75, 'м²' ),
			'facade_m2_per_m_upper'    => array( 'Фасадов на 1 м верхнего ряда', 0.72, 'м²' ),
			'facade_waste'             => array( 'Запас на отходы', 20, '%' ),
		),
		'Столешница и фартук'      => array(
			'top_price_dsp'       => array( 'Столешница ДСП (Egger стандарт)', 1725, 'грн за пог. м' ),
			'top_price_arpa'      => array( 'Столешница ARPA влагостойкая', 2780, 'грн за пог. м' ),
			'top_price_textured'  => array( 'Столешница фактурная, торец 90°', 3535, 'грн за пог. м' ),
			'top_price_compact'   => array( 'Столешница компакт 12 мм', 5500, 'грн за пог. м' ),
			'top_waste'           => array( 'Запас на стыки и остатки', 5, '%' ),
			'backsplash_standard' => array( 'Фартук-панель стандарт', 1750, 'грн за пог. м' ),
			'backsplash_premium'  => array( 'Фартук-панель премиум', 3250, 'грн за пог. м' ),
		),
		'Фурнитура'                => array(
			'module_width_lower'     => array( 'Средняя ширина нижнего модуля', 0.56, 'м' ),
			'module_width_upper'     => array( 'Средняя ширина верхнего модуля', 0.44, 'м' ),
			'doors_per_module'       => array( 'Дверей на модуль', 0.9, 'шт' ),
			'hinges_per_door'        => array( 'Петель на дверь', 2, 'шт' ),
			'hinge_price_premium'    => array( 'Петля + планка: немецкая (Hettich)', 410, 'грн' ),
			'hinge_price_standard'   => array( 'Петля + планка: стандарт (Muller)', 90, 'грн' ),
			'drawers_default'        => array( 'Ящиков по умолчанию', 3, 'шт' ),
			'drawer_price_premium'   => array( 'Ящик: комплект Hettich', 5240, 'грн' ),
			'drawer_price_standard'  => array( 'Ящик: направляющие скрытые с доводчиком', 620, 'грн' ),
			'cargo_price_premium'    => array( 'Карго: средний/премиум уровень', 3550, 'грн' ),
			'cargo_price_standard'   => array( 'Карго: базовый уровень', 2100, 'грн' ),
			'cargo_default'          => array( 'Карго по умолчанию', 1, 'шт' ),
			'p2o_price_premium'      => array( 'Push-to-open на дверь: Hettich', 210, 'грн' ),
			'p2o_price_standard'     => array( 'Push-to-open на дверь: Muller', 150, 'грн' ),
            'handle_price'           => array( 'Ручка на дверь или ящик (если не push-to-open)', 180, 'грн' ),
			'hanger_price'           => array( 'Навесы на верхний модуль', 190, 'грн' ),
			'small_hardware_percent' => array( 'Ножки, подсветка, мелочь (от фурнитуры)', 8, '%' ),
		),
		'Работа и монтаж'          => array(
			'work_per_module' => array( 'Работа, доставка и монтаж на 1 модуль', 3900, 'грн' ),
			'shape_straight'  => array( 'Коэффициент: прямая', 1.0, '×' ),
			'shape_corner'    => array( 'Коэффициент: угловая', 1.08, '×' ),
			'shape_u'         => array( 'Коэффициент: П-образная', 1.15, '×' ),
			'shape_island'    => array( 'Коэффициент: с островом', 1.25, '×' ),
		),
		'Показ клиенту'            => array(
			'range_percent' => array( 'Диапазон цены (плюс/минус)', 10, '%' ),
			'round_to'      => array( 'Округление до', 1000, 'грн' ),
		),
	);
}

/** Значения по умолчанию одним плоским массивом. */
function tm_calc_defaults() {
	$out = array();
	foreach ( tm_calc_fields() as $group ) {
		foreach ( $group as $key => $field ) {
			$out[ $key ] = $field[1];
		}
	}
	return $out;
}

/** Текущие настройки: сохранённое поверх значений по умолчанию. */
function tm_calc_settings() {
	$saved = get_option( TM_CALC_OPTION, array() );
	if ( ! is_array( $saved ) ) {
		$saved = array();
	}
	return array_merge( tm_calc_defaults(), array_intersect_key( $saved, tm_calc_defaults() ) );
}

/** Пункт меню. */
add_action(
	'admin_menu',
	function () {
		add_menu_page(
			'Калькулятор цен',
			'Калькулятор',
			'manage_options',
			'tm-calc-settings',
			'tm_calc_render_page',
			'dashicons-calculator',
			58
		);
	}
);

/** Регистрация опции и очистка значений. */
add_action(
	'admin_init',
	function () {
		register_setting(
			'tm_calc_group',
			TM_CALC_OPTION,
			array(
				'type'              => 'array',
				'sanitize_callback' => 'tm_calc_sanitize',
				'default'           => array(),
			)
		);
	}
);

function tm_calc_sanitize( $input ) {
	$defaults = tm_calc_defaults();
	$clean    = array();
	foreach ( $defaults as $key => $default ) {
		if ( isset( $input[ $key ] ) && '' !== $input[ $key ] ) {
			$value = str_replace( ',', '.', (string) $input[ $key ] );
			$value = is_numeric( $value ) ? (float) $value : $default;
			$clean[ $key ] = max( 0, $value );
		} else {
			$clean[ $key ] = $default;
		}
	}
	return $clean;
}

/** Страница настроек. */
function tm_calc_render_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$values = tm_calc_settings();
	?>
	<div class="wrap">
		<h1>Калькулятор цен: настройки</h1>
		<p>Все числа, из которых сервер считает цену кухни. Значения по умолчанию взяты из средних цен Viyar (октябрь 2026). Пустое поле возвращает значение по умолчанию. Эти числа не показываются клиентам.</p>
		<form method="post" action="options.php">
			<?php settings_fields( 'tm_calc_group' ); ?>
			<?php foreach ( tm_calc_fields() as $group => $fields ) : ?>
				<h2><?php echo esc_html( $group ); ?></h2>
				<table class="form-table" role="presentation">
					<?php foreach ( $fields as $key => $field ) : ?>
						<tr>
							<th scope="row"><label for="tm_calc_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field[0] ); ?></label></th>
							<td>
								<input type="text" inputmode="decimal" class="small-text"
									id="tm_calc_<?php echo esc_attr( $key ); ?>"
									name="<?php echo esc_attr( TM_CALC_OPTION . '[' . $key . ']' ); ?>"
									value="<?php echo esc_attr( $values[ $key ] ); ?>">
								<span class="description"><?php echo esc_html( $field[2] ); ?> (по умолчанию: <?php echo esc_html( $field[1] ); ?>)</span>
							</td>
						</tr>
					<?php endforeach; ?>
				</table>
			<?php endforeach; ?>
			<?php submit_button( 'Сохранить' ); ?>
		</form>

        		<?php if ( function_exists( 'tm_calc_compute' ) ) : ?>
			<h2>Проверка расчёта (для вас, клиенты этого не видят)</h2>
			<p>Три эталонные кухни пересчитываются по текущим настройкам. Сохраните изменения и обновите страницу, чтобы увидеть новый результат.</p>
			<table class="widefat striped" style="max-width:900px">
				<thead>
					<tr><th>Кухня</th><th>Модулей</th><th>Итого</th><th>Диапазон клиенту</th><th>За метр</th><th>Материалы</th><th>Фурнитура</th><th>Работа</th></tr>
				</thead>
				<tbody>
				<?php foreach ( tm_calc_reference_kitchens() as $name => $input ) : ?>
					<?php $r = tm_calc_compute( $input ); ?>
					<tr>
						<td><?php echo esc_html( $name ); ?></td>
						<td><?php echo esc_html( $r['modules'] ); ?></td>
						<td><strong><?php echo esc_html( number_format( $r['total'], 0, ',', ' ' ) ); ?></strong></td>
						<td><?php echo esc_html( number_format( $r['min'], 0, ',', ' ' ) . ' - ' . number_format( $r['max'], 0, ',', ' ' ) ); ?></td>
						<td><?php echo esc_html( number_format( $r['per_meter'], 0, ',', ' ' ) ); ?></td>
						<td><?php echo esc_html( number_format( $r['parts']['materials'], 0, ',', ' ' ) ); ?></td>
						<td><?php echo esc_html( number_format( $r['parts']['hardware'], 0, ',', ' ' ) ); ?></td>
						<td><?php echo esc_html( number_format( $r['parts']['work'], 0, ',', ' ' ) ); ?></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
	</div>
	<?php
}

/** Эталонные кухни для проверки формулы. */
function tm_calc_reference_kitchens() {
	return array(
		'Кухня-ориентир (170 000 грн по прайсу)' => array(
			'shape'      => 'corner',
			'length'     => 4,
			'upper'      => true,
			'facade'     => 'standard',
			'top'        => 'arpa',
			'backsplash' => 'standard',
			'hardware'   => 'premium',
			'handleless' => true,
			'drawers'    => 3,
			'cargo'      => 1,
		),
		'Недорогая (прямая, 4 м)'                 => array(
			'shape'      => 'straight',
			'length'     => 4,
			'upper'      => true,
			'facade'     => 'basic',
			'top'        => 'dsp',
			'backsplash' => 'none',
			'hardware'   => 'standard',
			'handleless' => false,
			'drawers'    => 3,
			'cargo'      => 1,
		),
		'Средняя (угловая, 4 м)'                  => array(
			'shape'      => 'corner',
			'length'     => 4,
			'upper'      => true,
			'facade'     => 'standard',
			'top'        => 'arpa',
			'backsplash' => 'standard',
			'hardware'   => 'standard',
			'handleless' => false,
			'drawers'    => 3,
			'cargo'      => 1,
		),
	);
}