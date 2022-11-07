<?php
namespace App\Models\notion;

/**
 * Notionのエキスパンションオブジェクト
 */
class NotionExp {

    private string $notionId;

    private string $name;

    private string $attr = "";
    
    /**
     * Get the value of notionId
     */ 
    public function getNotionId()
    {
        return $this->notionId;
    }

    /**
     * Set the value of notionId
     *
     * @return  self
     */ 
    public function setNotionId($notionId)
    {
        $this->notionId = $notionId;

        return $this;
    }

    /**
     * Get the value of name
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */ 
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of attr
     */ 
    public function getAttr()
    {
        return $this->attr;
    }

    /**
     * Set the value of attr
     *
     * @return  self
     */ 
    public function setAttr($attr)
    {
        $this->attr = $attr;

        return $this;
    }
}
?>