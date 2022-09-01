<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    function __construct($index, $name, $url, $price){
        $split = explode('/', $name, 2);
        $this->name = $split[0];
        $this->enname = $split[1];
        $this->url = $url;
        $this->index = $index + 1;
        $this->price = $price;
    }
    private $index;
    private $name;
    private $enname;
    private $url;
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

    public function getUrl() {
        return $this->url;
    }

    public function getPrice() {
        return $this->price;
    }
}
