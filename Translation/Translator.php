<?php

namespace Kjda\TranslationBundle\Translation;

use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

use Kjda\TranslationBundle\EntityService\TranslationService;

/**
 * Translator.
 *
 * @author Khaled Jouda <khaled.jouda@gmail.com>
 *
 * @api
 */
class Translator extends \Symfony\Component\Translation\Translator {

  /**
   *
   * @var \Kjda\TranslationBundle\EntityService\TranslationService
   */
  private $translationService = null;

  /**
   * {@inheritdoc}
   *
   * @api
   */
  public function trans($id, array $parameters = array(), $domain = null, $locale = null) {

    if (!$locale) {
      $locale = $this->getLocale();
    }
    return $this->translationService->translate($id, $parameters, $domain, $locale);
  }

  /**
   * {@inheritdoc}
   *
   * @api
   */
  public function transChoice($id, $number, array $parameters = array(), $domain = null, $locale = null) {
    return $id;
  }

  public function setTranslationService(TranslationService $service) {
    $this->translationService = $service;
  }

  public function getTranslationService(){
    return $this->translationService;
  }

  /**
   * {@inheritdoc}
   *
   * @api
   */
  public function setLocale($locale) {

    parent::setLocale($locale);
  }

}
