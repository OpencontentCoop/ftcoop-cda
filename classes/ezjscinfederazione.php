<?php

class ezjscInFederazione extends ezjscServerFunctions
{
	static public function create($args)
    {
    	$id= $args[0];
    	$response = false;
    	$object = eZContentObject::fetch((int)$id);
    	if ($object instanceof eZContentObject && $object->canEdit() && in_array($object->attribute('class_identifier'), eZUser::fetchUserClassNames())) {
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

        echo json_encode($response);
        eZExecution::cleanExit();
    }

    static public function exists($args)
    {
    	$id = $args[0];
    	$response = [
        	'codiceFiscale' => false,
        	'exists' => false
        ];
    	$object = eZContentObject::fetch((int)$id);
    	if ($object instanceof eZContentObject && $object->canEdit() && in_array($object->attribute('class_identifier'), eZUser::fetchUserClassNames())) {
            $user = eZUser::fetch($object->attribute('id'));
            if ($user instanceof eZUser){                        
                $response = [
                	'codiceFiscale' => $user->attribute('login'),
                	'exists' => InFederazione::userExists($user->attribute('login'))
                ];
            }
        }

        echo json_encode($response);
        eZExecution::cleanExit();
    }
}