<?php

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Validator\Url as UrlValidator;
use Phalcon\Mvc\Model\Validator\Ip;
use Phalcon\Mvc\Model\Validator\StringLength as StringLengthValidator;

class Comments extends Model
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
    public $parent_comment_id;
    /**
     * @var varchar
     */
    public $comment_id;
    /**
     * @var varchar
     */
    public $url_comment;
    /**
     * @var varchar
     */
    public $picture_url;
    /**
     * @var varchar
     */
    public $picture_title;
    /**
     * @var varchar
     */
    public $picture_description;
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
    public $video_type;
    /**
     * @var varchar
     */
    public $video_title;
    /**
     * @var varchar
     */
    public $video_description;
    /**
     * @var varchar
     */
    public $text_comment;
    /**
     * @var varchar
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

        $this->validate(new UrlValidator(array(
            'field' => 'url_comment',
            'message' => 'URLの形式を入力してください。',
            'allowEmpty' => true,
        )));

        $this->validate(new UrlValidator(array(
            'field' => 'picture_url',
            'message' => 'URLの形式を入力してください。',
            'allowEmpty' => true,
        )));

        $this->validate(new StringLengthValidator(array(
            "field" => 'picture_title',
            'max' => 100,
            'messageMaximum' => 'タイトルを入力してください。(50文字以内)',
        )));

        $this->validate(new StringLengthValidator(array(
            "field" => 'picture_description',
            'max' => 150,
            'messageMaximum' => '説明を入力してください。(150文字以内)',
        )));

        $this->validate(new UrlValidator(array(
            'field' => 'video_url',
            'message' => 'URLの形式を入力してください。',
            'allowEmpty' => true,
        )));

        $this->validate(new UrlValidator(array(
            'field' => 'video_thumbnail_url',
            'message' => 'URLの形式を入力してください。',
            'allowEmpty' => true,
        )));

        $this->validate(new StringLengthValidator(array(
            "field" => 'video_title',
            'max' => 100,
            'messageMaximum' => 'タイトルを入力してください。(50文字以内)',
        )));

        $this->validate(new StringLengthValidator(array(
            "field" => 'video_description',
            'max' => 150,
            'messageMaximum' => '説明を入力してください。(150文字以内)',
        )));

        $this->validate(new StringLengthValidator(array(
            "field" => 'text_comment',
            'max' => 150,
            'messageMaximum' => '説明を入力してください。(150文字以内)',
        )));

        if ($this->validationHasFailed() == true) {
            return false;
        }
    }
}
