<?php
namespace Syntro\SilverstripeElementalElementals\Tests;

use SilverStripe\Dev\FunctionalTest;

/**
 * Test the configuration
 * @author Matthias Leutenegger
 */
class DefaultTest extends FunctionalTest
{
    /**
     * Defines the fixture file to use for this test class
     * @var string
     */
    protected static $fixture_file = './defaultfixture.yml';

    // TODO: Add Tests

    /**
     * default test - description
     *
     * @return void
     */
    public function testDefault()
    {
        $this->assertEquals(2, 1+1);
    }
}