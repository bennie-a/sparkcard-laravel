<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    function __construct($name, $url){
        $split = explode('/', $name, 2);
        $this->name = $split[0];
        $this->enname = $split[1];
        $this->url = $url;
    }
    private $name;
    private $enname;
    private $url;

    public function getName() {
        return $this->name;
    }

    public function getEnname() {
        return $this->enname;
    }

    public function getUrl() {
        return $this->url;
    }

}
