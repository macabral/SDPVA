<?php
require_once 'sodaratualdb.php';
require_once 'processodb.php';
require_once 'parametrosdb.php';
require_once 'dadosderivadosdb.php';
require_once 'alertasdb.php';



class Zend_Controller_Action_Helper_Myhelper extends Zend_Controller_Action_Helper_Abstract {

    private function gravarlog($tipo, $msg, $modulo) {
        $_logger = Zend_Registry::get('logger');
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $_logger->setEventItem('username', Zend_Auth::getInstance()->getIdentity()->login);
            $_logger->setEventItem('fkidusu', Zend_Auth::getInstance()->getIdentity()->idusu);
        } else {
            $_logger->setEventItem('username', 'convidado');
            $_logger->setEventItem('fkidusu', 0);
        }
        $_logger->setEventItem('userip', $_SERVER['REMOTE_ADDR']);
        $_logger->setEventItem('modulo', $modulo);

        $msg = substr($msg,0,500);
        if ($tipo == 'info') {
            $_logger->info($msg);
        }
        if ($tipo == 'err') {
            $_logger->err($msg);
        }
        if ($tipo == 'warn') {
            $_logger->warn($msg);
        }
    }

    private function arredonda($numero) {
        $resto = $numero % 10;
        if ($resto == 1) {
            $numero = $numero - 1;
        }
        if ($resto == 2) {
            $numero = $numero - 2;
        }
        if ($resto == 3) {
            $numero = $numero - 3;
        }
        if ($resto == 4) {
            $numero = $numero - 4;
        }
        if ($resto == 5) {
            $numero = $numero + 5;
        }
        if ($resto == 6) {
            $numero = $numero + 4;
        }
        if ($resto == 7) {
            $numero = $numero + 3;
        }
        if ($resto == 8) {
            $numero = $numero + 2;
        }
        if ($resto == 9) {
            $numero = $numero + 1;
        }
        return $numero;
    }

    public function valData($data) {
        // valida data no formato dd/mm/aaaa
        if (strlen($data) <> 10) {
            return false;
        }
        $dd = substr($data, 0, 2);
        $mm = substr($data, 3, 2);
        $aa = substr($data, 6, 4);

        if (is_numeric($dd) && is_numeric($mm) && is_numeric($aa)) {
            if ($dd < 0 || $dd > 31) {
                return false;
            } elseif ($mm < 0 || $mm > 12) {
                return false;
            } elseif ($aa<0){
                return false;
            }
            return true;
        }
        return true;
    }

    public function processalocal($local,$arq) {
        // esta função tem por finalidade ler o arquivo XML dos dados do SODAR
        // gravar no banco de dados, processar os dados derivados e os alertas

        $arq = APP_ROOT . '/public/xml/' . $local . '/geral/' . $arq;

        $this->gravarlog('info', "Processando arquivo $arq", 'Processamento');

        // lê arquivo xml recebido da localidade
        $xml = simplexml_load_file($arq);

        // formata a data do processamento

        $dtx =  str_replace("T"," ",$xml['datahora']);
        $dt = "'" . $dtx . "'";

        // monta comando SQL
        $sql = 'INSERT INTO sodar_atual VALUES ' ;
        foreach ($xml->Obs as $obs){

            $sql .="(DEFAULT, $dt, ";
            $sql .= $obs->z . ', ';
            $sql .= $obs->speed . ', ';
            $sql .= $obs->dir . ', ';
            $sql .= $obs->U . ', ';
            $sql .= $obs->V . ', ';
            $sql .= $obs->W . ', ';
            $sql .= $obs->sigU . ', ';
            $sql .= $obs->sigU_r . ', ';
            $sql .= $obs->sigV . ', ';
            $sql .= $obs->sigV_r . ', ';
            $sql .= $obs->sigW . ', ';
            $sql .= $obs->speed_ass . ', ';
            $sql .= $obs->dir_ass . ', ';
            $sql .= $obs->U_ass . ', ';
            $sql .= $obs->V_ass . ', ';
            $sql .= $obs->W_ass . ', ';
            $sql .= $obs->sigU_ass . ', ';
            $sql .= $obs->sigU_r_ass . ', ';
            $sql .= $obs->sigV_ass . ', ';
            $sql .= $obs->sigV_r_ass . ', ';
            $sql .= $obs->sigW_ass . ', ';
            $sql .= $obs->shear . ', ';
            $sql .= $obs->shearDir . ', ';
            $sql .= $obs->sigSpeed . ', ';
            $sql .= $obs->sigLat . ', ';
            $sql .= $obs->sigPhi . ', ';
            $sql .= $obs->sigTheta . ', ';
            $sql .= $obs->TI . ', ';
            $sql .= $obs->PGz . ', ';
            $sql .= $obs->TKE . ', ';
            $sql .= $obs->EDR . ', ';
            $sql .= $obs->bck_raw . ', ';
            $sql .= $obs->bck . ', ';
            $sql .= $obs->bck_ID . ', ';
            $sql .= $obs->CT2 . ', ';
            $sql .= $obs->error . ', ';
            $sql .= $obs->PG . ', ';
            $sql .= $obs->h_range . ', ';
            $sql .= $obs->h_inv . ', ';
            $sql .= $obs->h_mixing . ', ';
            $sql .= $obs->H . ', ';
            $sql .= $obs->u_estrela . ', ';
            $sql .= $obs->L_estrela . ', ';
            $sql .= "'" . $local . "'";
            $sql .= '), ';
        }
        $sql = rtrim($sql, ', ');

        // salva registros do arquivo xml no banco de dados - tabela: sodar_atual
        $sodar_atualdb = new sodaratualdb();
        try {
            $sodar_atualdb->gravar($sql);
        } catch (Exception $e) {
            $this->gravarlog('err', utf8_encode($e->getMessage()), 'Processamento');
        }
        // atualiza a data de processamento - tabela: processamento
        $processadb = new processodb();
        $n = substr($dtx,0,4) .substr($dtx,5,2) . substr($dtx,8,2) . substr($dtx,11,2) .substr($dtx,14,2) . ".xml";
        $sql = "update processamento set datahora = " . $dt .   ", nomearqxml_d = '". $n . "' where local = '" . $local . "'";
        try {
            $processadb->gravar($sql);
        } catch (Exception $e) {
            $this->gravarlog('err', utf8_encode($e->getMessage()), 'Processamento');
        }
        // atualiza dados derivados
        $sql = "UPDATE sodar_atual s SET U = ROUND(s.speed*SIN(RADIANS(s.dir)),2), V= ROUND(s.speed*COS(RADIANS(s.dir)),2) WHERE s.data=$dt and s.speed<99.99";
        try {
            $sodar_atualdb->gravar($sql);
        } catch (Exception $e) {
            $this->gravarlog('err', utf8_encode($e->getMessage()), 'Processamento');
        }
        try {
            $derivados1 = $sodar_atualdb->getDerivados1($dt);
            foreach ($derivados1 as $linha) {
                $data = $linha['data'];
                $z = $linha['z'];
                $dir = $linha['dir'];
                $speed = $linha['speed'];
    
                ##Orientaçao da pista de esquerda para a direita
                
                ## Proa>=10 cauda>=2  traves>= 5
                
                $alinhado_real = number_format(cos(deg2rad($dir-70))*$speed/0.51444, 2);
                if($alinhado_real<0)
                {
                    $proa = number_format($alinhado_real*-1, 2);
                    $cauda = 0;
                }	
                else
                {
                    $cauda = number_format($alinhado_real, 2);
                    $proa = 0;
                }
                
                $traves_real = number_format(sin(deg2rad($dir-70))*$speed/0.51444, 2);	
                if($traves_real<0)
                {
                    $traves_esq = number_format($traves_real*-1, 2);
                    $traves_dir = 0;
                }	
                else
                {
                    $traves_dir = number_format($traves_real, 2);
                    $traves_esq = 0;
                }
                $sql = "INSERT INTO dados_derivados VALUES($alinhado_real, $traves_real, $cauda, $proa, $traves_dir, $traves_esq, '$data', $z, '$local' )";
                $sodar_atualdb->gravar($sql);
    
            }
        } catch (Exception $e) {
            $this->gravarlog('err', utf8_encode($e->getMessage()), 'Processamento');
        }
        // calcula os alertas
        $this->calcalerta($dtx,$local);
        
        // gera entradas para a RNA
        //system("python2.7 ". APPLICATION_PATH . "/python/escreve_entrada_altitude_RNA.py");
        
        // gera arquivos XML
        system("python2.7 ". APPLICATION_PATH . "/python/xml09/xml.py");
        system("python2.7 ". APPLICATION_PATH . "/python/xml27/xml.py");
        
        // gera gráficos
        system("python2.7 ". APPLICATION_PATH . "/python/barbela3h.py");
        system("python2.7 ". APPLICATION_PATH . "/python/barbela6h.py");
        system("python2.7 ". APPLICATION_PATH . "/python/barbela12h.py");
        system("python2.7 ". APPLICATION_PATH . "/python/barbela24h.py");
        system("python2.7 ". APPLICATION_PATH . "/python/vetor3h.py");
        system("python2.7 ". APPLICATION_PATH . "/python/vetor6h.py");
        system("python2.7 ". APPLICATION_PATH . "/python/vetor12h.py");
        system("python2.7 ". APPLICATION_PATH . "/python/vetor24h.py");
        system("python2.7 ". APPLICATION_PATH . "/python/var_proa.py");
        system("python2.7 ". APPLICATION_PATH . "/python/var_proa_27.py");
        system("python2.7 ". APPLICATION_PATH . "/python/var_cauda.py");
        system("python2.7 ". APPLICATION_PATH . "/python/var_cauda_27.py");
        system("python2.7 ". APPLICATION_PATH . "/python/var_traves_dir.py");
        system("python2.7 ". APPLICATION_PATH . "/python/var_traves_dir_27.py");
        system("python2.7 ". APPLICATION_PATH . "/python/var_traves_esq.py");
        system("python2.7 ". APPLICATION_PATH . "/python/var_traves_esq_27.py");
        system("python2.7 ". APPLICATION_PATH . "/python/vel_proa.py");
        system("python2.7 ". APPLICATION_PATH . "/python/vel_proa_27.py");
        system("python2.7 ". APPLICATION_PATH . "/python/vel_cauda.py");
        system("python2.7 ". APPLICATION_PATH . "/python/vel_cauda_27.py");
        system("python2.7 ". APPLICATION_PATH . "/python/vel_traves_dir.py");
        system("python2.7 ". APPLICATION_PATH . "/python/vel_traves_dir_27.py");
        system("python2.7 ". APPLICATION_PATH . "/python/vel_traves_esq.py");
        system("python2.7 ". APPLICATION_PATH . "/python/vel_traves_esq_27.py");
        system("python2.7 ". APPLICATION_PATH . "/python/windshear6x1.py");
        
        
        // envia arquivos XML (sendxml)


    }

    // calculo dos Alertas
    private function calcalerta($dthr,$local) {

        $arqdata = substr($dthr,0,4) . substr($dthr,5,2) . substr($dthr,8,2) .  substr($dthr,11,2) . substr($dthr,14,2);

        // variáveis
        $pista = '09';

        // le parametros
        $paramdb = new parametrosdb();
        $paramData = $paramdb->getParametros();

        // le dados_derivados
        $dadosderivadosdb = new dadosderivadosdb();

        // parametros
        $wind_var_cauda = $paramData['wind_var_cauda'];
        $wind_var_proa = $paramData['wind_var_proa'];
        $wind_var_traves = $paramData['wind_var_traves'];
        $wind_int_proa = $paramData['wind_int_alerta1_min'];
        $wind_int_cauda = $paramData['wind_int_alerta1_max'];
        $wind_int_traves = $paramData['wind_int_alerta2_max'];
        $wind_shear_interv1_max = $paramData['wind_shear_interv1_max'];
        $wind_shear_interv2_max = $paramData['wind_shear_interv2_max'];
        $wind_shear_interv3_max = $paramData['wind_shear_interv3_max'];

        // Velocidade de Cauda
        $dadosderivadosData = $dadosderivadosdb->getVelocidadeCauda($dthr);
        $dadosVelCauda = $dadosderivadosData['cauda'];
        $VelocidadeCauda = $this->fcalcula1($dadosVelCauda, $wind_int_cauda, $dadosderivadosData['altura']);

        // Velocidade de Proa
        $dadosderivadosData = $dadosderivadosdb->getVelocidadeProa($dthr);
        $dadosVelProa = $dadosderivadosData['proa'];
        $VelocidadeProa = $this->fcalcula1($dadosVelProa, $wind_int_proa, $dadosderivadosData['altura']);

        // Velocidade Través Esquerdo
        $dadosderivadosData = $dadosderivadosdb->getTravesEsquerdo($dthr);
        $dadosVelTravesEsquerdo = $dadosderivadosData['traves_esq'];
        $VelocidadeTravesEsquerdo = $this->fcalcula1($dadosVelTravesEsquerdo, $wind_int_traves, $dadosderivadosData['altura']);

        // Velocidade Través Direito
        $dadosderivadosData = $dadosderivadosdb->getTravesDireito($dthr);
        $dadosVelTravesDireito = $dadosderivadosData['traves_dir'];
        $VelocidadeTravesDireito = $this->fcalcula1($dadosVelTravesDireito, $wind_int_traves, $dadosderivadosData['altura']);

        // Variação de Cauda $var1 = $dadosVarCauda, $var2 = $wind_var_cauda, $var3 = $dadosderivadosData['altura']
        $dadosderivadosData = $dadosderivadosdb->getVariacaoCauda($dthr);
        $dadosVarCauda = $dadosderivadosData['var_cauda'];
        $VariacaoCauda = $this->fcalcula2($dadosVarCauda, $wind_var_cauda, $dadosderivadosData['altura']);

        // Variação de Proa
        $dadosderivadosData = $dadosderivadosdb->getVariacaoProa($dthr);
        $dadosVarProa = $dadosderivadosData['var_proa'];
        $VariacaoProa = $this->fcalcula2($dadosVarProa, $wind_var_proa, $dadosderivadosData['altura']);

        // Variação Través Direito
        $dadosderivadosData = $dadosderivadosdb->getVariacaoTravesDir($dthr);
        $dadosVarTravesDir = $dadosderivadosData['var_traves_dir'];
        $VariacaoTravesDir = $this->fcalcula2($dadosVarTravesDir, $wind_var_proa, $dadosderivadosData['altura']);

        // Variação Través Esquerdo
        $dadosderivadosData = $dadosderivadosdb->getVariacaoTravesEsq($dthr);
        $dadosVarTravesEsq = $dadosderivadosData['var_traves_esq'];
        $VariacaoTravesEsq = $this->fcalcula2($dadosVarTravesEsq, $wind_var_proa, $dadosderivadosData['altura']);

        // Windshear
        $dadosderivadosData = $dadosderivadosdb->getWindshear($dthr);
        $dadosWindshear = $dadosderivadosData['windshear'];

        switch ($dadosWindshear) {
            case ($dadosWindshear < $wind_shear_interv1_max):
                $tipow = 'Leve';
                $AlertaW = 0;
                break;
            case ($dadosWindshear >= $wind_shear_interv1_max && $dadosWindshear < $wind_shear_interv2_max):
                $tipow = 'Moderado';
                $AlertaW = 0;
                break;
            case ($dadosWindshear > $wind_shear_interv2_max && $dadosWindshear <= $wind_shear_interv3_max):
                $tipow = 'Forte';
                $AlertaW = 1;
                break;
            case ($dadosWindshear > $wind_shear_interv3_max):
                $tipow = 'Severo';
                $AlertaW = 1;
                break;
            default:
                break;
        }
        $txt = abs(number_format($dadosWindshear, 2)) . ' kt/100ft em ' . $this->arredonda(number_format($dadosderivadosData['altura'] / 0.3048, 0, '.', '')) . ' ft';
        $Windshear = $tipow . '<br>' . $txt;


        // salva alertas
        $alertadb = new alertasdb();
        $dtxml = substr($dthr,0,10) . "T" . substr($dthr,11,8);

        try {
            // grava dados processados na tabela processamento - pista 09
            $pista = '09';
            $velcauda = $dadosVelCauda;
            $velproa = $dadosVelProa;
            $velte = $dadosVelTravesEsquerdo;
            $veltd = $dadosVelTravesDireito;
            $varcauda = $dadosVarCauda;
            $varproa = $dadosVarProa;
            $vartd = $dadosVarTravesDir;
            $varte = $dadosVarTravesEsq;

            // verifica alertas
            $AlertaVelCauda = ($velcauda >= $wind_int_cauda) ? 1 : 0;
            $AlertaVelProa = ($velproa >= $wind_int_proa) ? 1 : 0;
            $AlertaVelTE = ($velte >= $wind_int_traves) ? 1 : 0;
            $AlertaVelTD = ($veltd >= $wind_int_traves) ? 1 : 0;
            $AlertaVC = ($varcauda >= $wind_var_cauda) ? 1 : 0;
            $AlertaVP = ($varproa >= $wind_var_proa) ? 1 : 0;
            $AlertaVTD = ($vartd >= $wind_var_traves) ? 1 : 0;
            $AlertaVTE = ($varte >= $wind_var_traves) ? 1 : 0;

            $data = [
                'datahora' => $dthr,
                'pista' => $pista,
                'local' => $local,
                'velocidadecauda' => $VelocidadeCauda,
                'velocidadeproa' => $VelocidadeProa,
                'velocidadetravesesquerdo' => $VelocidadeTravesEsquerdo,
                'velocidadetravesdireito' => $VelocidadeTravesDireito,
                'variacaocauda' => $VariacaoCauda,
                'variacaoproa' => $VariacaoProa,
                'variacaotravesesq' => $VariacaoTravesEsq,
                'variacaotravesdir' => $VariacaoTravesDir,
                'windshear' => $Windshear,
                'alertavelcauda' => $AlertaVelCauda,
                'alertavelproa' => $AlertaVelProa,
                'alertavelte' => $AlertaVelTE,
                'alertaveltd' => $AlertaVelTD,
                'alertavc' => $AlertaVC,
                'alertavp' => $AlertaVP,
                'alertavtd' => $AlertaVTD,
                'alertavte' => $AlertaVTE,
                'alertaw' => $AlertaW,
            ];
            try{
                $alertadb->insert($data);
                $this->gravarlog('info', "Alertas processados -  $local - pista 09 - $dthr", 'Processamento');
            } catch (Exception $e) {
                $this->gravarlog('err', utf8_encode($e->getMessage()), 'Processamento');
            }

            // grava arquivo xml
            $xml = "<?xml version='1.0' encoding='UTF-8'?><sdpva local='SBGR' pista='" . $pista . " datahora='" . $dtxml . " tipo='A' >";
            $xml .= '<velcauda><valor>' . $VelocidadeCauda . '</valor><alerta>' . $AlertaVelCauda . '</alerta></velcauda>';
            $xml .= '<velproa><valor>' . $VelocidadeProa . '</valor><alerta>' . $AlertaVelProa . '</alerta></velproa>';
            $xml .= '<velte><valor>' . $VelocidadeTravesEsquerdo . '</valor><alerta>' . $AlertaVelTE . '</alerta></velte>';
            $xml .= '<veltd><valor>' . $VelocidadeTravesDireito . '</valor><alerta>' . $AlertaVelTD . '</alerta></veltd>';
            $xml .= '<varcauda><valor>' . $VariacaoCauda . '</valor><alerta>' . $AlertaVC . '</alerta></varcauda>';
            $xml .= '<varproa><valor>' . $VariacaoProa . '</valor><alerta>' . $AlertaVP . '</alerta></varproa>';
            $xml .= '<vartd><valor>' . $VariacaoTravesDir . '</valor><alerta>' . $AlertaVTD . '</alerta></vartd>';
            $xml .= '<varte><valor>' . $VariacaoTravesEsq . '</valor><alerta>' . $AlertaVTE . '</alerta></varte>';
            $xml .= '<windshear><valor>' . $tipow . ' - ' . $txt . '</valor><alerta>' . $AlertaW . '</alerta></windshear>';
            $xml .= '</sdpva>';

            $data = new Zend_Date($dthr);
            $filename = $arqdata . ".xml";
            $arq = APP_XML . $local . '/alertas/' . $pista . '/' . $filename;
            try {
                $hc = fopen($arq, 'wb');
                fwrite($hc, $xml);
                fclose($hc);
                $this->gravarlog('info', "Arquivo XML gerado $local  $arq", 'Processamento');
            } catch (Exception $e) {
                $this->gravarlog('err', utf8_encode($e->getMessage()), 'Processamento');
            }

            // grava dados processados na tabela processamento - pista 27
            $pista = '27';
            $velcauda = $dadosVelProa;
            $velproa = $dadosVelCauda;
            $velte = $dadosVelTravesDireito;
            $veltd = $dadosVelTravesEsquerdo;
            $varcauda = $dadosVarProa;
            $varproa = $dadosVarCauda;
            $vartd = $dadosVarTravesEsq;
            $varte = $dadosVarTravesDir;

            // verifica alertas
            $AlertaVelCauda = ($velcauda >= $wind_int_cauda) ? 1 : 0;
            $AlertaVelProa = ($velproa >= $wind_int_proa) ? 1 : 0;
            $AlertaVelTE = ($velte >= $wind_int_traves) ? 1 : 0;
            $AlertaVelTD = ($veltd >= $wind_int_traves) ? 1 : 0;
            $AlertaVC = ($varcauda >= $wind_var_cauda) ? 1 : 0;
            $AlertaVP = ($varproa >= $wind_var_proa) ? 1 : 0;
            $AlertaVTD = ($vartd >= $wind_var_traves) ? 1 : 0;
            $AlertaVTE = ($varte >= $wind_var_traves) ? 1 : 0;

            $data = [
                'datahora' => $dthr,
                'pista' => $pista,
                'local' => $local,
                'velocidadecauda' => $VelocidadeProa,
                'velocidadeproa' => $VelocidadeCauda,
                'velocidadetravesesquerdo' => $VelocidadeTravesDireito,
                'velocidadetravesdireito' => $VelocidadeTravesEsquerdo,
                'variacaocauda' => $VariacaoProa,
                'variacaoproa' => $VariacaoCauda,
                'variacaotravesesq' => $VariacaoTravesDir,
                'variacaotravesdir' => $VariacaoTravesEsq,
                'windshear' => $Windshear,
                'alertavelcauda' => $AlertaVelCauda,
                'alertavelproa' => $AlertaVelProa,
                'alertavelte' => $AlertaVelTE,
                'alertaveltd' => $AlertaVelTD,
                'alertavc' => $AlertaVC,
                'alertavp' => $AlertaVP,
                'alertavtd' => $AlertaVTD,
                'alertavte' => $AlertaVTE,
                'alertaw' => $AlertaW,
            ];
            try {
                $alertadb->insert($data);
                $this->gravarlog('info', "Alertas processados $local - pista 27 -  $dthr", 'Processamento');
            } catch (Exception $e) {
                $this->gravarlog('err', utf8_encode($e->getMessage()), 'Processamento');
            }

            // grava arquivo xml
            $xml = "<?xml version='1.0' encoding='UTF-8'?><sdpva local='SBGR' pista='" . $pista . " datahora='" . $dtxml . " tipo='A' >";
            $xml .= '<velcauda><valor>' . $VelocidadeProa . '</valor><alerta>' . $AlertaVelProa . '</alerta></velcauda>';
            $xml .= '<velproa><valor>' . $VelocidadeCauda . '</valor><alerta>' . $AlertaVelCauda . '</alerta></velproa>';
            $xml .= '<velte><valor>' . $VelocidadeTravesDireito . '</valor><alerta>' . $AlertaVelTD . '</alerta></velte>';
            $xml .= '<veltd><valor>' . $VelocidadeTravesEsquerdo . '</valor><alerta>' . $AlertaVelTE . '</alerta></veltd>';
            $xml .= '<varcauda><valor>' . $VariacaoProa . '</valor><alerta>' . $AlertaVP . '</alerta></varcauda>';
            $xml .= '<varproa><valor>' . $VariacaoCauda . '</valor><alerta>' . $AlertaVC . '</alerta></varproa>';
            $xml .= '<vartd><valor>' . $VariacaoTravesEsq . '</valor><alerta>' . $AlertaVTE . '</alerta></vartd>';
            $xml .= '<varte><valor>' . $VariacaoTravesDir . '</valor><alerta>' . $AlertaVTD . '</alerta></varte>';
            $xml .= '<windshear><valor>' . $tipow . ' - ' . $txt . '</valor><alerta>' . $AlertaW . '</alerta></windshear>';
            $xml .= '</sdpva>';

            $data = new Zend_Date($dthr);
            $filename = $arqdata . ".xml";
            $arq = APP_XML . $local . '/alertas/' . $pista . '/' . $filename;
            try {
                $hc = fopen($arq, 'wb');
                fwrite($hc, $xml);
                fclose($hc);
                $this->gravarlog('info', "Arquivo XML gerado  $local  -  $arq", 'Processamento');
            } catch (Exception $e) {
                $this->gravarlog('err', utf8_encode($e->getMessage()), 'Processamento');
            }
        } catch (Exception $e) {
            $this->gravarlog('err', utf8_encode($e->getMessage()), 'Processamento');
        }
    }
    
    private function fcalcula1($var1, $var2, $var3) {
        if ($var1 >= $var2) {
            $txt = number_format($var1, 0) . ' kt em ' . $this->arredonda(number_format($var3 / 0.3048, 0, '.', '')) . ' ft';
        } else {
            $txt = abs(number_format($var1, 0)) . ' kt';
            if (number_format($var1, 0) != 0) {
                $txt .= ' em ' . $this->arredonda(number_format($var3 / 0.3048, 0, '.', '')) . ' ft';
            }
        }
        return $txt;
    }

    private function fcalcula2($var1, $var2, $var3) {
        $txt = number_format($var1, 0) . ' kt ';
        if ($var1 >= $var2) {
            $txt .= 'em ' . $this->arredonda(number_format($var3 / 0.3048, 0, '.', '')) . ' ft';
        } else {
            if (number_format($var1, 0) != 0) {
                $txt .= 'em ' . $this->arredonda(number_format($var3 / 0.3048, 0, '.', '')) . ' ft';
            }
        }
        return $txt;
}

 private function sqlRNA($variavel, $altura, $conexao, $limite){
		$sql = "SELECT ".$variavel." FROM sodar_atual WHERE z=".$altura." AND ".$variavel." < ".$limite." ORDER BY data DESC LIMIT 1";
		//echo $sql."<br/>";
		$result = $conexao->query($sql);
		$data = mysqli_fetch_assoc($result);
		if ($variavel=="speed") {
			return $data['speed']."";
		}elseif ($variavel=="dir") {
			return $data['dir']."";
		}elseif ($variavel=="TI") {
			return $data['TI']."";
		}elseif ($variavel=="TKE") {
			return $data['TKE']."";
		}elseif ($variavel=="EDR") {
			return $data['EDR']."";
		}elseif ($variavel=="U") {
			return $data['U']."";
		}elseif ($variavel=="V") {
			return $data['V']."";
		}elseif ($variavel=="sigW") {
			return $data['sigW']."";
		}

	}
    public function entradaRNA() {

##Dados de conexao com o banco
$server = "localhost";
$user = "root"; 
$senha = ""; 
$base = "sdpva"; 
$conexao = new mysqli($server, $user, $senha, $base);

if($conexao->connect_error)
{
	die("Erro na conexao (".mysqli_connect_errno().")".mysqli_connect_error());
}

##Criando a variavel de entrada
$entrada = array();
$entrada_ws = array();

## Pegando a ultima data processada
$sql = "SELECT datahora, dayofyear(DATE(datahora)) as dia_juliano, HOUR(datahora) as hora, MONTH(datahora) as mes FROM processamento ";
$result = $conexao->query($sql);
$result_data = mysqli_fetch_assoc($result);
//$data = $result_data['datahora'];
$data = "2016-04-11 18:30";
$dia_juliano = $result_data['dia_juliano'];
$hora = $result_data['hora'];
$mes = $result_data['mes'];

##Populando a variavel de entrada
array_push($entrada, $hora, $dia_juliano);
//array_push($entrada_ws,$mes,$hora);
############################################################################# DADOS DE VENTO SODAR #################################################################

//$data='2016-04-04 13:45:00';
$sql = "SELECT z, speed, dir, TI, TKE, EDR FROM sodar_atual WHERE data='$data' AND (z=30 OR z=60 OR z=90 OR z=120 OR z=150 OR z=180 OR z=210 OR z=240 OR z=270 OR z=300) ORDER BY z";
$result = $conexao->query($sql);
while($linha = $result->fetch_array(MYSQLI_BOTH)){
	
## Selecionando dados com TI até 180 metros
	if($linha['z'] < 210){			
		//if($linha['z']==30){
			##Velocidade
			if($linha['speed'] < 99.99){
				array_push($entrada,$linha['speed']);
			}
			else{
				$ret = $this->sqlRNA('speed',$linha['z'],$conexao,'99.99');
				array_push($entrada, $ret);
			}
			##Direcao
			if($linha['dir'] < 999){
				array_push($entrada,$linha['dir']);
			}
			else{
				$ret = $this->sqlRNA('dir',$linha['z'],$conexao,'999');
				array_push($entrada, $ret);
			}
			##TI
			if($linha['TI'] < 99.99){
				array_push($entrada,$linha['TI']);
			}
			else{
				$ret = $this->sqlRNA('TI',$linha['z'],$conexao,'99.99');
				array_push($entrada, $ret);
			}
			##TKE
			if($linha['TKE'] < 99.99){
				array_push($entrada,$linha['TKE']);
			}
			else{
				$ret = $this->sqlRNA('TKE',$linha['z'],$conexao,'99.99');
				array_push($entrada, $ret);
			}
			##EDR
			if($linha['EDR'] < 99.99){array_push($entrada,$linha['EDR']);}
			else{
				$ret = $this->sqlRNA('EDR',$linha['z'],$conexao,'99.99');

				array_push($entrada, $ret);
			}
			
		//}
		
		//array_push($entrada, $linha['speed'],$linha['dir'],$linha['TI'],$linha['TKE'],$linha['EDR']);
	}
	else{
		if($linha['speed'] < 99.99){array_push($entrada,$linha['speed']);}
		else{
			$ret = $this->sqlRNA('speed',$linha['z'],$conexao,'99.99');
			array_push($entrada, $ret);
		}
		##Direcao
		if($linha['dir'] < 999){array_push($entrada,$linha['dir']);}
		else{
			$ret = $this->sqlRNA('dir',$linha['z'],$conexao,'999');
			array_push($entrada, $ret);
		}		
		##TKE
		if($linha['TKE'] < 99.99){array_push($entrada,$linha['TKE']);}
		else{
			$ret = $this->sqlRNA('TKE',$linha['z'],$conexao,'99.99');
			array_push($entrada, $ret);
		}
		##EDR
		if($linha['EDR'] < 99.99){array_push($entrada,$linha['EDR']);}
		else{
			$ret = $this->sqlRNA('EDR',$linha['z'],$conexao,'99.99');
			array_push($entrada, $ret);
		}
		##sigW
		//if($linha['sigW'] < 99.99){array_push($entrada,$linha['sigW']);}
		//else{
		//	$ret = $this->sqlRNA('sigW',$linha['z'],$conexao,'99.99');
		//
		//	array_push($entrada, $ret);
		//}						
		//##U
		//if($linha['U'] < 99.99){array_push($entrada,$linha['U']);}
		//else{
		//	$ret = $this->sqlRNA('U',$linha['z'],$conexao,'99.99');
		//
		//	array_push($entrada, $ret);
		//}
		//##V
		//if($linha['V'] < 99.99){array_push($entrada,$linha['V']);}
		//else{
		//	$ret = $this->sqlRNA('V',$linha['z'],$conexao,'99.99');
		//
		//	array_push($entrada, $ret);
		//}
	
		//array_push($entrada, $linha['speed'],$linha['dir'],$linha['TKE'],$linha['EDR']);
	}
}
############################################################################# DADOS DE VENTO NA SUPERFICIE #################################################################

$sql = "SELECT runway, ws2a, wd2a FROM ems_wind WHERE timestamp='$data' ";
$result = $conexao->query($sql);
while($linha = $result->fetch_array(MYSQLI_BOTH))
{
	$runway = $linha['runway'];

	if($runway=="09R"){
		$ws2a_09R = $linha['ws2a'];
		$wd2a_09R = $linha['wd2a'];
	}
	elseif($runway=="09L"){
		$ws2a_09L = $linha['ws2a'];
		$wd2a_09L = $linha['wd2a'];
	}
	elseif($runway=="27R"){
		$ws2a_27R = $linha['ws2a'];
		$wd2a_27R = $linha['wd2a'];
	}
	elseif($runway=="27L"){
		$ws2a_27L = $linha['ws2a'];
		$wd2a_27L = $linha['wd2a'];
	}
}
## Media das medias de 2 minutos
$media_ws = ($ws2a_09R + $ws2a_27R + $ws2a_09L + $ws2a_27L)/4;

##Calculo do pico da media de 2 minutos
$pico_ws = $ws2a_09R;

if($ws2a_09L > $pico_ws){$pico_ws=$ws2a_09L;}
if($ws2a_27R > $pico_ws){$pico_ws=$ws2a_27R;}
if($ws2a_27L > $pico_ws){$pico_ws=$ws2a_27L;}


##Media ponderada de media_ws e o dobro do pico_ws
$speed_atual = ($media_ws + 2*$pico_ws)/3;

//echo $ws2a_09R." - ".$wd2a_09R." - ".$ws2a_09L." - ".$wd2a_09L." - ".$ws2a_27R." - ".$wd2a_27R." - ".$ws2a_27L." - ".$wd2a_27L." - ".$media_ws." - ".$speed_atual;

array_push($entrada, round($ws2a_09R,2),round($wd2a_09R,2),round($ws2a_27R,2),round($wd2a_27R,2));

############################################################################# DADOS DE PRESSAO NA SUPERFICIE #################################################################

## selecao de pressao atual e valor de th_uu e th_tt
$sql = "SELECT th_tt, th_uu, pa_qnh FROM ems_ptu WHERE timestamp='$data' ";

$result = $conexao->query($sql);
$linha = mysqli_fetch_assoc($result);

$th_tt = $linha['th_tt'];
$th_uu = $linha['th_uu'];
$pa_qnh_atual = $linha['pa_qnh'];

//echo $pa_qnh_atual." - ";

## selecao de pressao de 3 horas atras
$sql = "SELECT  pa_qnh as pa_qnh_3h FROM ems_ptu WHERE timestamp=DATE_SUB('$data', INTERVAL 180 MINUTE)";


$result = $conexao->query($sql);
$linha = mysqli_fetch_assoc($result);

$pa_qnh_3h = $linha['pa_qnh_3h'];
//echo $pa_qnh_3h." - ";

## selecao de pressao de 6 horas atras
$sql = "SELECT pa_qnh as pa_qnh_6h FROM ems_ptu WHERE timestamp=DATE_SUB('$data', INTERVAL 360 MINUTE)";

$result = $conexao->query($sql);
$linha = mysqli_fetch_assoc($result);

$pa_qnh_6h = $linha['pa_qnh_6h'];

$dif_3pa = $pa_qnh_atual - $pa_qnh_3h;
$dif_6pa = $pa_qnh_atual - $pa_qnh_6h;


//echo $th_tt." - ".$th_uu." - ".$dif_3pa." - ".$dif_6pa;

array_push($entrada, round($th_tt,2),round($th_uu,2),round($dif_3pa,2),round($dif_6pa,2), round($media_ws,2), round($speed_atual,2));

############################CONFERIR E REMOVER ISSO AQUI#######################################################
$entrada_sup = array();
array_push($entrada_sup, round($ws2a_09R,2),round($wd2a_09R,2),round($ws2a_27R,2),round($wd2a_27R,2), round($th_tt,2),round($th_uu,2),round($dif_3pa,2),round($dif_6pa,2), round($media_ws,2), round($speed_atual,2));

file_put_contents("c:/wamp64/www/sdpva/rna/entrada/entrada_altitude_2016_04_11_18_30.txt", implode(" ", $entrada));
file_put_contents("c:/wamp64/www/sdpva/rna/entrada/entrada_superficie_2016_04_11_18_30.txt", implode(" ", $entrada_sup));


#################################################################### MONTANDO ENTRADA PARA WINDSHEAR ############################################################################

## selecao de pressao atual, valor de th_uu, th_tt th_td e ground_t
$sql = "SELECT pa_qnh, th_tt, th_uu, th_td, ground_t FROM ems_ptu WHERE timestamp='$data' ";

$result = $conexao->query($sql);
$linha = mysqli_fetch_assoc($result);
$pa_qnh_ws = $linha['pa_qnh'];
$th_tt = $linha['th_tt'];
$th_uu = $linha['th_uu'];
$th_td = $linha['th_td'];
$ground_t = $linha['ground_t'];

## selecao de pressao de 15 minutos atras
$sql = "SELECT  pa_qnh as pa_qnh_ws_anterior FROM ems_ptu WHERE timestamp=DATE_SUB('$data', INTERVAL 15 MINUTE)";

$result = $conexao->query($sql);
$linha = mysqli_fetch_assoc($result);

$pa_qnh_ws_anterior = round($pa_qnh_ws - $linha['pa_qnh_ws_anterior'],2);

//array_push($entrada_ws,$pa_qnh_ws,$pa_qnh_ws_anterior,$th_tt, $th_uu, $th_td, $ground_t);

$sql = "SELECT z, speed, dir, TI, TKE, sigW, U, V FROM sodar_atual WHERE data='$data' AND (z=30 OR z=60 OR z=90 OR z=120 OR z=150 OR z=180  OR z=210 OR z=240 OR z=270 OR z=300) ORDER BY z";
$result = $conexao->query($sql);
while($linha = $result->fetch_array(MYSQLI_BOTH)){
	
	#Velocidade
	if($linha['speed'] < 99.99){array_push($entrada_ws,$linha['speed']);}
	else{
		$ret = $this->sqlRNA('speed',$linha['z'],$conexao,'99.99');
		array_push($entrada_ws, $ret);
	}
	##Direcao
	//if($linha['dir'] < 999){array_push($entrada_ws,$linha['dir']);}
	//else{
	//	$ret = $this->sqlRNA('dir',$linha['z'],$conexao,'999');
	//	array_push($entrada_ws, $ret);
	//}
	##U
	if($linha['U'] < 99.99){array_push($entrada_ws,$linha['U']);}
	else{
		$ret = $this->sqlRNA('U',$linha['z'],$conexao,'99.99');

		array_push($entrada_ws, $ret);
	}
	##V
	if($linha['V'] < 99.99){array_push($entrada_ws,$linha['V']);}
	else{
		$ret = $this->sqlRNA('V',$linha['z'],$conexao,'99.99');

		array_push($entrada_ws, $ret);
	}
	##sigW
	if($linha['sigW'] < 99.99){array_push($entrada_ws,$linha['sigW']);}
	else{
		$ret = $this->sqlRNA('sigW',$linha['z'],$conexao,'99.99');

		array_push($entrada_ws, $ret);
	}						
			
	##TKE
	if($linha['TKE'] < 99.99){array_push($entrada_ws,$linha['TKE']);}
	else{
		$ret = $this->sqlRNA('TKE',$linha['z'],$conexao,'99.99');
		array_push($entrada_ws, $ret);
	}
	##TI
	//if($linha['TI'] < 99.99){
	//	array_push($entrada_ws,$linha['TI']);
	//}
	//else{
	//	$ret = $this->sqlRNA('TI',$linha['z'],$conexao,'99.99');
	//	array_push($entrada_ws, $ret);
	//}
	
}
file_put_contents("c:/wamp64/www/sdpva/rna/entrada/entrada_ws_2016_04_11_18_30.txt", implode(" ", $entrada_ws));

#Modificando a flag para executar a rede neural
$sql = "UPDATE processamento SET flag_RNA=1";
$conexao->query($sql);



    }

}
