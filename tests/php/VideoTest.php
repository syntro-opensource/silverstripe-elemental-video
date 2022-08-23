<?php
namespace Syntro\SilverstripeElementalVideo\Tests;

use SilverStripe\Dev\FunctionalTest;
use Syntro\SilverstripeElementalVideo\Element\Video;

/**
 * Test the configuration
 * @author Matthias Leutenegger
 */
class VideoTest extends FunctionalTest
{
    /**
     * Defines the fixture file to use for this test class
     * @var string
     */
    protected static $fixture_file = './defaultfixture.yml';

    /**
     * testYoutubeVideoIdentifierCorrectlyFound
     *
     * @return void
     */
    public function testYoutubeVideoIdentifierCorrectlyFound()
    {
        $block = Video::create([
            'VideoType' => 'youtube'
        ]);

        $block->VideoURL = 'https://www.youtube.com/watch?v=NVGuFdX5guE';
        $this->assertEquals('NVGuFdX5guE', $block->getYTVideoIdentifier());

        $block->VideoURL = 'https://youtu.be/NVGuFdXdguE';
        $this->assertEquals('NVGuFdXdguE', $block->getYTVideoIdentifier());
    }

    /**
     * testIdentifiercorrectlySelectsPlatform
     *
     * @return void
     */
    public function testIdentifiercorrectlySelectsPlatform()
    {
        $block = Video::create([
            'VideoType' => 'youtube'
        ]);

        $block->VideoURL = 'https://www.youtube.com/watch?v=NVGuFdX5guE';
        $this->assertEquals('NVGuFdX5guE', $block->getVideoIdentifier());

        $block->VideoType = 'local';
        $this->assertEquals('', $block->getVideoIdentifier());
    }

    public function testCorrectlyIdentifiesYoutubeVideo()
    {
        $block = Video::create([
            'VideoType' => 'youtube'
        ]);

        $this->assertTrue($block->isYTVideo('https://www.youtube.com/watch?v=NVGuFdX5guE'));
        $this->assertTrue($block->isYTVideo('https://youtu.be/NVGuFdXdguE'));
        $this->assertFalse($block->isYTVideo('https://youtu.be/'));
        $this->assertFalse($block->isYTVideo('https://www.youtube.com/watch'));
        $this->assertFalse($block->isYTVideo('https://yodutu.be/NVGuFdXdguE'));
        $this->assertFalse($block->isYTVideo('https://www.youtudbe.com/watch?v=NVGuFdX5guE'));
        $this->assertFalse($block->isYTVideo(''));

    }
}
