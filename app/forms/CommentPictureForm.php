<?php

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Textarea;
use Phalcon\Forms\Element\File;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\File as FileValidator;
use Phalcon\Validation\Validator\StringLength as StringLength;

class CommentPictureForm extends Form
{

    /**
     * Initialize the Post form
     */
    public function initialize($entity = null, $options = array())
    {

        $picture_file_upload = new File("picture_url",array(
            'accept' => 'image/*',
            ));

        $this->add($picture_file_upload);

        $picture_title = new Text("picture_title",array(
            'maxlength' => 50,
            'placeholder' => '画像タイトル(50文字以内)',
            'class' => 'form-control'
        ));
        $picture_title->setFilters(array('striptags', 'string'));
        $picture_title->addValidators(array(
            new StringLength(array(
                'max' => 50,
                'messageMaximum' => '画像タイトルを入力してください(50文字以内)',
            ))
        ));
        $this->add($picture_title);

        $picture_description = new Textarea("picture_description",array(
            'maxlength'  => 150,
            'placeholder'=> '画像コメント(150文字以内)',
            'rows'       => 5,
            'cols'       => 40,
            'class' => 'form-control resize-disable'
        ));
        $picture_description->setFilters(array('striptags', 'string'));
        $picture_description->addValidators(array(
            new StringLength(array(
                'max' => 150,
                'messageMaximum' => '画像コメントを入力(150文字以内)',
            ))
        ));
        $this->add($picture_description);
    }
}