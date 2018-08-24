<?php

/**
 * @author John Snook
 * @date Aug 2, 2018
 * @license https:/**snooky.biz/site/license
 * @copyright 2018 John Snook Consulting
 * Description of IpHelper
 */

namespace johnsnook\visitors\helpers;

class IpHelper extends \yii\helpers\BaseIpHelper {

    /**
     * <code>
     * var_dump(cidrToRange("73.35.143.32/27"));
     * array(2) {
     *     [0]=>
     *     string(12) "73.35.143.32"
     *     [1]=>
     *     string(12) "73.35.143.63"
     * }
     * </code>
     *
     * @param string $cidr
     * @return array
     */
    public static function cidrToRange($cidr) {
        $range = array();
        $cidr = explode('/', $cidr);
        $range[0] = long2ip((ip2long($cidr[0])) & ((-1 << (32 - (int) $cidr[1]))));
        $range[1] = long2ip((ip2long($range[0])) + pow(2, (32 - (int) $cidr[1])) - 1);
        return $range;
    }

    /**
     * Returns an array of cidr lists that map the range given
     *
     * @param string $start
     * @param string $end
     * @return type
     */
    public static function rangeToCdrList($start, $end) {
        $s = explode(".", $start);
        /**
         * PHP ip2long does not handle leading zeros on IP addresses! 172.016
         * comes back as 172.14, seems to be treated as octal!
         */
        $start = "";
        $dot = "";
        while (list($key, $val) = each($s)) {
            $start = sprintf("%s%s%d", $start, $dot, $val);
            $dot = ".";
        }
        $end = "";
        $dot = "";
        $e = explode(".", $end);
        while (list($key, $val) = each($e)) {
            $end = sprintf("%s%s%d", $end, $dot, $val);
            $dot = ".";
        }
        $start = ip2long($start);
        $end = ip2long($end);
        $result = array();
        while ($end >= $start) {
            $maxsize = static::maskBlock($start, 32);
            $x = log($end - $start + 1) / log(2);
            $maxdiff = floor(32 - floor($x));
            $ip = long2ip($start);
            if ($maxsize < $maxdiff) {
                $maxsize = $maxdiff;
            }
            $result[] = "$ip/$maxsize";
            $start += pow(2, (32 - $maxsize));
        }
        return $result;
    }

    /**
     * I don't know
     *
     * @param integer $base
     * @param integer $bit
     * @return type
     */
    private static function maskBlock($base, $bit) {
        while ($bit > 0) {
            $im = hexdec(static::mask($bit - 1));
            $imand = $base & $im;
            if ($imand != $base) {
                break;
            }
            $bit--;
        }
        return $bit;
    }

    /**
     * Convert ip part from 10 base to hex
     *
     * @param int $maskVal
     * @return string
     */
    private static function mask($maskVal) {
        /**
         * use base_convert not dechex because dechex is broken and returns
         * 0x80000000 instead of 0xffffffff
         */
        return base_convert((pow(2, 32) - pow(2, (32 - $maskVal))), 10, 16);
    }

}
