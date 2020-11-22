<?php

namespace Maxtoan\ToolsBundle\Controller\ObjectManager;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use FOS\RestBundle\Util\Codes;
use Maxtoan\ToolsBundle\Form\ObjectManager\ExporterManager\DocumentsType;
use Maxtoan\ToolsBundle\Component\HttpFoundation\JsonResponse;

/**
 * Controlador para exportar los documentos
 *
 * @author Máximo Sojo <maxsojo13@gmail.com>
 */
class ExporterManagerController extends ManagerController
{
    /**
     * Genera un documento
     * @param   Request  $request
     */ 
    public function generateAction(Request $request) 
    {
        $objectDataManager = $this->getObjectDataManager($request);
        $chain = $objectDataManager->exporters()->resolveChainModel();
        $choices = [];
        $models = $chain->getModels();
        foreach ($models as $model) {
            $choices[$model->getName()] = $model->getName();
        }
        $form = $this->createForm(DocumentsType::class,$choices);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $options = [
                "fileName" => $request->get("fileName")
            ];
            $name = $form->get("name")->getData();
            $objectDataManager->exporters()->generateWithSource($name,$options);
        }
        
        return new JsonResponse([]);
        // return $this->toReturnUrl();
    }
}
