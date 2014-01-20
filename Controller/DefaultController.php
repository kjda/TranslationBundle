<?php

namespace Kjda\TranslationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller {

    /**
     * @Route("/", name="_kjda_translatoin_route_home")
     * @Template()
     */
    public function indexAction() {
        
        return array();
    }


    /**
     * @Route("/api/languages")
     * @Method("GET")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getLanguages() {

        $languages = $this->getService()->getLanguages();
        foreach($languages as $key => $language ){
            $languages[$key]  = $language->toArray();
        }
        return new JsonResponse($languages);
    }

    /**
     * @Route("/api/languages")
     * @Method({"POST"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function newLanguage() {
        try {
            $params = $this->getJsonRequestPayload();
            $service = $this->getService();
            $language = new \Kjda\TranslationBundle\Entity\Language;
            $language->setName($params['name'])->setLocale($params['locale']);
            $lang = $service->saveEntity($language);
            return new JsonResponse($lang->toArray());
        } catch (\Exception $e) {
            return new JsonResponse("Can not add language", 406);
        }
    }

    /**
     * @Route("/api/languages/{id}")
     * @Template()
     * @Method("GET")
     * @param integer $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function languageAction($id) {
        try {
            $language = $this->getService()->getLanguage($id);
            return new JsonResponse($language->toArray());
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), 406);
        }
    }

    /**
     * @Route("/api/translations/{languageId}")
     * @Template()
     */
    public function translationsAction($languageId) {
        try {
            $translations = $this->getService()->getLanguageTranslations($languageId);
            foreach ($translations as $key => $translation) {
                $translations[$key] = $translation->toArray();
            }
            return new JsonResponse($translations);
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), 406);
        }
        

        
    }

    /**
     * @Route("/api/translations/{languageId}/{translationId}")
     * @Template()
     * @Method("PUT")
     */
    public function translateAction($languageId, $translationId) {
        $params = $this->getJsonRequestPayload();
        try {
            $translation = $this->getService()
                    ->editTranslation($translationId, $params['content']);
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), 406);
        }
        return new JsonResponse($translation->toArray());
    }

    /**
     * 
     * @return array
     */
    private function getJsonRequestPayload() {
        $params = array();
        $content = $this->get('request')->getContent();
        if (!empty($content)) {
            $params = json_decode($content, true);
        }
        return $params;
    }

    /**
     * 
     * @return \Kjda\TranslationBundle\EntityService\TranslationService
     */
    protected function getService(){
        return $this->get('kjda_translation_service');
    }
}
