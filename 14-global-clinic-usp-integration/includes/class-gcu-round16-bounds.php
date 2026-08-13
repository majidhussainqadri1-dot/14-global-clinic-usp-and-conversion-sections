<?php

defined( 'ABSPATH' ) || exit;

final class GCU_Round16_Bounds {
	const MAX_QUESTION_SIGNALS = 500;

	public static function bootstrap() {
		add_filter( 'gcu_future_question_aggregates', array( __CLASS__, 'bound_question_signals' ), PHP_INT_MAX );
	}

	public static function bound_question_signals( $signals ) {
		if ( ! is_array( $signals ) ) {
			return array();
		}
		if ( count( $signals ) > self::MAX_QUESTION_SIGNALS ) {
			GCU_Observability::log( 'warning', 'future_faq_gap_signal_ceiling_exceeded', array( 'count' => count( $signals ), 'ceiling' => self::MAX_QUESTION_SIGNALS ) );
			return array();
		}
		return $signals;
	}
}
