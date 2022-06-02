<?php

namespace Syntro\SilverstripeElementalVideo\Element;

use SilverStripe\Assets\Image;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\FieldGroup;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Assets\File;
use SilverStripe\ORM\ValidationResult;
use DNADesign\Elemental\Models\BaseElement;


/**
 * llows the user to add a video from a spcified source
 *
 * @author Matthias Leutenegger <hello@syntro.ch>
 */
class Video extends BaseElement
{
    const YT_PATTERN = '/^https:\/\/(www\.)?(youtube\.com|youtu\.be)\/(watch\?v=)?([a-zA-Z0-9_-]+)/';

    /**
     * Defines the database table name
     * @config
     *  @var string
     */
    private static $table_name = 'ElementVideo';

    /**
    * @config
     * @var string
     */
    private static $icon = 'font-icon-block-video';

    /**
     * Singular name for CMS
     * @config
     *  @var string
     */
    private static $singular_name = 'Video';

    /**
     * Plural name for CMS
     * @config
     *  @var string
     */
    private static $plural_name = 'Videos';

    /**
    * @config
     * @var bool
     */
    private static $inline_editable = false;

    /**
     * @config
     * @var bool
     */
    private static $allow_title_customization = false;

    /**
     * Display a show title button
     *
     * @config
     * @var boolean
     */
    private static $displays_title_in_template = false;

    /**
     * hide the fields using display logic
     *
     * @config
     * @var array
     */
    private static $hide_field_for_style = [];


    /**
     * Database fields
     * @config
     * @var array
     */
    private static $db = [
        'VideoType' => 'Enum("local,youtube", "local")',
        'VideoURL' => 'Varchar',
        'Autoplay' => 'Boolean',
        'ShowControls' => 'Boolean',
        'Loop' => 'Boolean',
    ];

    /**
     * Add default values to database
     *  @var array
     */
    private static $defaults = [
        'VideoType' => 'local',
        'Autoplay' => false,
        'ShowControls' => true,
        'Loop' => false
    ];

    /**
     * Has_one relationship
     * @var array
     */
    private static $has_one = [
        'Video' => File::class,
        'Cover' => Image::class
    ];

    /**
     * Relationship version ownership
     * @var array
     */
    private static $owns = [
        'Video',
        'Cover'
    ];

    /**
     * CMS Fields
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName([
            'Autoplay',
            'ShowControls',
            'Loop'
        ]);
        $fields->addFieldsToTab(
            'Root.Main',
            [
                $typeField = OptionsetField::create(
                    'VideoType',
                    'Video Type',
                    ['local' => 'Local', 'youtube' => 'Youtube']
                ),
                FieldGroup::create([
                    CheckboxField::create(
                        'Autoplay',
                        'Autoplay'
                    ),
                    CheckboxField::create(
                        'ShowControls',
                        'Show Controls'
                    ),
                    CheckboxField::create(
                        'Loop',
                        'Loop'
                    ),
                ]),
                $urlField = TextField::create(
                    'VideoURL',
                    'Video URL'
                ),
                $fileField = UploadField::create(
                    'Video',
                    'Video'
                ),
                $thumbnailField = UploadField::create(
                    'Cover',
                    'Cover Image'
                ),
            ]
        );

        $thumbnailField
            ->setFolderName('videos/covers')
            ->setAllowedMaxFileNumber(1)
            ->hideIf('VideoType')->isEqualTo('youtube');
        $fileField
            ->setFolderName('videos')
            ->setAllowedExtensions(['mpeg','mp4', 'webm'])
            ->setAllowedMaxFileNumber(1)
            ->hideIf('VideoType')->isEqualTo('youtube');
        $urlField
            ->hideIf('VideoType')->isEqualTo('local');

        $hideablefields = $this->config()->get('hide_field_for_style');
        foreach ($hideablefields as $field => $hideForStyles) {
            if (in_array($this->Style, $hideForStyles)) {
                $fields->removeByName($field);
            }
        }
        return $fields;
    }


    /**
     * getType
     *
     * @return string
     */
    public function getType()
    {
        return _t(__CLASS__ . '.BlockType', 'Video');
    }

    /**
     * @return array
     */
    protected function provideBlockSchema()
    {
        $blockSchema = parent::provideBlockSchema();
        $blockSchema['content'] = 'A Video';
        return $blockSchema;
    }

    /**
     * validate - validates the given Data
     *
     * @return ValidationResult
     */
    public function validate()
    {
        $result = parent::validate();
        switch ($this->VideoType) {
            case 'youtube':
                if ($this->VideoURL && !$this->isYTVideo($this->VideoURL)) {
                    $result->addFieldError('VideoURL', 'Please enter a valid URL from Youtube');
                }
                break;
            case 'local':
                break;
            default:
                $result->addFieldError('VideoType', 'Invalid video type.');
                break;
        }
        return $result;
    }

    /**
     * isYTVideo - check if the given domain is a youtube one
     *
     * @param  string $domain domain to check
     * @return bool
     */
    public function isYTVideo($domain)
    {
        $match = preg_match(self::YT_PATTERN, $domain);
        return $match === 1;
    }

    /**
     * getVideoIdentifier - returns the remote ID of the video
     *
     * @return string
     */
    public function getVideoIdentifier()
    {
        switch ($this->VideoType) {
            case 'youtube':
                return $this->getYTVideoIdentifier();
            default:
                return '';
        }
    }

    /**
     * getYTVideoIdentifier - extracts the video ID from a youtube URL
     * Distinguishes between two formats:
     * - https://www.youtube.com/watch?v=XXXXXXXXXX
     * - https://youtu.be/XXXXXXXXXX
     *
     * @return string|null
     */
    public function getYTVideoIdentifier()
    {
        $matches = [];
        $match = preg_match(self::YT_PATTERN, $this->VideoURL, $matches);
        if ($match === 1) {
            return $matches[4];
        }
        return null;
    }
}
