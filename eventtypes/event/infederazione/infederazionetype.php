<?php

class InFederazioneType extends eZWorkflowEventType
{
    const WORKFLOW_TYPE_STRING = "infederazione";

    public function __construct()
    {
        parent::__construct(InFederazioneType ::WORKFLOW_TYPE_STRING, 'Workflow InFederazione');
    }

    /**
     * @param eZWorkflowProcess $process
     * @param eZEvent $event
     *
     * @return int
     */
    public function execute($process, $event)
    {
        $parameters = $process->attribute('parameter_list');
        if ($parameters['trigger_name'] == 'post_publish') {
            if (isset($parameters['object_id'], $parameters['version'])) {
                $object = eZContentObject::fetch($parameters['object_id']);
                if ($object instanceof eZContentObject && $parameters['version'] == 1 && in_array($object->attribute('class_identifier'), eZUser::fetchUserClassNames())) {
                    $user = eZUser::fetch($object->attribute('id'));
                    if ($user instanceof eZUser){                        
                        $dataMap = $object->dataMap();
                        $nome = isset($dataMap['nome']) && $dataMap['nome']->hasContent() ? $dataMap['nome']->toString() : $object->attribute('class_identifier');
                        $cognome = isset($dataMap['cognome']) && $dataMap['cognome']->hasContent() ? $dataMap['cognome']->toString() : $object->attribute('id');
                        $userData = [
                            "codiceFiscale" => $user->attribute('login'),
                            "nome" => $nome,
                            "cognome" => $cognome,
                            "password" => "P@ssword01",
                            "eMail" => strtolower($user->attribute('email'))
                        ];
                        $response = InFederazione::createUser($userData);
                        eZLog::write(var_export($userData, true) . ' ' . var_export($response, true), 'infederazione_error.log');
                    }
                }
            }
        }
        return eZWorkflowType::STATUS_ACCEPTED;
    }

    private function pushUser($userData)
    {
        
    }
}

eZWorkflowEventType::registerEventType(InFederazioneType ::WORKFLOW_TYPE_STRING, 'InFederazioneType');

