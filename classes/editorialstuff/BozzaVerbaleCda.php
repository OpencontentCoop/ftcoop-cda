<?php

class BozzaVerbaleCda extends OCEditorialStuffPostChangeStateDeferred
{
    public function attributes()
    {
        $attributes = parent::attributes();
        $attributes[] = 'available_from';
        $attributes[] = 'available_to';
        return $attributes;
    }
    
    public function tabs()
    {
        $currentUser = eZUser::currentUser();
        $templatePath = $this->getFactory()->getTemplateDirectory();
        $tabs = array(
            array(
                'identifier' => 'content',
                'name' => 'Contenuto',
                'template_uri' => "design:{$templatePath}/parts/content.tpl"
            )
        );
        if ( $this->object->attribute( 'can_edit' ) )
        {
            $tabs[] = array(
                'identifier' => 'history',
                'name' => 'Cronologia',
                'template_uri' => "design:{$templatePath}/parts/history.tpl"
            );
        }
        return $tabs;
    }
    
    public function attribute( $property )
    {
        if ( $property == 'available_from' )
        {            
            return $this->stringAttribute( 'available_from' );
        }
        if ( $property == 'available_to' )
        {            
            return $this->stringAttribute( 'available_to' );
        }
        return parent::attribute( $property );
    }
    
    public function needChangeToExpire()
    {
        $now = time();
        return  $this->is( 'expired' ) || ( $this->is( 'available' ) && $this->attribute( 'available_to' ) <= $now );
    }
    
    public function needChangeToAvailable()
    {
        $now = time();
        return $this->is( 'draft' ) && $this->attribute( 'available_from' ) <= $now;
    }
    
    public function setAvailable()
    {
        $this->setState( 'bozze_verbali_cda.available' );
    }
    
    public function setExpired()
    {
        //$this->setState( 'bozze_verbali_cda.expired' );
        $assignedNodeIDArray = array();
        $assignedNodes = $this->getObject()->attribute( 'assigned_nodes' );
        foreach( $assignedNodes as $node )
        {
            $assignedNodeIDArray[] = $node->attribute( 'node_id' );
        }
        eZContentObjectTreeNode::removeSubtrees( $assignedNodeIDArray, true );
    }
    
    protected function stringAttribute( $identifier, $callback = null )
    {
        $string = '';
        if ( isset( $this->dataMap[$identifier] ) )
        {
            $string = $this->dataMap[$identifier]->toString();
        }
        if ( is_callable( $callback ) )
        {
            return call_user_func( $callback, $string );
        }
        return $string;
    }
    
    
    public static function logVisit()
    {
        
        $http = eZHTTPTool::instance();
        $factory = $http->postVariable( 'Factory', false );
        $post = $http->postVariable( 'Post', false );
        $user = $http->postVariable( 'User', false );
        $start = $http->postVariable( 'Start', false );
        $end = $http->postVariable( 'End', false );
        
        $result = 'error';
        
        if ( $factory && $post && $user && $start && $end ){
            try
            {
                $currentPost = OCEditorialStuffHandler::instance( $factory, array() )->fetchByObjectId( $post );
                if ( $currentPost instanceof OCEditorialStuffPostInterface )
                {
                    OCEditorialStuffHistory::addHistoryToObjectId(
                        $currentPost->object->attribute( 'id' ),
                        'logvisit',
                        array( 'user_id' => $user, 'start' => $start, 'end' => $end )
                    );
                    $result = 'ok';
                }
            }
            catch ( Exception $e )
            {                
                eZDebug::writeNotice( $e->getMessage(), __FILE__ );
                $result = $e->getMessage();
            }
        }
        return $result;
    }
    
    public static function removeLogVisits($postId)
    {
        eZPersistentObject::removeObject( OCEditorialStuffHistory::definition(), array( 'object_id' => $postId, 'handler' => 'history', 'type' => 'logvisit' ) );        
    }
}