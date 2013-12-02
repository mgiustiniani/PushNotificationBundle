<?php
namespace Manticora\PushNotificationBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity(repositoryClass="Manticora\PushNotificationBundle\Repository\MessageRepository")
 * @ORM\Table(name="message")
 * @ORM\HasLifecycleCallbacks
 */
class Message
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
    protected $description;
    
    /**
     * Stringa su cui inserire la sintassi di cron
     * @ORM\Column(type="string", length=255)
     */
    protected $cronstring;
    /**
     * Stringa su cui inserire la sintassi di cron
     * @ORM\Column(type="boolean",nullable=true)
     */  
    protected $enable;
    /**
     * Stringa su cui inserire la sintassi di cron
     * @ORM\Column(type="boolean",nullable=true)
     */
    protected $active;
    /**
     * Stringa su cui inserire la sintassi di cron
     * @ORM\Column(type="boolean")
     */    
    protected $push_all;
    
    /**
     * Stringa su cui inserire la sintassi di cron
     * @ORM\Column(type="datetime",nullable=true))
     */
    protected $start_time;
    /**
     * Stringa su cui inserire la sintassi di cron
     * @ORM\Column(type="datetime",nullable=true))
     */
    protected $stop_time;
    
    /**
     * @ORM\ManyToOne(targetEntity="MessageGroup")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     */
    protected $group;
    /**
     * @ORM\ManyToOne(targetEntity="MessageType")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     */
    protected $type;
    
    /**
     * @ORM\ManyToOne(targetEntity="MessageTemplate")
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id")
     */
    protected $template;
    /**
     * @ORM\OneToMany(targetEntity="MessageAttribute", mappedBy="message",cascade={"persist", "remove"}, orphanRemoval=true, indexBy="chiave")
     *
     */
    protected $attributes;
    
    /**
     * @ORM\ManyToMany(targetEntity="Client")
     * @ORM\JoinTable(name="message_clients",
     *      joinColumns={@ORM\JoinColumn(name="message_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="client_id", referencedColumnName="id")}
     *      )
     **/
    private $clients;
    
    public function __construct(){
    	$this->attributes = new ArrayCollection();
    	$this->clients = new ArrayCollection();
    	
    }
    
	/**
	 * @ORM\PrePersist()
	 *
	 */
	public function prePersist(){
        if(is_object($this->template))
		foreach ($this->template->getAttributes() as $attribute) {
			$attr = clone $attribute;
		//	print_r($attr);
		$attr->setMessage($this);
			$this->addMessageAttribute( $attr);
			
		}
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

    /**
     * Set cronstring
     *
     * @param string $cronstring
     */
    public function setCronstring($cronstring)
    {
        $this->cronstring = $cronstring;
    }

    /**
     * Get cronstring
     *
     * @return string 
     */
    public function getCronstring()
    {
        return $this->cronstring;
    }

    /**
     * Set enable
     *
     * @param boolean $enable
     */
    public function setEnable($enable)
    {
        $this->enable = $enable;
    }

    /**
     * Get enable
     *
     * @return boolean 
     */
    public function getEnable()
    {
        return $this->enable;
    }

    /**
     * Set push_all
     *
     * @param boolean $pushAll
     */
    public function setPushAll($pushAll)
    {
        $this->push_all = $pushAll;
    }

    /**
     * Get push_all
     *
     * @return boolean 
     */
    public function getPushAll()
    {
        return $this->push_all;
    }

    /**
     * Set start_time
     *
     * @param datetime $startTime
     */
    public function setStartTime($startTime)
    {
        $this->start_time = $startTime;
    }

    /**
     * Get start_time
     *
     * @return datetime 
     */
    public function getStartTime()
    {
        return $this->start_time;
    }

    /**
     * Set stop_time
     *
     * @param datetime $stopTime
     */
    public function setStopTime($stopTime)
    {
        $this->stop_time = $stopTime;
    }

    /**
     * Get stop_time
     *
     * @return datetime 
     */
    public function getStopTime()
    {
        return $this->stop_time;
    }

    /**
     * Set group
     *
     * @param Manticora\PushNotificationBundle\Entity\MessageGroup $group
     */
    public function setGroup(\Manticora\PushNotificationBundle\Entity\MessageGroup $group)
    {
        $this->group = $group;
    }

    /**
     * Get group
     *
     * @return Manticora\PushNotificationBundle\Entity\MessageGroup 
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set type
     *
     * @param Manticora\PushNotificationBundle\Entity\MessageType $type
     */
    public function setType(\Manticora\PushNotificationBundle\Entity\MessageType $type)
    {
        $this->type = $type;
    }

    /**
     * Get type
     *
     * @return Manticora\PushNotificationBundle\Entity\MessageType 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Add attributes
     *
     * @param Manticora\PushNotificationBundle\Entity\MessageAttribute $attributes
     */
    public function addMessageAttribute(\Manticora\PushNotificationBundle\Entity\MessageAttribute $attributes)
    {
        $this->attributes[] = $attributes;
    }

    /**
     * Get attributes
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
    
    public function setAttributes(\Doctrine\Common\Collections\Collection $attributes)
    {
    	foreach ($attributes as $attribute){
    		$attribute->setMessage($this);
    	}
    	
    	$this->attributes = $attributes;
    }

    /**
     * Add clients
     *
     * @param Manticora\PushNotificationBundle\Entity\Client $clients
     */
    public function addClient(\Manticora\PushNotificationBundle\Entity\Client $clients)
    {
        $this->clients[] = $clients;
    }

    /**
     * Get clients
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getClients()
    {
        return $this->clients;
    }
    
    public function setClients(\Doctrine\Common\Collections\Collection $clients)
    {
    	return $this->clients = $clients;
    }

    /**
     * Set active
     *
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set template
     *
     * @param Manticora\PushNotificationBundle\Entity\MessageTemplate $template
     */
    public function setTemplate(\Manticora\PushNotificationBundle\Entity\MessageTemplate $template)
    {
        $this->template = $template;
    }

    /**
     * Get template
     *
     * @return Manticora\PushNotificationBundle\Entity\MessageTemplate 
     */
    public function getTemplate()
    {
        return $this->template;
    }
}