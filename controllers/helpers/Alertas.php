<?php

require_once 'processodb.php';
require_once 'parametrosdb.php';
require_once 'dadosderivadosdb.php';
require_once 'alertasdb.php';

class Zend_Controller_Action_Helper_Alertas extends Zend_Controller_Action_Helper_Abstract {


    public function alertas($local,$dthr) {

        $localx = 1;

        $dtah = substr($dthr,0,4) . "-" . substr($dthr,4,2) . "-" . substr($dthr,6,2) . " " . substr($dthr,8,2) . ":" . substr($dthr,10,2) . ":00";
        $dtxml = substr($dthr,0,4) . "-" . substr($dthr,4,2) . "-" . substr($dthr,6,2) . "T" . substr($dthr,8,2) . ":" . substr($dthr,10,2) . ":00";
        // variáveis
        $pista = '09';
        
        // le dados_derivados
        $dadosderivadosdb = new dadosderivadosdb();

        // le parametros
        $paramdb = new parametrosdb();
        $paramData = $paramdb->getParametros();

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
        $dadosderivadosData = $dadosderivadosdb->getVelocidadeCauda($dtah);
        $dadosVelCauda = $dadosderivadosData['cauda'];
        $VelocidadeCauda = $this->fcalcula1($dadosVelCauda, $wind_int_cauda, $dadosderivadosData['altura']);

        // Velocidade de Proa
        $dadosderivadosData = $dadosderivadosdb->getVelocidadeProa($dtah);
        $dadosVelProa = $dadosderivadosData['proa'];
        $VelocidadeProa = $this->fcalcula1($dadosVelProa, $wind_int_proa, $dadosderivadosData['altura']);

        // Velocidade Través Esquerdo
        $dadosderivadosData = $dadosderivadosdb->getTravesEsquerdo($dtah);
        $dadosVelTravesEsquerdo = $dadosderivadosData['traves_esq'];
        $VelocidadeTravesEsquerdo = $this->fcalcula1($dadosVelTravesEsquerdo, $wind_int_traves, $dadosderivadosData['altura']);

        // Velocidade Través Direito
        $dadosderivadosData = $dadosderivadosdb->getTravesDireito($dtah);
        $dadosVelTravesDireito = $dadosderivadosData['traves_dir'];
        $VelocidadeTravesDireito = $this->fcalcula1($dadosVelTravesDireito, $wind_int_traves, $dadosderivadosData['altura']);

        // Variação de Cauda $var1 = $dadosVarCauda, $var2 = $wind_var_cauda, $var3 = $dadosderivadosData['altura']
        $dadosderivadosData = $dadosderivadosdb->getVariacaoCauda($dtah);
        $dadosVarCauda = $dadosderivadosData['var_cauda'];
        $VariacaoCauda = $this->fcalcula2($dadosVarCauda, $wind_var_cauda, $dadosderivadosData['altura']);

        // Variação de Proa
        $dadosderivadosData = $dadosderivadosdb->getVariacaoProa($dtah);
        $dadosVarProa = $dadosderivadosData['var_proa'];
        $VariacaoProa = $this->fcalcula2($dadosVarProa, $wind_var_proa, $dadosderivadosData['altura']);

        // Variação Través Direito
        $dadosderivadosData = $dadosderivadosdb->getVariacaoTravesDir($dtah);
        $dadosVarTravesDir = $dadosderivadosData['var_traves_dir'];
        $VariacaoTravesDir = $this->fcalcula2($dadosVarTravesDir, $wind_var_proa, $dadosderivadosData['altura']);

        // Variação Través Esquerdo
        $dadosderivadosData = $dadosderivadosdb->getVariacaoTravesEsq($dtah);
        $dadosVarTravesEsq = $dadosderivadosData['var_traves_esq'];
        $VariacaoTravesEsq = $this->fcalcula2($dadosVarTravesEsq, $wind_var_proa, $dadosderivadosData['altura']);

        // Windshear
        $dadosderivadosData = $dadosderivadosdb->getWindshear($dtah);
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
                'datahora' => $dtah,
                'pista' => $pista,
                'local' => $localx,
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
            } catch (Exception $e) {
                $this->gravarlog('err', utf8_encode($e->getMessage()), 'Alertas');
            }

            // grava arquivo xml
            $xml = "<?xml version='1.0' encoding='UTF-8'?><sdpva local='".$local."' pista='" . $pista . "' datahora='" . $dtxml . "' tipo='A' >";
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

            $filename = $dthr . ".xml";
            $arq = APP_XML . $local . '/alertas/' . $pista . '/' . $filename;
            try {
                $hc = fopen($arq, 'wb');
                fwrite($hc, $xml);
                fclose($hc);
            } catch (Exception $e) {
                $this->gravarlog('err', utf8_encode($e->getMessage()),'Alertas');
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
                'datahora' => $dtah,
                'pista' => $pista,
                'local' => $localx,
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
            } catch (Exception $e) {
                $this->gravarlog('err', utf8_encode($e->getMessage()),'Alertas');
            }

            // grava arquivo xml
            $xml = "<?xml version='1.0' encoding='UTF-8'?><sdpva local='".$local."' pista='" . $pista . "' datahora='" . $dtxml . "' tipo='A' >";
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

            $filename = $dthr . ".xml";
            $arq = APP_XML . $local . '/alertas/' . $pista . '/' . $filename;
            try {
                $hc = fopen($arq, 'wb');
                fwrite($hc, $xml);
                fclose($hc);
            } catch (Exception $e) {
                $this->gravarlog('err', utf8_encode($e->getMessage()),'Alertas');
            }
        } catch (Exception $e) {
            $this->gravarlog('err', utf8_encode($e->getMessage()),'Alertas');
        }
        
        $this->gravarlog('info', "Alertas processados -  $local - $dthr", 'Alertas');
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
}