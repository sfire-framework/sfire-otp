<?php
/**
 * sFire Framework (https://sfire.io)
 *
 * @link      https://github.com/sfire-framework/ for the canonical source repository
 * @copyright Copyright (c) 2014-2020 sFire Framework.
 * @license   http://sfire.io/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\Otp\Driver;

use sFire\Otp\OtpAbstract;


/**
 * Class Hotp
 * @package sFire\Otp
 */
class Hotp extends OtpAbstract {
   

    /**
     * Get the password for a specific counter value
     * @param int $count 
     * @return string
     */
    public function counter(int $count): string {
        return $this -> generateOTP($count);
    }


    /**
     * Verify if a password is valid for a specific counter value
     * @param string $otp
     * @param int $counter
     * @return bool
     */
    public function verify(string $otp, int $counter): bool {
        return $otp === $this -> counter($counter);
    }


    /**
     * Returns the uri for a specific secret for HOTP method.
     * @param string $name
     * @param int $initial_count
     * @return string
     */
    public function getProvisioningUrl(string $name, int $initial_count): string {
        return 'otpauth://hotp/' . urlencode($name) . '?secret=' . $this -> secret . '&counter=' . $initial_count;
    }
}