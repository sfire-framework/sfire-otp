<?php
/**
 * sFire Framework (https://sfire.io)
 *
 * @link      https://github.com/sfire-framework/ for the canonical source repository
 * @copyright Copyright (c) 2014-2020 sFire Framework.
 * @license   http://sfire.io/license BSD 3-CLAUSE LICENSE
 */

declare(strict_types=1);

namespace sFire\Otp;

use sFire\Otp\Resource\Base32;


/**
 * Class AbstractOtp
 * @package sFire\Otp
 */
abstract class OtpAbstract Implements OtpInterface {


    /**
     * Contains the amount of digits the OTP must contain
     * @var int
     */
    private int $digits = 6;


    /**
     * A secret key for validating the OTP
     * @var string
     */
    public ?string $secret = null;


    /**
     * Contains the algorithm/digest which is used to generate the OTP
     * @var string
     */
    public string $algorithm = 'sha1';


    /**
     * Set the secret key
     * @param string $secret
     * @return object
     */
    public function setSecret(string $secret): object {
    	
        $this -> secret = $secret;
        return $this;
    }


    /**
     * Set the type of a algorithm which will be used to generate the OTP
     * @param string $algorithm
     * @return object
     */
    public function setAlgorithm(string $algorithm): object {
    	
        $this -> algorithm = $algorithm;
        return $this;
    }


    /**
     * Set the amount of digits the OTP needs to contain
     * @param int $digits
     * @return object
     */
    public function setDigits(int $digits): object {
        
        $this -> digits = $digits;
        return $this;
    }


    /**
     * Creates a random secret key that is compatible with an OTP secret key
     * @param int $length The length of the key
     * @param bool $numbers True if the secret key may contain numbers, False if not
     * @param bool $letters True if the secret key may lowercase letters, False if not
     * @param bool $capitals True if the secret key may capital letters, False if not
     * @return string
     */
    public function generateSecret(int $length = 16, bool $numbers = true, bool $letters = true, bool $capitals = true): string {
        
        $array           = [];
        $caseInsensitive = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
        $caseSensitive   = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
        $numbersArr     = [2, 3, 4, 5, 6, 7];
        $key             = '';

        $numbers  && ($array = array_merge($array, $numbersArr));
        $letters  && ($array = array_merge($array, $caseInsensitive));
        $capitals && ($array = array_merge($array, $caseSensitive));

        if(count($array) > 0) {

            for($i = 0; $i < $length; $i++) {
                $key .= $array[array_rand($array, 1)];
            }
        }

        return $key;
    }


    /**
     * Generate a one-time password
     * @param int $input The number used to seed the HMAC hash function.
     * @return string
     */
    protected function generateOTP(int $input): string {

        $hash = hash_hmac($this -> algorithm, $this -> intToByteString($input), $this -> byteSecret());
        $hmac = [];

        foreach(str_split($hash, 2) as $hex) {
            $hmac[] = hexdec($hex);
        }

        $offset = $hmac[count($hmac) - 1] & 0xF;
        $code   = ($hmac[$offset + 0] & 0x7F) << 24 | ($hmac[$offset + 1] & 0xFF) << 16 | ($hmac[$offset + 2] & 0xFF) << 8 | ($hmac[$offset + 3] & 0xFF);
        $otp    = $code % pow(10, $this -> digits);

        return str_pad((string) $otp, $this -> digits, '0', STR_PAD_LEFT);
    }


    /**
     * Returns the binary value of the base32 encoded secret
     * @return string
     */
    private function byteSecret() {
        return Base32 :: decode($this -> secret);
    }


    /**
     * Turns an int in a OATH byte string
     * @param int $int
     * @return string
     */
    private function intToByteString(int $int): string {

        $result = [];

        while($int != 0) {
            
            $result[] = chr($int & 0xFF);
            $int >>= 8;
        }
        
        return str_pad(join(array_reverse($result)), 8, "\000", STR_PAD_LEFT);
    }
}