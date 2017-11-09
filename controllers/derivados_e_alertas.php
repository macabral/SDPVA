<?php

$server = "localhost";
$user = "sdpva";
$senha = "sdpva*#";
$base = "sdpva";
$conexao = mysql_connect($server, $user, $senha) || die("Erro na conex�o!");
mysql_select_db($base);

##Fazer verifica��o do ultimo dado na tabela de dados_derivados pra depois fazer essa busca abaixo

$sql_data_max = "SELECT MAX(s2.data) as dataMaxima FROM sodar_atual s2";
$result_data_max = mysql_query($sql_data_max) || die($sql_data_max . "<br/><br/>" . mysql_error());
while ($linha_data_max = mysql_fetch_array($result_data_max)) {
//while ($linha_data_max = mysql_result($result_data_max,0)) {
    $data_max = $linha_data_max['dataMaxima'];
}

//Ajuste de componentes U e V
$sql_uv = "UPDATE sodar_atual s SET U= ROUND(s.speed*SIN(RADIANS(s.dir)),2), V= ROUND(s.speed*COS(RADIANS(s.dir)),2) WHERE s.data='$data_max' and s.speed<99.99";
$result_uv = mysql_query($sql_uv) || die($sql_uv . "<br/><br/>" . mysql_error());


$sql = "SELECT s.data, z, dir, speed FROM sodar_atual s where s.data='$data_max' AND s.dir<999.9;";
$result = mysql_query($sql) || die($sql . "<br/><br/>" . mysql_error());

$return = "";
while ($linha = mysql_fetch_array($result)) {

    $data = $linha["data"];
    $z = $linha["z"];
    $dir = $linha["dir"];
    $speed = $linha["speed"];
    ##Orienta�ao da pista de esquerda para a direita 
    ## Proa>=10 cauda>=2  traves>= 5

    $alinhado_real = number_format(cos(deg2rad($dir - 70)) * $speed / 0.51444, 2);
    if ($alinhado_real < 0) {
        $proa = number_format($alinhado_real * -1, 2);
        $cauda = 0;
    } else {
        $cauda = number_format($alinhado_real, 2);
        $proa = 0;
    }

    $traves_real = number_format(sin(deg2rad($dir - 70)) * $speed / 0.51444, 2);
    if ($traves_real < 0) {
        $traves_esq = number_format($traves_real * -1, 2);
        $traves_dir = 0;
    } else {
        $traves_dir = number_format($traves_real, 2);
        $traves_esq = 0;
    }
    $sql2 = "INSERT INTO dados_derivados VALUES($alinhado_real, $traves_real, $cauda, $proa, $traves_dir, $traves_esq, '$data', $z );";
    mysql_query($sql2);
}
?>
