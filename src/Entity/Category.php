<?php


namespace Entity;

/**
 * Description of Category
 *
 * @author Etudiant
 */
class Category {
    
    /**
     * id of the category
     * @var integer
     */
    private $id;
    
    /**
     * name of the user
     * @var string 
     */
    private $name;
    
    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setName($name) {
        $this->name = $name;
    }


}
