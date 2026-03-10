<?php
/**
 * Email Order Items
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-order-items.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 9.9.0
 */

use Automattic\WooCommerce\Utilities\FeaturesUtil;

defined( 'ABSPATH' ) || exit;

$text_align       = is_rtl() ? 'right' : 'left';
$margin_side      = is_rtl() ? 'left' : 'right';
$email_improvements_enabled = FeaturesUtil::feature_is_enabled( 'email_improvements' );
$price_text_align = $email_improvements_enabled ? 'right' : 'left';

foreach ( $items as $item_id => $item ) :
	$product       = $item->get_product();
	$sku           = '';
	$purchase_note = '';
	$image         = '';
	$item_avg_stock = '';

	if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
		continue;
	}

	if ( is_object( $product ) ) {
		$sku           = $product->get_sku();
		$purchase_note = $product->get_purchase_note();
		$image         = $product->get_image( $image_size );
		$item_avg_stock = $product->get_stock_quantity();
	}

	if ( $email_improvements_enabled ) :
		?>
	<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'order_item', $item, $order ) ); ?>">
		<td class="td font-family text-align-left" style="vertical-align: middle; word-wrap:break-word;">
			<table class="order-item-data">
				<tr>
					<?php if ( $show_image ) : ?>
						<td><?php echo wp_kses_post( apply_filters( 'woocommerce_order_item_thumbnail', $image, $item ) ); ?></td>
					<?php endif; ?>
					<td>
						<?php echo wp_kses_post( apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, false ) ); ?>
						<?php if ( $show_sku && $sku ) : ?>
							<?php echo wp_kses_post( ' (#' . $sku . ')' ); ?>
						<?php endif; ?>
						<?php do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, $plain_text ); ?>
						<?php
						$item_meta = wc_display_item_meta(
							$item,
							array(
								'before'       => '',
								'after'        => '',
								'separator'    => '<br>',
								'echo'         => false,
								'label_before' => '<span>',
								'label_after'  => ':</span> ',
							)
						);
						echo '<div class="email-order-item-meta">';
						echo wp_kses(
							$item_meta,
							array(
								'br'   => array(),
								'span' => array(),
								'a'    => array(
									'href'   => true,
									'target' => true,
									'rel'    => true,
									'title'  => true,
								),
							)
						);
						echo '</div>';
						do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, $plain_text );
						?>
					</td>
				</tr>
			</table>
		</td>
		<td class="td font-family text-align-<?php echo esc_attr( $price_text_align ); ?>" style="vertical-align:middle;">
			<?php
			echo '&times;';
			$qty          = $item->get_quantity();
			$refunded_qty = $order->get_qty_refunded_for_item( $item_id );

			if ( $refunded_qty ) {
				$qty_display = '<del>' . esc_html( $qty ) . '</del> <ins>' . esc_html( $qty - ( $refunded_qty * -1 ) ) . '</ins>';
			} else {
				$qty_display = esc_html( $qty );
			}
			echo wp_kses_post( apply_filters( 'woocommerce_email_order_item_quantity', $qty_display, $item ) );
			?>
		</td>
		<td class="td font-family text-align-<?php echo esc_attr( $price_text_align ); ?>" style="vertical-align:middle;">
			<?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?>
		</td>
	</tr>
	<?php else : ?>
	<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'order_item', $item, $order ) ); ?>">
		<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; word-wrap:break-word;">
		<?php
		echo wp_kses_post( ' #' . $sku . '' );
		?>
		</td>
		<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; word-wrap:break-word;">
		<?php
		if ( $show_image ) {
			echo wp_kses_post( apply_filters( 'woocommerce_order_item_thumbnail', $image, $item ) );
		}
		echo wp_kses_post( apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, false ) );

		do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, $plain_text );

		wc_display_item_meta(
			$item,
			array(
				'label_before' => '<strong class="wc-item-meta-label" style="float: ' . esc_attr( $text_align ) . '; margin-' . esc_attr( $margin_side ) . ': .25em; clear: both"> We Sell By ',
			)
		);

		do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, $plain_text );

		?>
		</td>

		<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
			<?php echo wp_kses_post( '' . $item_avg_stock . '' ); ?>
		</td>
		<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
			<?php
			$qty          = $item->get_quantity();
			$refunded_qty = $order->get_qty_refunded_for_item( $item_id );

			if ( $refunded_qty ) {
				$qty_display = '<del>' . esc_html( $qty ) . '</del> <ins>' . esc_html( $qty - ( $refunded_qty * -1 ) ) . '</ins>';
			} else {
				$qty_display = esc_html( $qty );
			}
			echo wp_kses_post( apply_filters( 'woocommerce_email_order_item_quantity', $qty_display, $item ) );
			?>
			#
			<?php
			wc_display_item_meta(
				$item,
				array(
					'label_before' => '<strong class="wc-item-meta-label" style="display:none;float: ' . esc_attr( $text_align ) . '; margin-' . esc_attr( $margin_side ) . ': .25em; clear: both">',
				)
			);

			do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, $plain_text );
			?>
		</td>
	</tr>
	<?php endif; ?>

	<?php
	if ( $show_purchase_note && $purchase_note ) {
		?>
		<tr>
			<td colspan="<?php echo $email_improvements_enabled ? '3' : '4'; ?>" class="font-family" style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align:middle;">
				<?php echo wp_kses_post( wpautop( do_shortcode( $purchase_note ) ) ); ?>
			</td>
		</tr>
		<?php
	}
	?>

<?php endforeach; ?>
