<?php
/*
 * Controle de visitas.
 *  - Cada acesso/recarga (F5, Ctrl+R) da página principal conta +1...
 *  - ...EXCETO quando o usuário está logado;
 *  - ...e EXCETO na página exibida logo após o logout (flag "sem_contar").
 * Chamar depois de session_start() e ANTES de qualquer HTML.
 */
function numero_visitas(): int
{
    $fp = fopen(__DIR__ . '/arq.in', 'c+')
        or die('Sem permissão para gravar o arquivo arq.in (dê permissão de escrita à pasta).');
    flock($fp, LOCK_EX);
    $n = (int) stream_get_contents($fp);

    if (!empty($_SESSION['usuario_id'])) {
        // logado: não conta
    } elseif (!empty($_SESSION['sem_contar'])) {
        unset($_SESSION['sem_contar']); // logout: pula só este carregamento
    } else {
        $n++;
        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, (string) $n);
    }
    flock($fp, LOCK_UN);
    fclose($fp);
    return $n;
}
