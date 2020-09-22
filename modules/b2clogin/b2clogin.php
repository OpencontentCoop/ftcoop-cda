<?php

use TheNetworg\OAuth2\Client\Provider\AzureResourceOwner;

/** @var array $Params */
/** @var eZModule $module */
$module = $Params['Module'];
$http = eZHTTPTool::instance();
eZSession::start();

$provider = new Azure([
    'clientId' => '42c20691-491f-4bd2-b344-cb10263fa8fb',
    'clientSecret' => '',
    'redirectUri' => 'https://riuniamoci.cooperazionetrentina.it/b2clogin',
    'tenant' => 'fedcooptn.onmicrosoft.com',
    'scope' => 'openid',
    'urlLogin' => 'https://FedCoopTn.b2clogin.com/',
    'pathAuthorize' => '/oauth2/v2.0/authorize',
    'pathToken' => '/oauth2/v2.0/token',
    'policy' => 'B2C_1A_PortaleCda_SI',
]);


$request = (array)$_POST;

if (!isset($request['code'])) {

    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl([
        'p' => $provider->policy,
        'response_type' => 'code id_token',
        'response_mode' => 'form_post',
        'nonce' => 'defaultNonce'
    ]);
    $http->setSessionVariable('oauth2state', $provider->getState());    
    eZLog::write('Set state ' . $provider->getState(), 'b2clogin.log');

    header('Location: ' . $authUrl);
    eZExecution::cleanExit();

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($request['state']) || ($request['state'] !== $http->sessionVariable('oauth2state'))) {

    eZLog::write('Invalid state ' . $http->sessionVariable('oauth2state'), 'b2clogin.log');
    $http->removeSessionVariable('oauth2state');
    return $module->handleError(eZError::KERNEL_ACCESS_DENIED, 'kernel', array(), array('Error', 'Invalid state'));

} else {

    // Optional: Now you have a token you can look up a users profile data
    try {

        $token = $request['id_token'];
        $claims = $provider->validateAccessToken($token);

        $azureUser = new AzureResourceOwner($claims);
        $codicefiscale = $azureUser->claim('Codicefiscale');

        $user = eZUser::fetchByName($codicefiscale);
        if ($user instanceof eZUser) {
            
            eZLog::write("User $codicefiscale found", 'b2clogin.log');

            $userID = $user->attribute('contentobject_id');
            // if audit is enabled logins should be logged
            eZAudit::writeAudit('user-login', array('User id' => $userID, 'User login' => $user->attribute('login')));
            eZUser::updateLastVisit($userID, true);
            eZUser::setCurrentlyLoggedInUser($user, $userID);
            // Reset number of failed login attempts
            eZUser::setFailedLoginAttempts($userID, 0);
            eZHTTPTool::instance()->setSessionVariable('B2LoginUserLoggedIn', true);

            $ini = eZINI::instance();
            $redirectionURI = '/';
            if (is_object($user)) {
                $userUriAttrName = '';
                $groupUriAttrName = '';
                if ($ini->hasVariable('UserSettings', 'LoginRedirectionUriAttribute')) {
                    $uriAttrNames = $ini->variable('UserSettings', 'LoginRedirectionUriAttribute');
                    if (is_array($uriAttrNames)) {
                        if (isset($uriAttrNames['user'])) {
                            $userUriAttrName = $uriAttrNames['user'];
                        }

                        if (isset($uriAttrNames['group'])) {
                            $groupUriAttrName = $uriAttrNames['group'];
                        }
                    }
                }
                $userObject = $user->attribute('contentobject');
                // 1. Check if redirection URI is specified for the user
                $userUriSpecified = false;
                if ($userUriAttrName) {
                    /** @var eZContentObjectAttribute[] $userDataMap */
                    $userDataMap = $userObject->attribute('data_map');
                    if (!isset($userDataMap[$userUriAttrName])) {
                        eZLog::write(
                            "Cannot find redirection URI: there is no attribute '$userUriAttrName' in object '" . $userObject->attribute('name') . "' of class '" . $userObject->attribute('class_name') . "'.",
                            'b2clogin.log'
                        );
                    } elseif (($uriAttribute = $userDataMap[$userUriAttrName])
                        && ($uri = $uriAttribute->attribute('content'))) {
                        $redirectionURI = $uri;
                        $userUriSpecified = true;
                    }
                }
                // 2.Check if redirection URI is specified for at least one of the user's groups (preferring main parent group).
                if (!$userUriSpecified && $groupUriAttrName && $user->hasAttribute('groups')) {
                    $groups = $user->attribute('groups');
                    if (isset($groups) && is_array($groups)) {
                        $chosenGroupURI = '';
                        foreach ($groups as $groupID) {
                            $group = eZContentObject::fetch($groupID);
                            /** @var eZContentObjectAttribute[] $groupDataMap */
                            $groupDataMap = $group->attribute('data_map');
                            $isMainParent = ($group->attribute('main_node_id') == $userObject->attribute('main_parent_node_id'));

                            if (!isset($groupDataMap[$groupUriAttrName])) {
                                eZLog::write(
                                    "Cannot find redirection URI: there is no attribute '$groupUriAttrName' in object '" . $group->attribute('name') . "' of class '" . $group->attribute('class_name') . "'.",
                                    'b2clogin.log'
                                );
                                continue;
                            }
                            $uri = $groupDataMap[$groupUriAttrName]->attribute('content');
                            if ($uri) {
                                if ($isMainParent) {
                                    $chosenGroupURI = $uri;
                                    break;
                                } elseif (!$chosenGroupURI) {
                                    $chosenGroupURI = $uri;
                                }
                            }
                        }
                        if ($chosenGroupURI) // if we've chose an URI from one of the user's groups.
                        {
                            $redirectionURI = $chosenGroupURI;
                        }
                    }
                }
            }

            return $module->redirectTo($redirectionURI);
        
        }else{
            eZLog::write("User $codicefiscale not found " .  $azureUser->claim('Denominazione'), 'b2clogin.log');

            $tpl = eZTemplate::factory();
            $tpl->setVariable('name', $azureUser->claim('Denominazione'));
            $Result = array();
            $Result['content'] = $tpl->fetch( 'design:user/user_not_registered.tpl' );
            $Result['path'] = array( array( 'text' => 'Login Federazione' , 'url' => false ) );

            //return $module->handleError(eZError::KERNEL_NOT_FOUND, 'kernel', array(), array('Error', 'User not found'));
        }

    } catch (Exception $e) {

        eZLog::write($e->getMessage() . $e->getTraceAsString(), 'b2clogin.log');
        return $module->handleError(eZError::KERNEL_ACCESS_DENIED, 'kernel', array(), array('Error', $e->getMessage()));
    }

}