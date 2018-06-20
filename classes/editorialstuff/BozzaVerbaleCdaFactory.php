<?php

class BozzaVerbaleCdaFactory extends OCEditorialStuffPostDefaultFactory
{
    public function instancePost( $data )
    {
        return new BozzaVerbaleCda( $data, $this );
    }
    
    public function getTemplateDirectory()
    {
        return 'editorialstuff/bozze_verbali_cda';
    }
    
    public function editModuleResult( $parameters, OCEditorialStuffHandlerInterface $handler, eZModule $module )
    {        
        return parent::editModuleResult( $parameters, $handler, $module );    
    }
}