<?php


use TheNetworg\OAuth2\Client\Provider\Azure as BaseAzure;
use Firebase\JWT\JWT;
use League\OAuth2\Client\Grant\AbstractGrant;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;
use TheNetworg\OAuth2\Client\Grant\JwtBearer;
use TheNetworg\OAuth2\Client\Token\AccessToken;

class Azure extends BaseAzure
{
    use BearerAuthorizationTrait;

    public $urlLogin = 'https://login.microsoftonline.com/';

    public $pathAuthorize = '/oauth2/authorize';

    public $pathToken = '/oauth2/token';

    public $scope = [];

    public $scopeSeparator = ' ';

    public $tenant = 'common';

    public $urlAPI = 'https://graph.windows.net/';

    public $resource = '';

    public $API_VERSION = '1.6';

    public $authWithResource = true;

    public $policy = 'b2c_1_sign_in';

    /**
     * Obtain URL for logging out the user.
     *
     * @param $post_logout_redirect_uri string The URL which the user should be redirected to after logout
     *
     * @return string
     */
    public function getLogoutUrl($post_logout_redirect_uri)
    {
        return 'https://login.microsoftonline.com/' . $this->tenant . '/oauth2/logout?post_logout_redirect_uri=' . rawurlencode($post_logout_redirect_uri);
    }

    /**
     * Get JWT verification keys from Azure Active Directory.
     *
     * @return array
     */
    public function getJwtVerificationKeys()
    {
        $factory = $this->getRequestFactory();
        $request = $factory->getRequestWithOptions('get', $this->urlLogin . $this->tenant . '/' . $this->policy . '/discovery/v2.0/keys', []);

        $response = $this->getParsedResponse($request);

        $e = $this->convertBase64urlToBase64($response['keys'][0]['e']);
        $n = $this->convertBase64urlToBase64($response['keys'][0]['n']);

        $rsa = new Crypt_RSA();
        $rsa->setPublicKey('<RSAKeyValue><Modulus>' . $n . '</Modulus><Exponent>' . $e . '</Exponent></RSAKeyValue>');
        $publicKey = $rsa->getPublicKey();

        return $publicKey;
    }

    private function convertBase64urlToBase64($input) 
    {
            
        $padding = strlen($input) % 4;
        if ($padding > 0) {
            $input .= str_repeat("=", 4 - $padding);
        }
        return strtr($input, '-_', '+/');
    }

    /**
     * Get the specified tenant's details.
     *
     * @param string $tenant
     *
     * @return array
     */
    public function getTenantDetails($tenant)
    {
        $factory = $this->getRequestFactory();
        $request = $factory->getRequestWithOptions(
            'get',
            $this->urlLogin . $tenant . '/' . $this->policy . '/v2.0/.well-known/openid-configuration',
            []
        );

        $response = $this->getParsedResponse($request);

        return $response;
    }
}
