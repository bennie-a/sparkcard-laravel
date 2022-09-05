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
    private $color;

    public function getIndex():int {
        return $this->index;
    }

    public function getName():string {
        return $this->name;
    }

    public function getEnname():string {
        return $this->enname;
    }

    public function getPrice():int {
        $str_num = str_replace(',', '', $this->price);
        $int_num = intval($str_num);
        return $int_num;
    }

    public function getColor():string {
        return $this->color;
    }

    public function setColor(string $color) {
        $this->color = $color;
    }

}
