<?php


class Key {

    private $column;
    private $keyWord;

    public function __construct($column, $keyWord) {
        $this->column = $column;
        $this->keyWord = $keyWord;
    }

    public function getColumn()
    {
        return $this->column;
    }

    public function getKeyWord()
    {
        return $this->keyWord;
    }

}