<!DOCTYPE html>
<html lang="pt" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <!-- NAME: FOLLOW UP -->
    <!--[if gte mso 15]>
    <xml>
        <o:OfficeDocumentSettings>
        <o:AllowPNG/>
        <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
    </xml>
    <![endif]-->    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>*|MC:SUBJECT|*</title>
    <style type="text/css">
    body {padding: 20px; color: #3d3d3d;}
    h1 {font-size: 12px; font-family:"Lucida Sans", "Lucida Sans Regular", "Lucida Grande", "Lucida Sans Unicode", "Geneva", "Verdana", "sans-serif";  text-align: justify;}
    h3 {font-size: 12px; font-family:"Lucida Sans", "Lucida Sans Regular", "Lucida Grande", "Lucida Sans Unicode", "Geneva", "Verdana", "sans-serif";  text-align: justify;}
    p {font-size: 12px; font-family: "Lucida Sans", "Lucida Sans Regular", "Lucida Grande", "Lucida Sans Unicode", "Geneva", "Verdana", "sans-serif"; text-align: justify;}
    .marcacao {font-weight: bold; color: #AD60A6;}
    .confirmButton { background-color:#44c767;border-radius:28px;border:1px solid #18ab29;display:inline-block;cursor:pointer;color:#ffffff;font-family:Arial;font-size:17px; padding:16px 31px; text-decoration:none; text-shadow:0px 1px 0px #2f6627;}
    .unsubscribe-info {font-size: 10px; color: #7c7c7c;}
    .menu {font-size: 12px; color: #7c7c7c;}
    @media only screen and (min-width: 600px)  {
        body {padding: 120px; color: #3d3d3d;}
    }
    </style>
</head>
<body>
    <h1>Bem-vindo(a) a Sweet!</h1>
    <p>Agora basta seu confirmar seu e-mail para aproveitar todos os nossos benefícios. Confirmando, <span class="marcacao">você automaticamente ganha 30 pontos</span>, além de participar de atividades para ganhar ainda mais e <span class="marcacao">poder trocar por prêmios!</span></p>

    @php
        $siteOrigin   = explode("/", $customer['site_origin']);
        $siteOrigin   = $siteOrigin[3] ?? 'default';
        $showDownload = ['dinheiro-na-internet', 'emagrecimento', 'revenda'];
    @endphp

    @if(in_array($siteOrigin, $showDownload))
        <p><br><center><a href="{{ env('STORE_URL') }}/login/verify/{{ $customer['confirmation_code'] }}" target="_blank" class="confirmButton">BAIXAR E-BOOK / CONFIRMAR E-MAIL</a></center></p>
    @else
        <p><br><center><a href="{{ env('STORE_URL') }}/login/verify/{{ $customer['confirmation_code'] }}" target="_blank" class="confirmButton">CONFIRMAR E-MAIL</a></center></p>
    @endif
    
    <br>
    <h3>Como fazer o login no portal da Sweet?<br> É muuuito simples: </h3>
    <p>Acesse store.sweetbonus.com.br, digite seu <span class="marcacao">e-mail</span> e depois sua <span class="marcacao">data de nascimento</span>. É só isso!</p>
    <p><br><br>
    -- <br>
    Atenciosamente,<br>
    Equipe Sweet.</p>

    <br>
    <p class="unsubscribe-info">A Sweet preza muito por não encher a sua caixa de correio de e-mail não desejados, por isso você pode se descadastrar a 
        qualquer momento logando em sua conta e acessando a opção MINHA CONTA > DESCADASTRO. Em caso de dúvidas, envie-nos um e-mail para contato@sweetpanels.com. 
        Será um prazer atendê-lo!<br><br><br><br>
            <center><a href="https://play.google.com/store/apps/details?id=com.appsweetbonus" target="_blank"><img src="https://uploaddeimagens.com.br/images/002/670/450/full/pt-br_badge_web_generic-min.png?1590417493" style="width:200px;"alt=""></a></center>
            <center><img src="https://i.imgur.com/7SJORFL.jpg" alt="Logo Sweet" style="width: 100px;"></center>
            <center><span class="menu">A Sweet é uma plataforma em que qualquer pessoa pode realizar tarefas simples na internet, ganhar pontos e trocar por produtos.</span>
            <center><span class="menu"><a href="http://sweetbonus.com.br/termos-e-condicoes">Termos e Condições</a> | <a href="http://sweetbonus.com.br/politica-de-privacidade">Política de Privacidade</a></span></center>
            <center><span class="menu">© 2020 Sweet Media Marketing Ltda</span></center>
    </p>

</body>
</html>