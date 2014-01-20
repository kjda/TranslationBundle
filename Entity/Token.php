<?php

namespace Kjda\TranslationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Token
 *
 * @ORM\Table(name="kjda_translation_token")
 * @ORM\Entity()
 */
class Token
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=255)
     * @Assert\NotBlank(message = "entity.token.empty")
     * @Assert\Length(min = "1", minMessage="entity.token.length")
     */
    private $token;

    /**
     * @var \Kjda\TranslationBundle\Entity\Domain
     *
     * @ORM\ManyToOne(targetEntity="Kjda\TranslationBundle\Entity\Domain", cascade={"all"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="domainId", referencedColumnName="id")
     * })
     */
    private $domain;
    

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
     * Set token
     *
     * @param string $token
     * @return Token
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
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
     * Set domain
     *
     * @param string $domain
     * @return Domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * Get domain
     *
     * @return string 
     */
    public function getDomain()
    {
        return $this->domain;
    }
    
    

}
