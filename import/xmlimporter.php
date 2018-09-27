<?php

/*
[ImportSettings]
AvailableSourceHandlers[]=xmlimporter

[xmlimporter-HandlerSettings]
Enabled=true
Name=OCExportAs XML Import Handler
ClassName=OCExportasXMLImporter
DefaultParentNodeID=2
*/


class OCExportasXMLImporter extends SQLIImportAbstractHandler implements ISQLIImportHandler
{
    protected $rowIndex = 0;

    protected $rowCount;

    protected $currentGUID;

    protected $remoteUrl;

    protected $parentNodeId;

    protected $errors;

    protected $contentClass;

    protected $remoteIdErrorList = array();

    protected $debug;

    protected $mapper;

    /**
     * (non-PHPdoc)
     * @see extension/sqliimport/classes/sourcehandlers/ISQLIImportHandler::initialize()
     */
    public function initialize()
    {
        $user = eZUser::fetchByName('admin');
        eZUser::setCurrentlyLoggedInUser($user, $user->attribute('contentobject_id'));

        $file = null;
        if (isset($this->options['file'])) {
            $file = eZSys::rootDir() . '/' . $this->options['file'];

        } elseif (isset($this->options['object'])) {
            $object = eZContentObject::fetch((int)$this->options['object']);
            if ($object instanceof eZContentObject) {
                $dataMap = $object->dataMap();
                if (isset($dataMap['file'])) {
                    $binary = $dataMap['file']->content();
                    if ($binary instanceof eZBinaryFile) {
                        $file = eZSys::rootDir() . '/' . $binary->filePath();
                    }
                }
            }
        }

        $xmlOptions = new SQLIXMLOptions(array(
            'xml_path' => $file,
            'xml_parser' => 'simplexml'
        ));
        $xmlParser = new SQLIXMLParser($xmlOptions);
        $this->dataSource = $xmlParser->parse();

        $this->remoteUrl = $this->options['url'];

        if (isset($this->options['mapper']) && class_exists($this->options['mapper'])){
            $this->mapper = new $this->options['mapper'];
        }

        $this->parentNodeId = $this->options['parent'];

        $db = eZDB::instance();
        $db->setErrorHandling(eZDB::ERROR_HANDLING_EXCEPTIONS);
    }

    /**
     * (non-PHPdoc)
     * @see extension/sqliimport/classes/sourcehandlers/ISQLIImportHandler::getProcessLength()
     */
    public function getProcessLength()
    {
        if (!isset($this->rowCount)) {
            $this->rowCount = count($this->dataSource->object);
        }

        return $this->rowCount;
    }

    /**
     * (non-PHPdoc)
     * @see extension/sqliimport/classes/sourcehandlers/ISQLIImportHandler::getNextRow()
     */
    public function getNextRow()
    {
        if ($this->rowIndex < $this->rowCount) {
            $row = $this->dataSource->object[$this->rowIndex];
            $this->rowIndex++;
        } else {
            $row = false; // We must return false if we already processed all rows
        }

        return $row;
    }

    /**
     * (non-PHPdoc)
     * @see extension/sqliimport/classes/sourcehandlers/ISQLIImportHandler::process()
     */
    public function process($row)
    {
        $attributes = $row->attributes();

        $this->currentGUID = (string)$attributes['remote_id'];

        $factory = str_replace('.xml', '', basename($this->options['file']));
        $factoryRootNodeId = OpenPAConsiglioConfiguration::instance()->getRepositoryRootNodeId($factory);

        try {
            $contentOptions = new SQLIContentOptions(array(
                'class_identifier' => $this->mapClassIdentifier($attributes['class_identifier']),
                'remote_id' => (string)$this->currentGUID
            ));

            if ($currentObject = $this->alreadyImported($contentOptions)) {
                //do something...
            }

            $content = $this->createContent($contentOptions);

            foreach ($row->attribute as $element) {
                try {
                    $this->setFieldContent($content, $element);
                } catch (Exception $e) {
                    $this->addError($e->getMessage());
                }
            }

            if ($content instanceof SQLIContent) {
                if ($content->isNew()) {
                    $content->addLocation(SQLILocation::fromNodeID($this->parentNodeId));
                }

                if ($factoryRootNodeId && $factoryRootNodeId != $this->parentNodeId){
                    $content->addLocation(SQLILocation::fromNodeID($factoryRootNodeId));
                }

                $publisher = SQLIContentPublisher::getInstance();
                $publisher->publish($content);

                $object = $content->getRawContentObject();
                $object->setAttribute('published', $attributes['published']);
                $object->store();
            } else {
                print_r($content);
                print_r($this->errors);
                die();
            }

            // Free some memory. Internal methods eZContentObject::clearCache() and eZContentObject::resetDataMap() will be called
            // @see SQLIContent::__destruct()
            unset($content);

        } catch (eZDBException $e) {
            $this->addError($e->getMessage());
            $this->remoteIdErrorList[] = "'{$this->currentGUID}'";
            eZDB::instance()->rollback();
        } catch (Exception $e) {
            $this->addError($e->getMessage());
        }
    }

    protected function alreadyImported($contentOptions)
    {
        return eZContentObject::fetchByRemoteID($contentOptions['remote_id']);
    }

    protected function createContent($contentOptions)
    {
        if ($this->debug) {
            $content = new stdClass();
            $content->fields = new stdClass();
            if (!$this->contentClass) {
                $this->contentClass = eZContentClass::fetchByIdentifier($contentOptions['class_identifier']);
            }
            foreach ($this->contentClass->dataMap() as $classAttribute) {
                $content->fields->{$classAttribute->attribute('identifier')} = '';
            }
        } else {
            $content = SQLIContent::create($contentOptions);
        }

        return $content;
    }

    protected function mapClassIdentifier($identifier)
    {
        if ($this->mapper){
            return $this->mapper->mapClassIdentifier($identifier);
        }
        return $identifier;
    }

    protected function mapFieldIdentifier($identifier)
    {
        if ($this->mapper){
            return $this->mapper->mapFieldIdentifier($identifier);
        }
        return $identifier;
    }

    private function setFieldContent($content, SimpleXMLElement $element)
    {
        $attributes = $element->attributes();
        $identifier = $this->mapFieldIdentifier($attributes['contentclass_attribute_identifier']);

        if ($this->debug) {
            $this->cli->output($attributes['contentclass_attribute_identifier'] . ' -> ' . $identifier);
        }

        if (!$identifier)
            return;

        $dataType = $attributes['data_type_string'];
        $hasContent = (bool)($attributes['has_content'] == '1');
        if (isset($content->fields->{$identifier})) {
            if ($hasContent) {

                switch ($dataType) {
                    case 'ezimage':
                        $url = rtrim($this->remoteUrl, '/') . '/' . $attributes['full_path'];
                        $content->fields->{$identifier} = SQLIContentUtils::getRemoteFile($url);
                        break;

                    case 'ezbinaryfile':
                        $filepath = str_replace('{eZSys::hostname()}', '', $attributes['filepath']);
                        $url = rtrim($this->remoteUrl, '/') . $filepath;
                        $content->fields->{$identifier} = SQLIContentUtils::getRemoteFile($url);
                        break;

                    case 'ezxmltext':
                        //$content->fields->{$identifier} = SQLIContentUtils::getRichContent((string)$element);
                        $content->fields->{$identifier} = (string)$element;
                        break;

                    case 'ezobjectrelationlist':
                    case 'ezobjectrelation':
                        $list = array();
                        foreach ($element->object as $related) {
                            $relatedAttributes = $related->attributes();
                            $object = eZContentObject::fetchByRemoteID($relatedAttributes['remote_id']);
                            if ($object instanceof eZContentObject) {
                                $list[] = $object->attribute('id');
                            }
                        }
                        $content->fields->{$identifier} = implode('-', $list);
                        break;

                    case 'ezstring':
                    case 'eztext':
                        $content->fields->{$identifier} = (string)$element;
                        break;

                    case 'ezselection':
                        $list = array();
                        foreach ($element->item as $item) {
                            $itemAttributes = $item->attributes();
                            $list[] = $itemAttributes['content'];
                        }
                        $content->fields->{$identifier} = implode('|', $list);
                        break;

                    default:
                        if (isset($attributes['content']))
                            $content->fields->{$identifier} = (string)$attributes['content'];
                        else
                            $content->fields->{$identifier} = (string)$element;
                        break;
                }
            } else {
                if ($dataType == 'ezimage' && $content->fields->{$identifier} instanceof SQLIContentField) {
                    $contentObjectAttribute = $content->fields->{$identifier}->getRawAttribute();
                    /** @var eZImageAliasHandler $imageHandler */
                    $imageHandler = $contentObjectAttribute->attribute('content');
                    if ($imageHandler) {
                        $imageHandler->setAttribute('alternative_text', false);
                        $imageHandler->removeAliases();
                        $imageHandler->store($contentObjectAttribute);
                    }
                }
                //$content->fields->{$identifier} = null;
            }
        } else {
            $this->addError("Field $identifier ($dataType) not found");
        }
    }

    private function addError($error)
    {
        eZLog::write("[{$this->currentGUID}] $error", 'xmlimport.log');
        $this->errors[$this->currentGUID][] = $error;
    }

    /**
     * (non-PHPdoc)
     * @see extension/sqliimport/classes/sourcehandlers/ISQLIImportHandler::cleanup()
     */
    public function cleanup()
    {
        // foreach ($this->errors as $id => $errors) {
        // 	$this->cli->output($id);
        // 	foreach ($errors as $error) {
        // 		$this->cli->output('  - ' . $error);
        // 	}        	
        // }

        if (count($this->remoteIdErrorList)) {
            $db = eZDB::instance();
            $query = "DELETE FROM ezcontentobject WHERE " . $db->generateSQLINStatement($this->remoteIdErrorList,
                    'remote_id');
            //$db->query($query);
            var_dump($query);
        }

        return;
    }

    /**
     * (non-PHPdoc)
     * @see extension/sqliimport/classes/sourcehandlers/ISQLIImportHandler::getHandlerName()
     */
    public function getHandlerName()
    {
        return 'OCExportAs XML Import Handler';
    }

    /**
     * (non-PHPdoc)
     * @see extension/sqliimport/classes/sourcehandlers/ISQLIImportHandler::getHandlerIdentifier()
     */
    public function getHandlerIdentifier()
    {
        return 'ocexportasxmlimporthandler';
    }

    /**
     * (non-PHPdoc)
     * @see extension/sqliimport/classes/sourcehandlers/ISQLIImportHandler::getProgressionNotes()
     */
    public function getProgressionNotes()
    {
        return 'Currently importing : ' . $this->currentGUID;
    }
}