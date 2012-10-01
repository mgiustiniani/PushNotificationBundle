<?php
namespace Manticora\PushNotificationBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity(repositoryClass="Manticora\PushNotificationBundle\Repository\MessageAttributeRepository")
 * @ORM\Table(name="message_attribute")
 */
class MessageAttribute
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
    protected $chiave;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true )
     */
    protected $valore;
    
    /**
     * @ORM\ManyToOne(targetEntity="MessageTemplate", inversedBy="attributes")
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id")
     */
    protected $message_template;
    /**
     * @ORM\ManyToOne(targetEntity="Message", inversedBy="attributes")
     * @ORM\JoinColumn(name="message_id", referencedColumnName="id")
     */
    protected $message;
    
   
    
    public function __construct(){
    	
    }
    
    public function __toString()  {
    	return $this->valore;
    }
    
 /*   public function __clone()
    {
    	// If the entity has an identity, proceed as normal.
    	if ($this->id) {
    	}
    }*/

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
     * Set key
     *
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * Get key
     *
     * @return string 
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set value
     *
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set message
     *
     * @param Manticora\PushNotificationBundle\Entity\Message $message
     */
    public function setMessage(\Manticora\PushNotificationBundle\Entity\Message $message)
    {
        $this->message = $message;
    }

    /**
     * Get message
     *
     * @return Manticora\PushNotificationBundle\Entity\Message 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set chiave
     *
     * @param string $chiave
     */
    public function setChiave($chiave)
    {
        $this->chiave = $chiave;
    }

    /**
     * Get chiave
     *
     * @return string 
     */
    public function getChiave()
    {
        return $this->chiave;
    }

    /**
     * Set valore
     *
     * @param string $valore
     */
    public function setValore($valore)
    {
        $this->valore = $valore;
    }

    /**
     * Get valore
     *
     * @return string 
     */
    public function getValore()
    {
        return $this->valore;
    }

    /**
     * Set message_template
     *
     * @param Manticora\PushNotificationBundle\Entity\MessageTemplate $messageTemplate
     */
    public function setMessageTemplate(\Manticora\PushNotificationBundle\Entity\MessageTemplate $messageTemplate)
    {
        $this->message_template = $messageTemplate;
    }

    /**
     * Get message_template
     *
     * @return Manticora\PushNotificationBundle\Entity\MessageTemplate 
     */
    public function getMessageTemplate()
    {
        return $this->message_template;
    }
}