<?php

namespace App\Models\notion;

use App\Models\Expansion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotionCard extends Model
{
    use HasFactory;

    
    private $id;

    private $index;

    private $name = '';

    private $price;

    private $color;

    private int $stock;

    private ?array $expansion;

    private ?string $imageUrl = null;

    private string $enname = "";

    private bool $isFoil;

    private string $lang = "";

    private string $desc = "";

    private string $conditon = "";

    private string $barcode = "";

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

    public function getStock():int {
        return $this->stock;
    }

    public function setStock(int $stock) {
        $this->stock = $stock;
    }

    public function setExpansion(array $expansion) {
        $this->expansion = $expansion;
    }

    public function getExpansion():array {
        return $this->expansion;
    }

    public function setImageUrl($imageUrl) {
        $this->imageUrl = $imageUrl;
    }

    public function getImageUrl() {
        return $this->imageUrl;
    }

    public function setEnname($enname) {
        $this->enname = $enname;
    }

    public function getEnname() {
        return $this->enname;
    }

    public function setFoil($isFoil) {
        $this->isFoil = $isFoil;
    }

    public function isFoil() {
        return $this->isFoil;
    }

    public function setLang($lang) {
        $this->lang = $lang;
    }

    public function getLang() {
        return $this->lang;
    }

    public function getDesc() {
        return $this->desc;
    }

    public function setDesc($desc) {
        $this->desc = $desc;
    }

    public function setCondition(string $conditon) {
        $this->conditon = $conditon;
    }

    public function getCondition():string {
        return $this->conditon;
    }

    /**
     * Get the value of barcode
     */ 
    public function getBarcode()
    {
        return $this->barcode;
    }

    /**
     * Set the value of barcode
     *
     * @return  self
     */ 
    public function setBarcode($barcode)
    {
        $this->barcode = $barcode;

        return $this;
    }
}
