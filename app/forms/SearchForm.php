<?php

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength as StringLength;

class SearchForm extends Form
{

    /**
     * Initialize the Post form
     */
    public function initialize($entity = null, $options = array())
    {

        $title = new Text("keyword",array(
            'placeholder' => 'まとめを検察',
            'class' => 'form-control'
        ));
        $title->setLabel("keyword");
        $title->setFilters(array('striptags', 'string'));
        $title->addValidators(array(
            new PresenceOf(array(
                'message' => 'キーワードを入力してください'
            ))
        ));
        $this->add($title);
    }
}