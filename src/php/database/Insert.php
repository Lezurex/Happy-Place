<?php

class Insert {

    private $column;
    private $value;

    public function __construct($column, $value) {
        $this->column = $column;
        $this->value = $value;
    }

    public function getColumn() {
        return $this->column;
    }

    public function getValue()
    {
        return $this->value;
    }
}