<?php

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Textarea;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Numericality;
use Phalcon\Validation\Validator\StringLength as StringLength;

class CommentTextForm extends Form
{

    /**
     * Initialize the Post form
     */
    public function initialize($entity = null, $options = array())
    {

        $text_comment = new Textarea("text_comment",array(
            'maxlength'  => 150,
            'placeholder'=> 'テキストを入力(150文字以内)',
            'rows'       => 5,
            'cols'       => 40,
            'class' => 'form-control resize-disable'
        ));

        $text_comment->setFilters(array('striptags', 'string'));
        $text_comment->addValidators(array(
            new StringLength(array(
                'max' => 150,
                'messageMaximum' => 'テキストを入力(150文字以内)',
                'min' => 1,
                'messageMinimum' => 'テキストを入力(1文字以上)',
            ))
        ));
        $this->add($text_comment);

    }
}