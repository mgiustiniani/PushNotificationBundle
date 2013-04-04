<?php
namespace Manticora\PushNotificationBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity(repositoryClass="Manticora\PushNotificationBundle\Repository\ClientOldRepository")
 * @ORM\Table(name="client_old", uniqueConstraints={@ORM\UniqueConstraint(name="token_unique",columns={"token"})}
 *      )
 */
class ClientOld
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
    protected $type;
    

    
    /**
     *  @ORM\Column(type="string", length=255)
     */
    protected $token;
    
    /**
     *  @ORM\Column(type="text", nullable=true)
     */
    protected $description;
    
    
    
public function __toString() {
	return $this->type.' - '.$this->token;
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
     * Set type
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set token
     *
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * Get token
     *
     * @return string 
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set description
     *
     * @param text $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return text 
     */
    public function getDescription()
    {
        return $this->description;
    }
}