<?php

namespace Entity;

/**
 * Description of Loaning
 *
 * @author Etudiant
 */
class Loaning
{

    /**
     * id of the loaning
     * @var integer
     */
    private $id;

    /**
     * date_start of the loaning
     * @var \Datetime
     */
    private $dateStart;

    /**
     * date_end of the loaning
     * @var \Datetime
     */
    // backslash devant datetime pour désigner le namespace global
    private $dateEnd;

    /**
     * The game associated with this loaning
     * @var \Entity\Game
     */
    private $game;

    /**
     * The user that did this loaning
     * @var \Entity\User
     */
    private $user;

    public function getId()
    {
        return $this->id;
    }

    public function getDateStart()
    {
        return $this->dateStart;
    }

    public function getDateEnd()
    {
        return $this->dateEnd;
    }

    public function getGame()
    {
        return $this->game;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setDateStart($dateStart)
    {

        if ($dateStart instanceof string) {
            $dateStart = \DateTime::createFromFormat('Y/m/d', $dateStart);
        }
        $this->dateStart = $dateStart;
    }

    public function setDateEnd($dateEnd)
    {

        if ($dateEnd instanceof string) {
            $dateEnd = \DateTime::createFromFormat('Y/m/d', $dateEnd);
        }
        $this->dateEnd = $dateEnd;
    }

    public function setGame(\Entity\Game $game)
    {
        $this->game = $game;
    }

    public function setUser(\Entity\User $user)
    {
        $this->user = $user;
    }

}
