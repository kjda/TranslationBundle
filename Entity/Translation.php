<?php

namespace Kjda\TranslationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Translation
 *
 * @ORM\Table(name="kjda_translation_translation", uniqueConstraints={@ORM\UniqueConstraint(name="language_token", columns={"languageId", "tokenId"})})
 * @ORM\Entity()
 */
class Translation
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
     * @var text
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;
    
    
    /**
     * @var \Kjda\TranslationBundle\Entity\Language
     *
     * @ORM\ManyToOne(targetEntity="Kjda\TranslationBundle\Entity\Language", cascade={"all"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="languageId", referencedColumnName="id")
     * })
     */
    private $language;
    
    
    
    /**
     * @var \Kjda\TranslationBundle\Entity\Token
     *
     * @ORM\ManyToOne(targetEntity="Kjda\TranslationBundle\Entity\Token", cascade={"all"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tokenId", referencedColumnName="id")
     * })
     */
    private $token;
    
    
    
    

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
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
    
    /**
     * Set content
     *
     * @param string  $content
     * @return Token
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }
    
    
    /**
     * Get language
     *
     * @return \Kjda\TranslationBundle\Language 
     */
    public function getLanguage()
    {
        return $this->language;
    }
    
    /**
     * Set language
     *
     * @param \Kjda\TranslationBundle\Language  $language
     * @return Token
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }
    
    
    /**
     * Get token
     *
     * @return \Kjda\TranslationBundle\Token 
     */
    public function getToken()
    {
        return $this->token;
    }
    
    /**
     * Set token
     *
     * @param \Kjda\TranslationBundle\Token  $token
     * @return Token
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }
    
    public function toArray(){
        $token = $this->token;
        $domain = $token->getDomain();
        if( $domain ){
            $domain = $domain->getName();
        }
      return array(
          'id' => $this->id,
          'content' => $this->content,
          'tokenId' => $this->token->getId(),
          'token' => $token->getToken(),
          'domain' => $domain,
          'language' => $this->language->toArray(),
          
      );
    }
}
