<?php

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Textarea;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Url as UrlValidator;
use Phalcon\Validation\Validator\StringLength as StringLength;

class CommentVideoForm extends Form
{

    /**
     * Initialize the Post form
     */
    public function initialize($entity = null, $options = array())
    {

        $video_title = new Text("video_title",array(
            'maxlength' => 50,
            'placeholder' => '動画タイトル(50文字以内)',
            'class' => 'form-control'
        ));
        $video_title->setFilters(array('striptags', 'string'));
        $video_title->addValidators(array(
            new StringLength(array(
                'max' => 50,
                'messageMaximum' => '動画タイトルを入力してください(50文字以内)',
            ))
        ));
        $this->add($video_title);

        $video_description = new Textarea("video_description",array(
            'maxlength'  => 150,
            'placeholder'=> '動画コメント(150文字以内)',
            'rows'       => 5,
            'cols'       => 40,
            'class' => 'form-control resize-disable'
        ));
        $video_description->setFilters(array('striptags', 'string'));
        $video_description->addValidators(array(
            new StringLength(array(
                'max' => 150,
                'messageMaximum' => '動画コメントを入力(150文字以内)',
            ))
        ));

        $this->add($video_description);

        $video_url = new Text("video_url",array(
            'size' => 25,
            'placeholder'=> 'URLを入力',
            'class' => 'form-control'
        ));
        $video_url->addValidators(array(
            new UrlValidator(array(
               'message' => 'URLの形式を入力してください',
            )),
        ));
        $this->add($video_url);

    }
}