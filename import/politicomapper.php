<?php

class PoliticoMapper
{
    public function mapClassIdentifier($identifier)
    {
        return 'user';
    }

    public function mapFieldIdentifier($identifier)
    {
        $mapped = null;
        switch ($identifier){

            case 'nome':
                $mapped = 'nome';
                break;

            case 'cognome':
                $mapped = 'cognome';
                break;

            case 'account':
                $mapped = 'user_account';
                break;

            case 'email':
                $mapped = 'altre_email';
                break;

            case 'image':
                $mapped = 'image';
                break;
        }

        return $mapped;
    }
}