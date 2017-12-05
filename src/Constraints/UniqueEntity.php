<?php

namespace Constraints;

use Symfony\Component\Validator\Constraint;
/**
 * Description of UniqueEntity
 *
 * @author Etudiant
 */
class UniqueEntity extends Constraint
{
    public $message = 'The {{column}} already exists';
    
    public $field; // le nom de la colonne
    public $dao;
    
    public function __construct($options = null)
    {
        if(is_array($options)){
            $this->field = $options['field'];
            $this->dao = $options['dao'];
        }
        parent::__construct($options);
        // $options : tableau associatif
    }
    
    public function validatedBy()
    {
        //get_class : obtenir le nom de la class courante
        // cette contrainte validé par un validator
        return get_class($this).'Validator';
    }
    
    public function getField()
    {
        return $this->field;
    }

    public function getDao()
    {
        return $this->dao;
    }
    
}
