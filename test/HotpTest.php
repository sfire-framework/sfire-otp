<?php
/**
 * sFire Framework (https://sfire.io)
 *
 * @link      https://github.com/sfire-framework/ for the canonical source repository
 * @copyright Copyright (c) 2014-2020 sFire Framework.
 * @license   http://sfire.io/license BSD 3-CLAUSE LICENSE
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sFire\Otp\Driver\Hotp;


/**
 * Class HotpTest
 */
final class HotpTest extends TestCase {


    /**
     * Holds an instance of Hotp
     * @var Hotp
     */
    private Hotp $hotp;


    /**
     * Setup. Created new Hotp instance
     * @return void
     */
    protected function setUp(): void {
       
        $this -> hotp = new Hotp();
        $this -> hotp -> setSecret('ABCDEFGHIJK');
    }


    /**
     * Test if token can be generated
     * @return void
     */
    public function testIfTokenCanBeGeneratedByCurrentTimestamp(): void {

        $this -> assertTrue(true === is_string($this -> hotp -> counter(1)));
        $this -> assertTrue(6 === strlen($this -> hotp -> counter(1)));
        $this -> assertRegExp('#[0-9]{6,6}#', $this -> hotp -> counter(1));
    }


    /**
     * Test if a token can be verified
     * @return void
     */
    public function testIfTokenCanBeVerified(): void {

        $this -> assertTrue($this -> hotp -> verify('058407', 1));
        $this -> assertTrue($this -> hotp -> verify($this -> hotp -> counter(1), 1));
        $this -> assertFalse($this -> hotp -> verify('012345', 1));
    }


    /**
     * Test if a provisioning URL can be generated
     * @return void
     */
    public function testRetrievingProvisioningUrl(): void {
        $this -> assertTrue(true === is_string($this -> hotp -> getProvisioningUrl('Accountname', 1)));
    }


    /**
     * Test if multiple settings can be set
     * Test if the settings are working correctly
     * @return void
     */
    public function testSettingOptions(): void {

        $this -> hotp -> setAlgorithm('ripemd160');
        $this -> hotp -> setDigits(8);
        $this -> assertTrue($this -> hotp -> verify('36605754', 1));
    }


    /**
     * Test if a secret key can be generated
     * @return void
     */
    public function testIfKeyCanBeGenerated(): void {

        $this -> assertRegExp('#[a-zA-Z234567]{16,16}#', $this -> hotp -> generateSecret());
        $this -> assertRegExp('#[a-zA-Z234567]{48,48}#', $this -> hotp -> generateSecret(48));
        $this -> assertRegExp('#[234567]{48,48}#', $this -> hotp -> generateSecret(48, true, false, false));
        $this -> assertRegExp('#[a-z234567]{48,48}#', $this -> hotp -> generateSecret(48, true, true, false));
        $this -> assertRegExp('#[a-zA-Z234567]{48,48}#', $this -> hotp -> generateSecret(48, true, true, true));
    }
}