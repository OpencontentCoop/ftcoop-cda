<?php
require 'autoload.php';

$cli = eZCLI::instance();
$script = eZScript::instance( array( 'description' => ( "Add codicefiscale as username" ),
    'use-session' => false,
    'use-modules' => true,
    'use-extensions' => true ) );

$script->startup();

$options = $script->getOptions('[list]',
    '',
    array(
        'list' => ''
    )
);
$script->initialize();
$script->setUseDebugAccumulators( true );

$db = eZDB::instance();
$output = new ezcConsoleOutput();

try
{
    $db = eZDB::instance();
    $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );

    $users = eZUser::fetchObjectList(eZUser::definition());
    $total = count($users);
    foreach ($users as $index => $user) {
        $index++;
        $object = $user->attribute('contentobject');

        if (!$options['list']){
            $cli->output("$index/$total " . $user->attribute('contentobject_id') . ' ' . $user->attribute('login') . ' ' . $object->attribute('name'));

            $opts = new ezcConsoleQuestionDialogOptions();
            $opts->text = "Inserisci nuovo username";
            $opts->validator = new ezcConsoleQuestionDialogTypeValidator(ezcConsoleQuestionDialogTypeValidator::TYPE_STRING, '');
            $opts->showResults = true;
            $question = new ezcConsoleQuestionDialog( $output, $opts );
            $username = ezcConsoleDialogViewer::displayDialog( $question );
            if (!empty(trim($username))){
                $cli->warning($username);
                $user->setAttribute( "login", trim($username) );
                $user->store();
            }

            $contentObjectAttribute = $object->dataMap()['user_account'];
            $contentObjectAttribute->setAttribute( 'data_text', '' );
            $contentObjectAttribute->store();
        }else{
            $cli->output($object->attribute('name') . ' -> ' . $user->attribute('login'));
        }

    }

    $script->shutdown();
}
catch( eZDBException $e )
{
    $errCode = $e->getCode();
    $errCode = $errCode != 0 ? $errCode : 1; // If an error has occured, script must terminate with a status other than 0
    $script->shutdown( $errCode, $e->getMessage() );
}
catch( Exception $e )
{
    $errCode = $e->getCode();
    $errCode = $errCode != 0 ? $errCode : 1; // If an error has occured, script must terminate with a status other than 0
    $script->shutdown( $errCode, $e->getMessage() );
}

$script->shutdown();