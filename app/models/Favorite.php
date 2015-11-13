<?php

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Validator\StringLength as StringLengthValidator;

class Favorite extends Model
{
     /**
     * @var varchar
     */
    public $user_fb_id;
    /**
     * @var varchar
     */
    public $page_id;

    public $update_time;

    /**
     * お気に入り initializer
     */
    public function initialize()
    {

    }

    public function validation()
    {
        /**
         *string length validation
         */
        $this->validate(new StringLengthValidator(array(
            "field" => 'user_fb_id',
            'max' => 50,
            'min' => 1,
            'messageMaximum' => 'ユーザーのIDは長いです。(50文字以内)',
            'messageMinimum' => 'ユーザーのIDはなければなりません。(1文字以上)'
        )));

        $this->validate(new StringLengthValidator(array(
            "field" => 'page_id',
            'max' => 100,
            'min' => 1,
            'messageMaximum' => 'ページのIDは長いです。。(100文字以内)',
            'messageMinimum' => 'ページのIDはなければなりません。(1文字以上)'
        )));

        if ($this->validationHasFailed() == true) {
            return false;
        }
    }
}
