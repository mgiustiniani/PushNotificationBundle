<?php

namespace Manticora\PushNotificationBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity(repositoryClass="Manticora\PushNotificationBundle\Repository\MessageTypeRepository")
 * @ORM\Table(name="message_type")
 */
class MessageType
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name;
     /**
     * @ORM\Column(type="string", length=255)
     */
    protected $description;
    
    
    

public function __toString() {
	return $this->name .' - ' .$this->description;
}
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
}