<?php

Utils::ajaxHeader();

if($request->get('action') == 'send-mail'){

    $nome = $request->post('nome');
    $email = $request->post('email');
    $telefone = $request->post('telefone');
    $mensagem = $request->post('mensagem');
    $interesse = $request->post('interesse');
    $tipo = $request->post('tipo', 'contato');
    
    if ($nome == '' || !Utils::checkEmail($email) || $telefone == '' || $mensagem == '') {
        Utils::jsonResponse('Os campos n&atilde;o foram preenchidos corretamente');
    }

    if(!Security::csrfIsValid()){
        Utils::jsonResponse('Invalid CSRF Token');
    }

    if ($tipo == 'servico') {
        $interesse = '<p>Contato interessado no serviço: '.$interesse.'</p>';
    }


    $msg = 
    $interesse."
    <p>
        <strong>Nome:</strong> {$nome}<br/>
        <strong>Telefone:</strong> {$telefone}<br/>
        <strong>Email:</strong> <a href=\"mailto:{$email}\">{$email}</a><br>
        <strong>Mais detalhes:</strong><br>" . nl2br($mensagem) . "
    </p>";
    

    $destinatarios = explode(',', (string) $Config->get('email'));

    $rs = Lead::search([
        's' => 'id',
        'w' => "email='{$email}'"
    ]);
    if($rs->numRows() == 0){
        $objL = new Lead();
        $objL->set('nome', $nome);
        $objL->set('email', $email);
        $objL->set('tel', Utils::replace('/[^0-9]/','',$telefone));
        $objL->dbInsert();
    }
    
    Mail::schedule([
        'from' => $email,
        'fromName' => $nome,
        'subject' => 'Contato via site',
        'to' => $destinatarios,
        'message' => $msg,
        'attachment' => [],
        'bcc' => [],
    ]);
    
    $ptsNome = explode(' ',$nome);
    FBApi::event('Contact', 'Site_Contact', [
        'email' => $email,
        'phone' => Utils::replace('/[^0-9]/','',$telefone),
        'first_name' => $ptsNome[0],
        'last_name' => str_replace($ptsNome[0].' ','',$nome),
    ]);

    Utils::jsonResponse('Mensagem enviada com sucesso. Em breve entraremos em contato.', true);

}

Utils::jsonResponse('A&ccedil;&atilde;o incorreta.');