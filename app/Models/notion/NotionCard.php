<?php

namespace App\Models\notion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotionCard extends Model
{
    use HasFactory;

    
    private $id;

    private $index;

    private $name;

    private $price;

    private $color;

    public function setId($id) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;   
    }

    public function setIndex($index) {
        $this->index = $index;
    }

    public function getIndex() {
        return $this->index;
    }

    public function getName():string {
        return $this->name;
    }

    public function setName(string $name){
        $this->name = $name;
    }

    public function getPrice():int {
        return $this->price;
    }

    public function setPrice($price) {
        $this->price = $price;
    }

    
    public function getColor():string {
        return $this->color;
    }

    public function setColor(string $color){
        $this->color = $color;
    }

}
