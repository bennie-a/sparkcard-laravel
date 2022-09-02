<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    function __construct($index, $name, $price){
        $split = explode('/', $name, 2);
        $this->name = $split[0];
        $this->enname = $split[1];
        $this->index = $index + 1;
        $this->price = $price;
    }
    private $index;
    private $name;
    private $enname;
    private $price;

    public function getIndex() {
        return $this->index;
    }

    public function getName() {
        return $this->name;
    }

    public function getEnname() {
        return $this->enname;
    }

    public function getPrice() {
        return $this->price;
    }

    protected $visible = ['index', 'name', 'enname', 'price'];

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
