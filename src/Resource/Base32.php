<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.io
 * @copyright Copyright (c) 2014-2020 sFire Framework.
 * @license   https://sfire.io/license BSD 3-CLAUSE LICENSE
 */

namespace sFire\Otp\Resource;

use sFire\Otp\Exception\RuntimeException;


/**
 * Class Base32
 * @package sFire\Otp
 */
class Base32 {


    private const BITS_5_RIGHT = 31;
    private const CHARS = 'abcdefghijklmnopqrstuvwxyz234567';


    /**
     * Encodes a string to Base32. Use padding false when encoding for urls
     * @param string $data
     * @param bool $padRight
     * @return string
     */
    public static function encode($data, $padRight = false) {

        $dataSize       = strlen($data);
        $res            = '';
        $remainder      = 0;
        $remainderSize  = 0;

        for($i = 0; $i < $dataSize; $i++) {

            $b              = ord($data[$i]);
            $remainder      = ($remainder << 8) | $b;
            $remainderSize += 8;
            
            while ($remainderSize > 4) {

                $remainderSize -= 5;
                $c = $remainder & (self::BITS_5_RIGHT << $remainderSize);
                $c >>= $remainderSize;
                $res .= static::CHARS[$c];
            }
        }

        if($remainderSize > 0) {

            $remainder <<= (5 - $remainderSize);
            $c = $remainder & self::BITS_5_RIGHT;
            $res .= static::CHARS[$c];
        }

        if($padRight) {

            $padSize = (8 - ceil(($dataSize % 5) * 8 / 5)) % 8;
            $res .= str_repeat('=', $padSize);
        }

        return $res;
    }


    /**
     * Decode a Base32 string
     * @param string $data
     * @return string
     * @throws RuntimeException
     */
    public static function decode($data) {

        $data     = rtrim($data, "=\x20\t\n\r\0\x0B");
        $dataSize = strlen($data);
        $buf      = 0;
        $bufSize  = 0;
        $res      = '';
        $charMap  = array_flip(str_split(static::CHARS));
        $charMap += array_flip(str_split(strtoupper(static::CHARS)));

        for($i = 0; $i < $dataSize; $i++) {

            $c = $data[$i];

            if(!isset($charMap[$c])) {

                if ($c == ' ' || $c == "\r" || $c == "\n" || $c == "\t") {
                    continue; //Ignore these safe characters
                }

                throw new RuntimeException('Encoded string contains unexpected char #' . ord($c) . ' at offset ' . $i . ' (using improper alphabet?)');
            }

            $b        = $charMap[$c];
            $buf      = ($buf << 5) | $b;
            $bufSize += 5;

            if($bufSize > 7) {
                
                $bufSize -= 8;
                $b = ($buf & (0xff << $bufSize)) >> $bufSize;
                $res .= chr($b);
            }
        }

        return $res;
    }
}