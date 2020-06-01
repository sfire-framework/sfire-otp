<?php
/**
 * sFire Framework (https://sfire.io)
 *
 * @link      https://github.com/sfire-framework/ for the canonical source repository
 * @copyright Copyright (c) 2014-2020 sFire Framework.
 * @license   http://sfire.io/license BSD 3-CLAUSE LICENSE
 */

declare(strict_types=1);

namespace sFire\Otp\Driver;

use sFire\Otp\OtpAbstract;


/**
 * Class Totp
 * @package sFire\Otp
 */
class Totp extends OtpAbstract {
    

    /**
     * The time before a TOTP expires in seconds
     * @var int
     */
    private int $interval = 30;


    /**
     * Set the interval for TOTP before the otp will expire
     * @param int $interval
     * @return self
     */
    public function setInterval(int $interval): self {
        
        $this -> interval = $interval;
        return $this;
    }


    /**
     * Get the password for a specific unix timestamp value
     * @param int $timestamp A unix timestamp (acts like a counter)
     * @return string
     */
    public function timestamp(int $timestamp): string {
        return $this -> generateOtp($this -> timecode($timestamp));
    }


    /**
     * Get the password for the current unix timestamp value
     * @return string
     */
    public function now(): string {
        return $this -> generateOtp($this -> timecode(time()));
    }


    /**
     * Verify if a password is valid for a specific counter value
     * @param string $otp
     * $param int $discrepancy Discrepancy is the factor of interval allowed on either side of the given timestamp
     * @param int $timestamp
     * @return bool
     */
    public function verify(string $otp, ?int $discrepancy = 0, int $timestamp = null): bool {

        if($timestamp === null) {
            $timestamp = time();
        }

        if(null === $discrepancy || 0 === $discrepancy) {
            return $otp === $this -> timestamp($timestamp);
        }

        for($i = -$discrepancy; $i <= $discrepancy; ++$i) {

            if($otp === $this -> timestamp($timestamp + ($i * $this -> interval))) {
                return true;
            }
        }

        return false;
    }


    /**
     * Returns the uri for a specific secret for totp method.
     * @param string $name The account name to be used
     * @return string
     */
    public function getProvisioningUrl(string $name): string {

        $this -> validateSecret();
        return 'otpauth://totp/' . urlencode($name) . '?secret=' . $this -> secret;
    }


    /**
     * Transform a timestamp in a counter based on specified internal
     * @param int $timestamp
     * @return int
     */
    private function timeCode(int $timestamp): int {
        return (int) ((((int) $timestamp * 1000) / ($this -> interval * 1000)));
    }
}