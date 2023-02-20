<?php

class HttpStatusCode
{
    static private $httpStatusCode = array(
        100 => 'Continuar.',
        101 => 'Protocolos de Comuta��o.',
        102 => 'Processamento.',
        200 => 'OK.',
        201 => 'Criado.',
        202 => 'Aceito.',
        203 => 'Informa��es n�o autorizadas.',
        204 => 'Sem Conte�do.',
        205 => 'Redefinir Conte�do.',
        206 => 'Conte�do Parcial.',
        207 => 'V�rios Status.',
        208 => 'J� Informado.',
        226 => 'IM Usado.',
        300 => 'M�ltiplas Escolhas.',
        301 => 'Movido Permanentemente.',
        302 => 'Encontrado.',
        303 => 'Ver Outros.',
        304 => 'N�o Modificado.',
        305 => 'Usar proxy.',
        307 => 'Redirecionamento Tempor�rio.',
        308 => 'Redirecionamento Permanente.',
        400 => 'Solicita��o Inv�lida.',
        401 => 'N�o Autorizado.',
        402 => 'Pagamento Necess�rio.',
        403 => 'Proibido.',
        404 => 'N�o Encontrado.',
        405 => 'M�todo N�o Permitido.',
        406 => 'N�o Aceit�vel.',
        407 => 'Autentica��o Proxy Necess�ria.',
        408 => 'Tempo limite de solicita��o.',
        409 => 'Conflito.',
        410 => 'Desaparecido.',
        411 => 'Comprimento Necess�rio.',
        412 => 'Falha na pr�-condi��o.',
        413 => 'Carga Muito Grande.',
        414 => 'Request-URI muito longo.',
        415 => 'Tipo de m�dia n�o suportado.',
        416 => 'Faixa Solicitada N�o Satisfat�ria.',
        417 => 'Expectativa falhou.',
        418 => 'eu sou um bule.',
        421 => 'Solicita��o mal direcionada.',
        422 => 'Entidade N�o Process�vel.',
        423 => 'Bloqueado.',
        424 => 'Depend�ncia com Falha.',
        426 => 'Atualiza��o Necess�ria.',
        428 => 'Pr�-condi��o Necess�ria.',
        429 => 'Muitos Pedidos.',
        431 => 'Campos de cabe�alho de solicita��o muito grandes.',
        444 => 'Conex�o Fechada Sem Resposta.',
        451 => 'Indispon�vel por motivos legais.',
        499 => 'Solicita��o Fechada do Cliente.',
        500 => 'Erro interno do servidor.',
        501 => 'N�o Implementado.',
        502 => 'Gateway Inv�lido.',
        503 => 'Servi�o indispon�vel.',
        504 => 'Tempo limite do gateway.',
        505 => 'Vers�o HTTP n�o suportada.',
        506 => 'Variante Tamb�m Negocia.',
        507 => 'Armazenamento Insuficiente.',
        508 => 'Loop Detectado.',
        510 => 'N�o Estendido.',
        511 => 'Autentica��o de Rede Necess�ria.',
        599 => 'Erro de tempo limite de conex�o de rede.',
    );

    static function getMessage($statusCode = 200, $convertToUtf8 = false)
    {
        $statusText = self::$httpStatusCode[$statusCode];
        if ($convertToUtf8) {
            return mb_convert_encoding(
                $statusText,
                'utf-8',
                'iso8859-1'
            );
        }

        return $statusText;
    }
}
