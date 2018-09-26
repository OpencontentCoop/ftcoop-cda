<?php

class FTCoopOpenPAConsiglioConfiguration extends OpenPAConsiglioConfiguration
{
    public function getSyncClassRemoteHost()
    {
        return 'http://dev.ftcoop.opencontent.it';
    }

    public function useApp()
    {
        return false;
    }

    public function enableVotazioniinCruscotto()
    {
        return false;
    }

    public function getCurrentSiteaccessIdentifier()
    {
        return 'ftcoop';
    }

    public function getRepositoryRootRemoteId($repositoryIdentifier)
    {
        return 'openpa_consiglio_' . $repositoryIdentifier;
    }

    public function getRepositoryRootNodeId($repositoryIdentifier)
    {
        $remote = $this->getRepositoryRootRemoteId($repositoryIdentifier);
        if ($object = eZContentObject::fetchByRemoteID($remote)){
            return $object->attribute('main_node_id');
        }
        return null;
    }

    public function getRepositoryRootNodePathString($repositoryIdentifier)
    {
        $remote = $this->getRepositoryRootRemoteId($repositoryIdentifier);
        if ($object = eZContentObject::fetchByRemoteID($remote)){
            return $object->attribute('main_node')->attribute('path_string');
        }
        return null;
    }

    public function getRepositoryPersistentVariable($repositoryIdentifier)
    {
        return array(
            'top_menu' => true,
            'topmenu_template_uri' => 'design:consiglio/page_topmenu.tpl'
        );
    }

    public function getAlertsContainerNodeId()
    {
        /*
        {def $alerts_container = openpaini( 'OpenPAConsiglio', 'DashboardAlertsContainerNode', false() )} //@todo
         */
        return 0;
    }

    public function getSocketInfo()
    {
        /*
        {openpaini('OpenPAConsiglio','SocketUrl','cal')}
        {openpaini('OpenPAConsiglio','SocketPort','8090')}
        */
        // return array(
        //     'url' => 'devnginx2.opencontent.it',
        //     'port' => 8091,
        //     'js_url' => 'devnginx2.opencontent.it:8091',
        // );
        return array(
            'url' => 'ftcoop-cda.opencontent.it',
            'port' => 8091,
            'js_url' => 'ftcoop-cda.opencontent.it',
        );
    }

    public function getBackendEndPoint()
    {
        //OpenPAINI::variable( 'OpenPAConsiglio', 'BackendEndPoint' )
        return false;
    }

    public function isMailDebug()
    {
        //OpenPAINI::variable( 'OpenPAConsiglio', 'UseMailDebug', 'true' )
        return true;
    }

    public function getMailDebugAdress()
    {
        //OpenPAINI::variable( 'OpenPAConsiglio', 'UseMailDebugAddress', 'lr@opencontent.it' )
        return 'lr@opencontent.it';
    }

    public function getAvailableClasses()
    {
        return array(
            'allegato_seduta',
            'openpa_consiglio_collaboration_area',
            'openpa_consiglio_collaboration_room',
            'openpa_consiglio_collaboration_comment',
            'openpa_consiglio_collaboration_file',
            'convocazione_seduta',
            'invitato',
            'invito',
            'materia',
            'osservazione',
            'punto',
            'seduta',
            'votazione',
            'organo_sociale',
            'rendiconto_spese',
            'proposta_punto',
        );
    }

    public function getContainerDashboards()
    {
        return array(
            'seduta' => 'folder',
            //'punto' => 'folder',
            'allegati_seduta' => 'folder',
            'invitato' => 'user_group',
            //'invito' => 'folder',
            'politico' => 'user_group',
            'tecnico' => 'user_group',
            'materia' => 'folder',
            'organo' => 'folder',
            //'convocazione_seduta' => 'folder',
            'votazione' => 'folder',
            'osservazioni' => 'folder',
            'referentelocale' => 'user_group',
            'areacollaborativa' => 'folder',
            'rendiconto_spese' => 'folder', // non è una dashboard
            'proposta' => 'folder',
            'responsabilearea' => 'user_group',
            'cda_evento' => 'folder',
            'cda_documento' => 'folder',
        );
    }

    public function getActiveDashboards()
    {
        return array(
            'seduta' => true,
            'materia' => true,
            'invitato' => true,
            'referentelocale' => true,
            //'areacollaborativa' => true,
            'organo' => true,
            'tecnico' => true,
            'politico' => true,
            //'proposta' => true,
            'responsabilearea' => true,
            'cda_evento' => true,
            'cda_documento' => true,
        );
    }

    public function calcolaImportGettone($percentuale)
    {
        $importoMassimo = 120;
        $base = 0;
        if ( $percentuale > 0 )
        {
            $base = 100;
        }

        return number_format( ( intval( $base ) * $importoMassimo / 100 ), 2 );
    }

    public function calcolaLivelloGettone($percentuale)
    {
        // if ($percentuale >= 75) {
        //     return 'success';
        // } elseif ($percentuale >= 25) {
        //     return 'warning';
        // } else {
        //     return 'danger';
        // }

        if ($percentuale > 0) {
            return 'success';
        } else {
            return 'danger';
        }
    }

    public function siteTitle()
    {
        return eZINI::instance()->variable( 'SiteSettings', 'SiteName' );
    }

    public function siteUrl()
    {
        return "https://www.cooperazionetrentina.it/cda";
    }

    public function assetUrl()
    {
        return "https://www.cooperazionetrentina.it";
    }

    public function logoPath()
    {
        return 'extension/ftcoop-cda/design/cda/images/logo.png';
    }

    public function logoTitle()
    {
        return $this->siteTitle();
    }

    public function logoSubtitle()
    {
        return '';
    }

    public function headImages()
    {
        return array();
    }

    public function needLogin()
    {
        return false;
    }

    public function attributeContacts()
    {
        return false;
    }

    public function attributeFooter()
    {
        return false;
    }

    public function textCredits()
    {
        return false;
    }

    public function googleAnalyticsId()
    {
        return false;
    }

    public function cookieLawUrl()
    {
        return false;
    }

    public function menu()
    {
        return array();
    }

    public function userMenu()
    {
        return array();
    }

    public function bannerPath()
    {
        return false;
    }

    public function bannerTitle()
    {
        return false;
    }

    public function bannerSubtitle()
    {
        return false;
    }
}