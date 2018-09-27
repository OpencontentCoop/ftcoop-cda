php extension/sqliimport/bin/php/sqlidoimport.php -sbackend --source-handlers=exportasXMLhandler --options="exportasXMLhandler::file=extension/ftcoop-cda/import/data/politico.xml,url=https://www.cooperazionetrentina.it,parent=12,mapper=PoliticoMapper";

php extension/sqliimport/bin/php/sqlidoimport.php -sbackend --source-handlers=exportasXMLhandler --options="exportasXMLhandler::file=extension/ftcoop-cda/import/data/tecnico.xml,url=https://www.cooperazionetrentina.it,parent=12,mapper=PoliticoMapper";

php extension/sqliimport/bin/php/sqlidoimport.php -sbackend --source-handlers=exportasXMLhandler --options="exportasXMLhandler::file=extension/ftcoop-cda/import/data/responsabilearea.xml,url=https://www.cooperazionetrentina.it,parent=12,mapper=PoliticoMapper";

php runcronjobs.php -sbackend sqliimport_cleanup;

php extension/sqliimport/bin/php/sqlidoimport.php -sbackend --source-handlers=exportasXMLhandler --options="exportasXMLhandler::file=extension/ftcoop-cda/import/data/organo.xml,url=https://www.cooperazionetrentina.it,parent=69";

php runcronjobs.php -sbackend sqliimport_cleanup