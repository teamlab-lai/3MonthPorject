<?php

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Textarea;
use Phalcon\Forms\Element\File;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Url as UrlValidator;
use Phalcon\Validation\Validator\Numericality;
use Phalcon\Validation\Validator\File as FileValidator;
use Phalcon\Validation\Validator\StringLength as StringLength;

class PostForm extends Form
{

    /**
     * Initialize the Post form
     */
    public function initialize($entity = null, $options = array())
    {


        $title = new Text("title",array(
            'maxlength' => 50,
            'placeholder' => 'まとめトピックを入力(50文字以内)',
            'class' => 'form-control'
        ));
        $title->setLabel("title");
        $title->setFilters(array('striptags', 'string'));
        $title->addValidators(array(
            new PresenceOf(array(
                'message' => 'タイトルを入力してください'
            ))
        ));
        $this->add($title);

        $description = new Textarea("description",array(
            'maxlength'  => 150,
            'placeholder'=> 'まとめの説明を入力(150文字以内)',
            'rows'       => 5,
            'cols'       => 40,
            'class' => 'form-control resize-disable'
        ));
        $description->setLabel("description");
        $description->setFilters(array('striptags', 'string'));
        $description->addValidators(array(
            new StringLength(array(
                'max' => 150,
                'messageMaximum' => 'まとめの説明を入力(150文字以内)',
            ))
        ));
        $this->add($description);

        $url_preview = new Text("url_preview",array(
            'size' => 25,
            'placeholder'=> 'URLを入力',
            'class' => 'form-control'
        ));
        $url_preview->setLabel("url preview");
        $url_preview->addValidators(array(
            new UrlValidator(array(
               'message' => 'URLの形式を入力してください',
               'allowEmpty' => true,
               'class' => 'form-control'
            ))
        ));
        $this->add($url_preview);

        $file_upload = new File("file_upload",array(
            'accept' => 'image/*',
            'class' => 'form-control'
            ));

        $file_upload->setLabel("file upload");
        /*
        $file_upload->addValidator(
            new FileValidator([
                'maxSize'       => '2M',
                'messageSize'   => 'Your image exceeds the max filesize (:max).',
                'allowedTypes'  => [ 'image/png'],
                'messageType'   => 'Your image is not a valid image file.',
                'allowEmpty' => true,
            ])
        );
        */
        $this->add($file_upload);

    }
}