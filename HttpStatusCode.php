<?php

class HttpStatusCode
{
    static private $httpStatusCode = array(
        100 => 'Continuar.',
        101 => 'Protocolos de Comutação.',
        102 => 'Processamento.',
        200 => 'OK.',
        201 => 'Criado.',
        202 => 'Aceito.',
        203 => 'Informações não autorizadas.',
        204 => 'Sem Conteúdo.',
        205 => 'Redefinir Conteúdo.',
        206 => 'Conteúdo Parcial.',
        207 => 'Vários Status.',
        208 => 'Já Informado.',
        226 => 'IM Usado.',
        300 => 'Múltiplas Escolhas.',
        301 => 'Movido Permanentemente.',
        302 => 'Encontrado.',
        303 => 'Ver Outros.',
        304 => 'Não Modificado.',
        305 => 'Usar proxy.',
        307 => 'Redirecionamento Temporário.',
        308 => 'Redirecionamento Permanente.',
        400 => 'Solicitação Inválida.',
        401 => 'Não Autorizado.',
        402 => 'Pagamento Necessário.',
        403 => 'Proibido.',
        404 => 'Não Encontrado.',
        405 => 'Método Não Permitido.',
        406 => 'Não Aceitável.',
        407 => 'Autenticação Proxy Necessária.',
        408 => 'Tempo limite de solicitação.',
        409 => 'Conflito.',
        410 => 'Desaparecido.',
        411 => 'Comprimento Necessário.',
        412 => 'Falha na pré-condição.',
        413 => 'Carga Muito Grande.',
        414 => 'Request-URI muito longo.',
        415 => 'Tipo de mídia não suportado.',
        416 => 'Faixa Solicitada Não Satisfatória.',
        417 => 'Expectativa falhou.',
        418 => 'eu sou um bule.',
        421 => 'Solicitação mal direcionada.',
        422 => 'Entidade Não Processável.',
        423 => 'Bloqueado.',
        424 => 'Dependência com Falha.',
        426 => 'Atualização Necessária.',
        428 => 'Pré-condição Necessária.',
        429 => 'Muitos Pedidos.',
        431 => 'Campos de cabeçalho de solicitação muito grandes.',
        444 => 'Conexão Fechada Sem Resposta.',
        451 => 'Indisponível por motivos legais.',
        499 => 'Solicitação Fechada do Cliente.',
        500 => 'Erro interno do servidor.',
        501 => 'Não Implementado.',
        502 => 'Gateway Inválido.',
        503 => 'Serviço indisponível.',
        504 => 'Tempo limite do gateway.',
        505 => 'Versão HTTP não suportada.',
        506 => 'Variante Também Negocia.',
        507 => 'Armazenamento Insuficiente.',
        508 => 'Loop Detectado.',
        510 => 'Não Estendido.',
        511 => 'Autenticação de Rede Necessária.',
        599 => 'Erro de tempo limite de conexão de rede.',
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
