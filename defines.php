<?php
/**
 * @author Ondřej Doněk <ondrejd@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GNU General Public License 3.0
 * @link https://github.com/ondrejd/odwp-shwcp_gleton for the canonical source repository
 * @package odwp-shwcp_gleton
 * @since 0.1.0
 */

// Here are defined fields constants

defined( 'GLT_ICO_FIELD' ) || define( 'GLT_ICO_FIELD', 'extra_column_3' );
defined( 'GLT_MESTO_FIELD' ) || define( 'GLT_MESTO_FIELD', 'extra_column_1' );//extra_column_1-4
defined( 'GLT_KRAJ_FIELD' ) || define( 'GLT_KRAJ_FIELD', 'extra_column_2' );//extra_column_2-5
defined( 'GLT_PRAVNIFORMA_FIELD' ) || define( 'GLT_PRAVNIFORMA_FIELD', 'extra_column_7' );//extra_column_7-11
defined( 'GLT_OBOR_FIELD' ) || define( 'GLT_OBOR_FIELD', 'extra_column_8' );//extra_column_8-10
defined( 'GLT_WEB_FIELD' ) || define( 'GLT_WEB_FIELD', 'extra_column_9' );

// And here are some common functions

if( !function_exists( 'odwpglt_get_status_name' ) ) :
    /**
     * Returns correct name for given status.
     * @param integer $status
     * @return string
     * @since 0.2.0
     */
    function odwpglt_get_status_name( $status ) {
        $ret = __( 'neosloveno', GLT_SLUG );
        switch( (int) $status ) {
            case 2: $ret = __( 'otevřeno', GLT_SLUG ); break;
            case 3: $ret = __( 'uzavřeno', GLT_SLUG ); break;
        }
        return $ret;
    }
endif;

if( !function_exists( 'odwpglt_get_substatus_name' ) ) :
    /**
     * Returns correct name for given substatus.
     * @param integer $status
     * @param integer $substatus
     * @return string
     * @since 0.2.0
     */
    function odwpglt_get_substatus_name( $status, $substatus ) {
        $ret = '';

        if( 0 == (int) $status || 1 == (int) $status ) {
            $ret = __( '---', GLT_SLUG );
        }
        elseif( 2 == (int) $status ) {
            switch( (int) $substatus ) {
                case 1: $ret = __( 'nedovolal', GLT_SLUG ); break;
                case 2: $ret = __( 'zájem', GLT_SLUG ); break;
                case 3: $ret = __( 'váhá', GLT_SLUG ); break;
                case 4: $ret = __( 'email', GLT_SLUG ); break;
                case 5: $ret = __( 'v budoucnu', GLT_SLUG ); break;
                case 6: $ret = __( 'kalkulace', GLT_SLUG ); break;
                case 7: $ret = __( 'obchod', GLT_SLUG ); break;
                default: $ret = __( '---', GLT_SLUG );
            }
        } 
        elseif( 3 == (int) $status ) {
            switch( (int) $substatus ) {
                case 1: $ret = __( 'nezájem', GLT_SLUG ); break;
                case 2: $ret = __( 'smlouva', GLT_SLUG ); break;
                case 3: $ret = __( 'sekretářka', GLT_SLUG ); break;
                default: $ret = __( '---', GLT_SLUG );
            }
        }

        return $ret;
    }
endif;

if( !function_exists( 'odwpglt_get_statuses_and_substatuses' ) ) :
    /**
     * Returns array with statuses and substatuses.
     * @return array
     * @since 0.2.0
     */
    function odwpglt_get_statuses_and_substatuses() {
        return array(
            1 => array(
                'label' => __( 'neosloveno', GLT_FILE ),
                'items' => array()
            ),
            2 => array(
                'label' => __( 'otevřeno', GLT_FILE ),
                'items' => array(
                    '1' => __( 'nedovolal', GLT_FILE ),
                    '2' => __( 'zájem', GLT_FILE ),
                    '3' => __( 'váhá', GLT_FILE ),
                    '4' => __( 'email', GLT_FILE ),
                    '5' => __( 'v budoucnu', GLT_FILE ),
                    '6' => __( 'kalkulace', GLT_FILE ),
                    '7' => __( 'obchod', GLT_FILE ),
                ),
            ),
            3 => array(
                'label' => __( 'uzavřeno', GLT_FILE ),
                'items' => array(
                    '1' => __( 'nezájem', GLT_FILE ),
                    '2' => __( 'smlouva', GLT_FILE ),
                    '3' => __( 'sekretářka', GLT_FILE ),
                ),
            ),
        );
    }
endif;
