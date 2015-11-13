<?php

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Validator\Url as UrlValidator;
use Phalcon\Mvc\Model\Validator\Ip;
use Phalcon\Mvc\Model\Validator\StringLength as StringLengthValidator;

class Topics extends Model
{
     /**
     * @var varchar
     */
    public $user_name;
     /**
     * @var varchar
     */
    public $user_fb_id;
    /**
     * @var varchar
     */
    public $user_picture_url;
    /**
     * @var varchar
     */
    public $page_id;
    /**
     * @var varchar
     */
    public $title;
    /**
     * @var varchar
     */
    public $picture_url;
    /**
     * @var varchar
     */
    public $video_url;
    /**
     * @var varchar
     */
    public $video_thumbnail_url;
    /**
     * @var varchar
     */
    public $description;
    /**
     * @var varchar
     */
    public $ip_location;
    /**
     * @var int
     */
    public $views;
    /**
     * @var timestamp
     */
    public $update_time;

    public function initialize()
    {
        /*
        $this->belongsTo('product_types_id', 'ProductTypes', 'id', array(
            'reusable' => true
        ));
        */
    }

    public function validation()
    {
        /**
         *string length validation
         */
        $this->validate(new StringLengthValidator(array(
            "field" => 'title',
            'max' => 100,
            'min' => 1,
            'messageMaximum' => 'まとめトピックを入力してください。(50文字以内)',
            'messageMinimum' => 'まとめトピックを入力してください。(1文字以内)'
        )));

        $this->validate(new StringLengthValidator(array(
            "field" => 'description',
            'max' => 150,
            'min' => 0,
            'messageMaximum' => 'まとめの説明を入力してください。(150文字以内)',
        )));

        /**
         *url validation
         */
        $this->validate(new UrlValidator(array(
            'field' => 'user_picture_url',
            'message' => 'URLの形式を入力してください。',
            'allowEmpty' => true,
        )));

        $this->validate(new UrlValidator(array(
            'field' => 'picture_url',
            'message' => 'ユーザー画像はURLの形式を入力してください。',
            'allowEmpty' => true,
        )));

        $this->validate(new UrlValidator(array(
            'field' => 'video_url',
            'message' => 'ビデオはURLの形式を入力してください。',
            'allowEmpty' => true,
        )));

        $this->validate(new UrlValidator(array(
            'field' => 'video_thumbnail_url',
            'message' => 'ビデオのサムネイルはURLの形式を入力してください。',
            'allowEmpty' => true,
        )));

        /**
         *IP validation
         */
        // Any pubic IP
        $this->validate(new IP(array(
          'field'             => 'ip_location',
          'version'           => IP::VERSION_4 | IP::VERSION_6, // v6 and v4. The same if not specified
          'allowReserved'     => true,   // False if not specified. Ignored for v6
          'allowPrivate'      => true,   // False if not specified
          'message'           => 'IPの形式を入力してください。',
        )));

        if ($this->validationHasFailed() == true) {
            return false;
        }
    }
}
