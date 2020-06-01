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


/**
 * Interface OtpInterface
 * @package sFire\Otp
 */
Interface OtpInterface {


    /**
     * Set the secret key
     * @param string $secret
     * @return object
     */
    public function setSecret(string $secret): object;


    /**
     * Set the type of a algorithm which will be used to generate the OTP
     * @param string $algorithm
     * @return object
     */
    public function setAlgorithm(string $algorithm): object;


    /**
     * Set the amount of digits the OTP needs to contain
     * @param int $digits
     * @return object
     */
    public function setDigits(int $digits): object;


    /**
     * Creates a random secret key that is compatible with an OTP secret key
     * @param int $length The length of the key
     * @param bool $numbers True if the secret key may contain numbers, False if not
     * @param bool $letters True if the secret key may lowercase letters, False if not
     * @param bool $capitals True if the secret key may capital letters, False if not
     * @return string
     */
    public function generateSecret(int $length = 16, bool $numbers = true, bool $letters = true, bool $capitals = true): string;
}