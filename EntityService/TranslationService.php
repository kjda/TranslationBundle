<?php

namespace Kjda\TranslationBundle\EntityService;

use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator;
use Doctrine\ORM\EntityManager;
use Kjda\TranslationBundle\Entity as Entity;

class TranslationService {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository 
     */
    private $languageRepository;
    
    /**
     *
     * @var \Doctrine\Common\Persistence\ObjectRepository  
     */
    private $translationRepository;
    
    /**
     *
     * @var \Doctrine\Common\Persistence\ObjectRepository  
     */
    private $tokenRepository;
    
    /**
     *
     * @var \Doctrine\Common\Persistence\ObjectRepository 
     */
    private $domainRepository;

    /**
     * 
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em){
        $this->em = $em;
        $this->languageRepository = $this->em->getRepository('KjdaTranslationBundle:Language');
        $this->translationRepository = $this->em->getRepository('KjdaTranslationBundle:Translation');
        $this->tokenRepository = $this->em->getRepository('KjdaTranslationBundle:Token');
        $this->domainRepository = $this->em->getRepository('KjdaTranslationBundle:Domain');
    }

    /**
     * 
     * @param type $name
     * @param type $locale
     * @return \Kjda\TranslationBundle\Entity\Language
     */
    public function addLanguage($name, $locale) {

        $lang = new Entity\Language();
        $lang->setName($name);
        $lang->setLocale($locale);
        $this->saveEntity($lang);
        $this->fillMissingTranslations($lang);

        return $lang;
    }

    /**
     * 
     * @param object $entity
     */
    public function saveEntity($entity) {
        $this->em->persist($entity);
        $this->em->flush();
    }

    /**
     * 
     * @return array
     */
    public function getLanguages() {
        return $this->languageRepository->findAll();
    }

    /**
     * 
     * @param integer $id
     * @return \Kjda\TranslationBundle\Entity\Language
     */
    public function getLanguage($id) {
        return $this->languageRepository->find($id);
    }

    /**
     * 
     * @param integer $languageId
     * @return array
     * @throws \Exception
     */
    public function getLanguageTranslations($languageId) {
        $language = $this->languageRepository->findOneById($languageId);
        if (!$language) {
            throw new \Exception("Language does not exist");
        }
        return $this->translationRepository->findBy(array(
                    "language" => $language
                        ), null, 1000, 0);
    }

    /**
     * 
     * @param \Kjda\TranslationBundle\Entity\Language $lang
     */
    public function fillMissingTranslations(Entity\Language $lang) {
        $tokens = $this->tokenRepository->findAll();
        foreach ($tokens as $token) {
            $translation = $this->translationRepository->findOneBy(array(
                'language' => $lang,
                'token' => $token
            ));
            if (!$translation) {
                $this->newTranslation($lang, $token, $token->getToken());
            }
        }
    }

    /**
     * 
     * @param string $id
     * @param string $parameters
     * @param string $domain
     * @param string $locale
     * @return string
     */
    public function translate($id, array $parameters = array(), $domain, $locale) {
        $token = $this->findTokenOrCreateIt($id, $domain);
        $language = $this->languageRepository->findOneBy(array(
            'locale' => $locale
        ));
        
        $tr = $this->translationRepository->findOneBy(array(
            'language' => $language, 
            'token' => $token
        ));
        
        if (!$tr) {
            $this->newTranslation($language, $token, $id);
            return $id;
        }
        return $tr->getContent();
    }

    /**
     * 
     * @param \Kjda\TranslationBundle\Entity\Language $lang
     * @param \Kjda\TranslationBundle\Entity\Token $token
     * @param string $translation
     */
    public function newTranslation($lang, $token, $translation) {
        $t = new Entity\Translation;
        $t->setLanguage($lang)
                ->setToken($token)
                ->setContent($translation);
        $this->em->persist($t);
        $this->em->flush();
    }

    /**
     * 
     * @param string $translationId
     * @param string $content
     */
    public function editTranslation($translationId, $content) {
        $translation = $this->translationRepository->find($translationId);
        if ($translation) {
            $translation->setContent($content);
            $this->saveEntity($translation);
        }
    }

    /**
     * 
     * @param string $name
     * @return \Kjda\TranslationBundle\Entity\Domain
     */
    public function findDomainOrCreateIt($name) {
        if (!$name) {
            $name = 'messages';
            //return null;
        }
        $domain = $this->domainRepository->findOneBy(array('name' => $name));
        if (!$domain) {
            $domain = new Entity\Domain();
            $domain->setName($name);
            $this->saveEntity($domain);
        }
        return $domain;
    }

    /**
     * 
     * @param string $id
     * @param string $domain
     * @return \Kjda\TranslationBundle\Entity\Token
     */
    public function findTokenOrCreateIt($id, $domain = null) {
        $domain = $this->findDomainOrCreateIt($domain);
        $token = $this->tokenRepository->findOneBy(array('token' => $id, 'domain' => $domain));
        if (!$token) {
            $token = new Entity\Token();
            $token->setToken($id);
            $token->setDomain($domain);
            $this->saveEntity($token);
        }
        return $token;
    }

}
