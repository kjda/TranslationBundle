<?php

namespace Kjda\TranslationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * Language
 *
 * @ORM\Table(name="kjda_translation_language")
 * @ORM\Entity()
 * @UniqueEntity("name", message="Language exists already")
 * @UniqueEntity("locale", message="Locale exists already")
 */
class Language
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
     * @ORM\Column(name="name", type="string", length=64, unique=true)
     * @Assert\NotBlank(message = "entity.lang.name.empty")
     * @Assert\Length(min = "1", minMessage="entity.lang.name.length")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="locale", type="string", length=12, unique=true)
     * @Assert\NotBlank(message="entity.lang.locale.empty")
     * @Assert\Length(max="12", maxMessage="entity.lang.locale.length")
     */
    private $locale;


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
     * @return Language
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
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
     * Set locale
     *
     * @param string $locale
     * @return Language
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get locale
     *
     * @return string 
     */
    public function getLocale()
    {
        return $this->locale;
    }

    
    
    public function toArray(){
      return array(
          'id' => $this->id,
          'name' => $this->name,
          'locale' => $this->locale
      );
    }
}
