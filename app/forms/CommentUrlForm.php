<?php

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Validation\Validator\Url as UrlValidator;
use Phalcon\Validation\Validator\Numericality;

class CommentUrlForm extends Form
{

    /**
     * Initialize the Post form
     */
    public function initialize($entity = null, $options = array())
    {

        $url_comment = new Text("url_comment",array(
            'size' => 25,
            'placeholder'=> 'URLを入力',
            'class' => 'form-control'
        ));

        $url_comment->addValidators(array(
            new UrlValidator(array(
               'message' => 'URLの形式を入力してください',
            ))
        ));

        $this->add($url_comment);

    }
}