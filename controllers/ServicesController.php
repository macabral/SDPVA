<?php

// o arquivo xml a ser devolvido está armazenado na tabela xmlfile
// esta tabela deve ter o nome do último arquivo xml gerado no processamento.

require_once 'processamentodb.php';
require_once 'clientesxmldb.php';
require_once 'parametrosdb.php';

class ServicesController extends Zend_Controller_Action {

    public function indexAction() {

    }

    public function getdiagnosticoAction() {
        // envia o arquivo de diagnóstico por solicitação do cliente
        $this->_helper->viewRenderer->setNoRender(true);
        $this->view->layout()->disableLayout();
        $dt    = $this->_getParam('dt', '');
        $pista = $this->_getParam('pista', '09');
        $local = $this->_getParam('local','');

        $processamentodb = new processamentodb();
        $localdb = new parametrosdb();

        if (empty($dt)) {
            $nomearq         = $processamentodb->getfile();
        } else {
            $nomearq = substr($dt,0,12) . '.xml';
        }
        if (empty($local)) {
            $local = $localdb->getLocal();
        }

        $arq = APP_XML . strtoupper($local) . '/diagnostico/' . $pista . '/' . $nomearq;
        try {
            if (file_exists($arq)) {
                $xml = file_get_contents($arq, true);
            } else {
                $xml = '<?xml version="1.0" encoding="UTF-8"?><sdpva><error>Arquivo não encontrado ' . strtoupper($local) . ' (' . $dt . '.xml)</error></sdpva>';
            }
        } catch (Exception $e) {
            $xml = '<?xml version="1.0" encoding="UTF-8"?><sdpva><error>' . $e->getMessage() . '</error></sdpva>';
        }
        header('Content-Type: text/xml');
        echo $xml;
        
        $this->_helper->Logger->gravarlog('info', 'Diagnóstico enviado', 'getdiagnostico');
    }

    public function getalertasAction() {
        // envia o arquivo de alertas por solicitação do cliente
        $this->_helper->viewRenderer->setNoRender(true);
        $this->view->layout()->disableLayout();
        // identifica o último arquivo xml gerado
        // retorna o arquivo xml

        $dt    = $this->_getParam('dt', '');
        $pista = $this->_getParam('pista', '09');
        $local = $this->_getParam('local','');

        $processamentodb = new processamentodb();
        $localdb = new parametrosdb();

        if (empty($dt)) {
            $nomearq         = $processamentodb->getfile();
        } else {
            $nomearq = substr($dt,0,12) . '.xml';
        }
        if (empty($local)) {
            $local = $localdb->getLocal();
        }

        $arq = APP_XML . strtoupper($local) . '/alertas/' . $pista . '/' . $nomearq;
        try {
            if (file_exists($arq)) {
                $xml = file_get_contents($arq, true);
            } else {
                $xml = '<?xml version="1.0" encoding="UTF-8"?><sdpva><error>Arquivo não encontrado ' . strtoupper($local) . ' (' . $dt . '.xml)</error></sdpva>';            }
        } catch (Exception $e) {
            $xml = '<?xml version="1.0" encoding="UTF-8"?><sdpva><error>' . $e->getMessage() . '</error></sdpva>';
        }
        header('Content-Type: text/xml');
        echo $xml;
        
        $this->_helper->Logger->gravarlog('info', 'Alertas enviado', 'getalertas');
    }

    public function getgeralAction() {
        //
        $this->_helper->viewRenderer->setNoRender(true);
        $this->view->layout()->disableLayout();
        // identifica o último arquivo xml gerado
        // retorna o arquivo xml

        $dt    = $this->_getParam('dt', '');
        $local = $this->_getParam('local', 'SBGR');

        if (empty($dt)) {
            $processamentodb = new processamentodb();
            $nomearq         = $processamentodb->getfile();
        } else {
            $nomearq = substr($dt,0,12)  . '.xml';
        }
        $arq = APP_XML . strtoupper($local) . '/geral/' . $nomearq;
        try {
            if (file_exists($arq)) {
                $xml = file_get_contents($arq, true);
            } else {
                $xml = '<?xml version="1.0" encoding="UTF-8"?><sdpva><error>Arquivo não encontrado (' . $dt . '.xml)</error></sdpva>';
            }
        } catch (Exception $e) {
            $xml = '<?xml version="1.0" encoding="UTF-8"?><sdpva><error>Um erro foi encontrado: ' . $e->getMessage() . '</error></sdpva>';
        }
        header('Content-Type: text/xml');
        echo $xml;

        $this->_helper->Logger->gravarlog('info', 'Geral enviado', 'getgeral');
    }

    public function getprognosticoAction() {
        //
        $this->_helper->viewRenderer->setNoRender(true);
        $this->view->layout()->disableLayout();
        // identifica o último arquivo xml gerado
        // retorna o arquivo xml

        $dt    = $this->_getParam('dt', '');
        $local = $this->_getParam('local', 'SBGR');

        if (empty($dt)) {
            $processamentodb = new processamentodb();
            $nomearq         = $processamentodb->getfile();
        } else {
            $nomearq = substr($dt,0,12)  . '.xml';
        }
        $arq = APP_XML . strtoupper($local) . '/prognostico/' . $nomearq;
        try {
            if (file_exists($arq)) {
                $xml = file_get_contents($arq, true);
            } else {
                $xml = '<?xml version="1.0" encoding="UTF-8"?><sdpva><error>Arquivo não encontrado (' . $dt . '.xml)</error></sdpva>';
            }
        } catch (Exception $e) {
            $xml = '<?xml version="1.0" encoding="UTF-8"?><sdpva><error>Um erro foi encontrado: ' . $e->getMessage() . '</error></sdpva>';
        }
        header('Content-Type: text/xml');
        echo $xml;

        $this->_helper->Logger->gravarlog('info', 'Prognóstico enviado', 'getprognostico');
    }

    public function getmndAction() {
        // envia o arquivo mnd
        $this->_helper->viewRenderer->setNoRender(true);
        $this->view->layout()->disableLayout();

        // nome do arquivo a enviar
        $nomearq    = $this->_getParam('dt');
        $local    = $this->_getParam('local');
        if (empty($nomearq)) {
            $date = new Zend_Date();
            $nomearq = $date->toString("YYMMdd") ;
        }
        $nomearq = $nomearq . '.mnd';
        if (empty($local)) {
            // recupera a localidade
            $localdb = new parametrosdb();
            $local = $localdb->getLocal();
        }
        $arq = APP_APRUN . $nomearq;
        try {
            if (file_exists($arq)) {
                $xml = file_get_contents($arq, true);
            } else {
                $xml = '<?xml version="1.0" encoding="UTF-8"?><sdpva><error>Arquivo n�o encontrado.</error></sdpva>';
            }
        } catch (Exception $e) {
             $xml = '<?xml version="1.0" encoding="UTF-8"?><sdpva><error>Um erro foi encontrado: ' . $e->getMessage() . '</error></sdpva>';
        }
        header('Content-Type: application/text');
        echo $xml;
        
        $this->_helper->Logger->gravarlog('info', 'Arquivo MND enviado', 'getmnd');
    }
    
    public function sendxmlAction() {
        // envia os arquivos cadastrados na tabela clientes
        $this->_helper->viewRenderer->setNoRender(true);
        $this->view->layout()->disableLayout();

        // log de arquivos enviados
        $log             = 'Enviando arquivos XML: ';
        $pista           = '09';

        // identifica o último arquivo xml gerado
        $processamentodb = new processamentodb();

        // recupera a localidade
        $localdb = new parametrosdb();
        $local = $localdb->getLocal();

        // envia arquivos para os clientes XML
        $clientesxmldb   = new clientesxmldb();
        $clientesxmlData = $clientesxmldb->getAtivos();
        // nome arquivo
        $nomearq = $processamentodb->getfile();

        foreach ($clientesxmlData as $d) {
            $pista   = $d['pista'];
            $tipo    = $d['tipo'];

            // le arquivo
            if ($tipo == 'D') {
                $arq = APP_XML . $local . '/diagnostico/' . $pista . '/' . $nomearq;
            } elseif ($tipo == 'P') {
                $arq = APP_XML . $local . '/prognostico/' . $nomearq;
            } elseif ($tipo == 'A') {
                $arq = APP_XML . $local . '/alertas/' . $pista . '/'. $nomearq;
            } elseif ($tipo == 'G') {
                $arq = APP_XML . $local . '/geral/' . $nomearq;
            }
            $log .= "  >  " . $arq . " ! ";
            try {
                if (file_exists($arq)) {
                    $xml = file_get_contents($arq, true);
                } else {
                    $xml = '<?xml version="1.0" encoding="UTF-8"?><sdpva><error>Arquivo não encontrado (' . $arq . ')</error></sdpva>';
                    $log .= "  Arquivo não encontrado!" . " ! ";
                }
            } catch (Exception $e) {
                $xml = '<?xml version="1.0" encoding="UTF-8"?><sdpva><error>' . $e->getMessage() . '</error></sdpva>';
                $log .= $e->getMessage() . " ! ";
            }
            try {
                $client = new Zend_Http_Client($d['url'], array('timeout' => 30));
                $client->setRawData($xml, 'text/xml');
                $client->request('POST');
                $log .= "   Enviando para: ". $d['url'] . " | ";
            } catch (Zend_Http_Client_Exception $e) {
                $log .= utf8_encode($e->getCode() . ' Erro: ' . $e->getMessage()) . " ";
            }
        }
        // grava arquivo de log

        $this->_helper->Logger->gravarlog('info', $log, 'sendxml');
        
        // envia os arquivos mnd
        //$this->_redirect("services/sendmnd");
    }

    public function receivexmlAction() {
        // recebe os arquivos enviados pelo servidor (sendxml)
        $this->_helper->viewRenderer->setNoRender(true);
        $this->view->layout()->disableLayout();
	$locale = new Zend_Locale('auto');

        $log = 'Recebendo arquivos XML.' . chr(13);
        try {
            $request = new Zend_Controller_Request_Http();
            $data    = $request->getRawBody();
            // le xml para identificar local para a pasta de armazenamento
            try {
                $xml = SimpleXML_load_string($data);
                $log .= $xml;
            } catch (Exception $e) {
                $xml = '<?xml version="1.0" encoding="UTF-8"?><sdpva><error>' . $e->getMessage() . '</error></sdpva>';
                $log .= $e->getMessage();
            }
            $local = $xml['local'];
            if (!empty($local)) {
                $pista = $xml['pista'];
                $tipo  = $xml['tipo'];
                $arq   = substr($xml['datahora'], 0, 4) . substr($xml['datahora'], 5, 2) . substr($xml['datahora'], 8, 2) . substr($xml['datahora'], 11, 2) . substr($xml['datahora'], 14, 2) ;
               if ($tipo == 'D') {
                    $tipo = 'diagnostico';
                } elseif ($tipo == 'P') {
                    $tipo = 'prognostico';
                } elseif ($tipo == 'A') {
                    $tipo = 'alertas';
                } else {
                    $tipo = 'geral';
                }
                if ($tipo == 'geral' || $tipo == "prognostico"){
                    $arq1 = APP_XML . $local . '/' . $tipo . '/' . $arq . '.xml';  
                } else {
                    $arq1 = APP_XML . $local . '/' . $tipo . '/' . $pista . '/' . $arq . '.xml';
                }   
                $log .= 'Gerando arquivo: ' . $arq1 . chr(13);
                if (file_exists($arq1)) {
                    unlink($arq1);
                }
                $file = fopen($arq1, 'w');
                fwrite($file, $data);
                fclose($file);
                
                // caso geral insere dados do xml no banco de dados
                if ($tipo == "geral") {
                    $this->processasodar($local,$arq);
                }
  
            } else {
                $log .= 'Não foi possível ler o arquivo XML.' . $xml;
            }
        } catch (Zend_Http_Client_Exception $e) {
            $log .= '[' . $e->getCode() . ']:' . $e->getMessage();
        }
        
        $this->_helper->Logger->gravarlog('info', $log, 'getxml');

    }

    private function processasodar($local,$dt) {
        
        // local = SBGR
        // $dt no formato YYYYMMDDHHMM
        
        $this->_helper->viewRenderer->setNoRender(true);
        $this->view->layout()->disableLayout();
        
        $processamentodb = new processamentodb();
        $localdb = new parametrosdb();

        if (empty($dt)) {
            $data = new Zend_Date();
            $dt = $data->get("YYYYMMddHHmm");
        } 
        $dt = substr($dt,0,12);
        if (empty($local)) {
            $local = $localdb->getLocal();
        }
            
        // processa dados do sodar
        if ($this->_helper->Procsodar->procsodar($local,$dt)) {
            
            // processa alertas
            $this->_helper->Alertas->alertas($local, $dt);
                      
            // processa solicitação de dados da EMS
            $this->_helper->getEMS->getems($local,$dt);
            
            // processa entrada RNA
            // a RNA chama o processamento do prognóstico
            $this->_helper->Entradarna->entradarna($local,$dt);
                       
            // gerar gráficos
            $this->_helper->Graficos->graficos();
            
            // enviar arquivos XML para clientes cadastrados
            $this->sendxmlAction();
            
        }
        
    }

    // ****************************************************************
    // executa o processamento com base na datahora do processamento do sodar

    public function execsodarAction() {

        $this->_helper->viewRenderer->setNoRender(true);
        $this->view->layout()->disableLayout();

        //chdir('/var/www/html/SDPVA/');

	//system("bash /var/www/html/SDPVA/insere_bd_atual.sh 2>&1");
        // processa soda_atual
        $this->_helper->Readmnd->readmnd();

        $processamentodb = new processamentodb();
        $localdb = new parametrosdb();

        $dtData = $processamentodb->getProcessamento();
        $dt = $dtData['datahora'];
        $dt = substr($dt,0,4) . substr($dt,5,2) . substr($dt,8,2) . substr($dt,11,2) . substr($dt,14,2);

        $local = $localdb->getLocal();

        // processa dados derivados
        $this->_helper->Dadosderivados->dadosderivados();

        // gera arquivos XML
        system("python2.7 " . APPLICATION_PATH . "/python/xml09/xml.py");
        system("python2.7 " . APPLICATION_PATH . "/python/xml27/xml.py");
        system("python2.7 " . APPLICATION_PATH . "/python/xml_geral.py");

        // processa alertas
        $this->_helper->Alertas->alertas($local, $dt);

        // processa solicita��o de dados da EMS
        $this->_helper->getEMS->getems($local,$dt);

        // processa entrada RNA
        // a RNA chama o processamento do progn�stico
        $this->_helper->Entradarna->entradarna($local,$dt);

        // gerar gr�ficos
        $this->_helper->Graficos->graficos();

        // enviar arquivos XML para clientes cadastrados
        $this->sendxmlAction();

    }
 
    // ****************************************************************
    // executa o processamento com base na datahora do processamento do sodar

    public function procemsAction() {

        $this->_helper->viewRenderer->setNoRender(true);
        $this->view->layout()->disableLayout();

	$localdb = new parametrosdb();
	$processamentodb = new processamentodb();
	
	$dt = new Zend_Date();

        $dt = $dt->toString("YYYY-MM-dd HH:mm");
        $dt = substr($dt,0,4) . substr($dt,5,2) . substr($dt,8,2) . substr($dt,11,2) . substr($dt,14,2);

        $local = $localdb->getLocal();

	//grava nova data hora
	$datahr = substr($dt,0,4) . "-" . substr($dt,4,2) . "-" . substr($dt,6,2) . " " . substr($dt,8,2) . ":" . substr($dt,10,2);
	print_r($datahr);
        $processamentodb->gravaData($datahr);

        // processa solicita��o de dados da EMS
        $this->_helper->getEMS->getems($local,$dt);

        // processa entrada RNA
        // a RNA chama o processamento do progn�stico
        $result  = $this->_helper->Entradarna->entradarna($local,$dt);

        // enviar arquivos XML para clientes cadastrados
        //$this->sendxmlAction();

    }
    // ****************************************************************
    // envio de arquivos MND do APRun
    
    public function sendmndAction() {
        // envia para os arquivos cadastrados na tabela clientes
        //$this->_helper->viewRenderer->setNoRender(true);
        //$this->view->layout()->disableLayout();

        // log de arquivos enviados
        $log = 'Enviando arquivos MND...';
        
        // nome do arquivo a enviar
        $nomearq    = $this->_getParam('arq');
        if (empty($nomearq)) {
            $date = new Zend_Date();
            $nomearq = $date->toString("YYMMdd") . '.mnd';
        } 

        
        
        $log .= '  ==> Arquivo ' . $arq;
        
        // recupera a localidade
        $localdb = new parametrosdb();
        $local = $localdb->getLocal();
        
        $arq = APP_APRUN2 . strtoupper($local) . "/mnd/" . $nomearq;
                
        // envia arquivos para os clientes XML
        $clientesxmldb   = new clientesxmldb();
        $clientesxmlData = $clientesxmldb->getAtivos2();
        
        if (file_exists($arq)) {
           // envia o arquivo
           try {
                $fp = fopen($arq,"r");
                foreach ($clientesxmlData as $d) {
                    $client = new Zend_Http_Client($d['url'], array('timeout' => 30));
                    $client->setParameterGet(array("arquivo" => $nomearq, "local" => $local));
                    $client->setRawData($fp, 'application/text')->request('PUT');
                    $log .= "Arquivo enviado com sucesso para " . $d['url'];
                }
            } catch (Zend_Http_Client_Exception $e) {
                $log .= utf8_encode($e->getCode() . ': ' . $e->getMessage()) . chr(13);
            }
        } else {
            $log .= "arquivo não encontrado " . $arq;
        }

        $this->_helper->Logger->gravarlog('info', $log, 'sendmnd');
    }
    
    public function getmndfileAction() {
        // recebe o arquivo mnd (arquivo padrão do APRun)
        $this->_helper->viewRenderer->setNoRender(true);
        $this->view->layout()->disableLayout();
        
        $log = "MND: ";
        
        // recebe como parametros a localidade e o nome do arquivo gerado
                
        try {
            $client = new Zend_Controller_Request_Http();
            $nomearq    = $this->_getParam('arquivo');
            $local    = $this->_getParam('local');
            $data    = $client->getRawBody();
            $arq = APP_XML . $local . '/mnd/' . $nomearq;
            
            $file = fopen($arq, 'w');
            fwrite($file, $data);
            fclose($file);
            $log .= "Recebido arquivo mnd " . $arq;
        } catch (Zend_Http_Client_Exception $e) {
            $log  .= $e->getMessage();
        }
        
        $this->_helper->Logger->gravarlog('info', $log, 'getmndfile');
        
    }
    
    public function getremotemndAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->view->layout()->disableLayout();
        $url = "http://10.32.48.63:8080/SDPVA/services/getmnd";
	if($hc = fopen($url,'r'))
	{        
	    	$data = stream_get_contents($hc);
		fclose($hc);
		$date = new Zend_Date();
		$nomearq = $date->toString("YYMMdd") . '.mnd';
		$arq = APP_XML . 'SBRJ/mnd/' . $nomearq;
		unlink($arq);

		$file = fopen($arq, 'w+');
		fwrite($file, $data);
		fclose($file);
	}

    }
    
    public function getlidarAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->view->layout()->disableLayout();
        //date_default_timezone_set('America/Sao_Paulo');
        $dia = date('d');
        $mes = substr(date('F'),0,3);
        $ano = date('Y');
              
        // diretorio de trabalho
        $diretorio_programa = "/mnt/blview/History/".$ano.'/'.$mes;
        $arq = $diretorio_programa . '/' . 'CEILOMETER_1_LEVEL_3_DEFAULT_' . $dia . '.his';

        try {
            if (file_exists($arq)) {
                $txt = file_get_contents($arq, true);
            } else {
                $txt = 'Arquivo não encontrado.';
            }
        } catch (Exception $e) {
            $txt = $e->getMessage();
        }
        header('Content-Type: application/text');
        echo $txt;
    }

    public function rnaAction(){
          $this->_helper->viewRenderer->setNoRender(true);
          $this->view->layout()->disableLayout();
	  $this->_helper->Entradarna->entradarna();
    }
    
    public function testemsAction() {
        
    }
    

    
}

