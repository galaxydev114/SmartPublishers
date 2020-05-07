<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:v="urn:schemas-microsoft-com:vml">
<head>
<!--[if gte mso 9]><xml><o:OfficeDocumentSettings><o:AllowPNG/><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml><![endif]-->
<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
<meta content="width=device-width" name="viewport"/>
<!--[if !mso]><!-->
<meta content="IE=edge" http-equiv="X-UA-Compatible"/>
<!--<![endif]-->
<title>Smart Publishers</title>
<!--[if !mso]><!-->
<link href="https://fonts.googleapis.com/css?family=Cormorant+Garamond|Roboto&display=swap" rel="stylesheet" type="text/css"/>
<!--<![endif]-->
<style type="text/css">
*{font-family: 'Roboto', sans-serif;box-sizing: border-box;margin: 0;padding: 0;}
.main{background-image: url('/assets/img/mail/Image_004.jpg');background-repeat: no-repeat;background-size: cover;}
.wrapper{padding: 0 200px;}
.pass-text{color: #cc9966 !important;display: block;font-weight: bold;}
.header{position: relative;height: 200px;text-align: center;}
.browser-link{display: block;color: #9BA6BC !important;padding-top: 20px;text-align: right;font-size:24px;}
.browser-link:hover{color: #fff;}
.logo-img{width: 400px;height: 140px;}
.content{background: #fff;border-radius: 5px;text-align: center;padding-top: 80px;}
.banner{width: 100%;margin-top: 80px;}
.title{font-size: 50px;font-family: 'Cormorant Garamond', serif;}
.content-p{font-size: 24px;margin: 0;}
.golden{color: #cc9966 !important;}
.email-text{color: #cc9966 !important;font-weight: bold !important;margin: 0;font-size: 24px;}
.email-text a{color: #cc9966 !important;font-weight: bold !important;}
.verify-email-link{font-weight: bold !important;font-size:24px;color: #cc9966;text-decoration: underline;}
.line-golden{margin: 20px 240px 40px;height: 1px;background-color: #cc9966 !important;}
.text-team{font-family: 'Cormorant Garamond', serif;font-style: italic;margin: 0;font-size: 24px;}
.footer{text-align: center;padding: 80px 0;}
.footer-a{font-size: 24px;color:#fff !important;}
.choptsite-a{margin-right: 40px;font-size:24px;}
.footer-span{color: #fff;font-size: 24px;}
.top-br{display: none;}
@media(max-width: 768px){
.wrapper{padding: 0 20px;}
.title{font-size: 32px;}
.header{height: 140px;}
.logo-img{width: 230px;height: 80px;}
.content{margin-top: -20px;padding-top: 30px;}
.line-golden {margin: 10px 40px 20px;}
.banner {margin-top: 40px;}
.footer {padding: 20px 0;}
.choptsite-a{margin: 0;}
.footer-span{font-size: 20px;}
.verify-email-link{font-size: 20px;}
.click-here{display: inline-block;}
.text-team{font-size: 20px;}
.email-text{font-size: 20px;}
.content-p{font-size: 20px;}
.browser-link{font-size: 20px;}
.footer-a{font-size: 20px;}
.top-br{display: block;}
}
</style>
</head>

<body>
<div class="main" style="
            background-image: url('{{ asset("assets/img/mail/Image_004.jpg") }}');
            background-repeat: no-repeat;
            background-size: cover;
">
    <div class="wrapper">
        <a class="browser-link" href="{{ url('/showVerifyEmail/'.$user->id.'/'.$user->password) }}">Ver no navegador</a>
        <div class="header">
            <img class="logo-img" src="{{ asset('assets/img/mail/golden_logo.png') }}">
        </div>
        <div class="content" style="background-color: #fff;">
            <div>
                <div class="title" style="font-family: 'Cormorant Garamond', serif; color: #000;">Bem-vindo ao treinamento Smart Publishers</div>
                <div class="title golden">{{ $user['name'] }}!</div>
                <div class="line-golden"></div>
                <p class="content-p">Seu email registrado é:</p>
                <p class="golden email-text">{{ $user['email'] }}</p>
                <br>
                <p class="content-p">Clique no link abaixo para validar sua conta de e-mail e<br>
                faça o login com esta senha padrão. <span class="pass-text">{{ $user['password'] }}</span>Depois de fazer login, você poderá alterar sua senha.</p>
                <br>
                <a href="{{ url('verifyEmail', $user->verifyUser->token) }}" class="golden verify-email-link">Validar endereço de email</a>
                <br class="top-br"><br class="top-br">
                <p class="content-p">Aproveite sua jornada,</p>
                <p class="text-team">Equipe de treinamento da Smart Publishers</p>
            </div>
            <img class="banner" src="{{ asset('assets/img/mail/Image_001.jpg') }}">
        </div>
        <div class="footer">
            <a class="footer-a choptsite-a" href="{{ url('/') }}">Smart Publishers Site</a>
            <a class="footer-a" href="https://smartpublishers.co">Smart Publishers Site</a><br><br>
            <span class="footer-span">Se você não consegue ver este e-mail corretamente, por favor </span><a class="footer-a" href="{{ url('/showVerifyEmail/'.$user->id.'/'.$user->password) }}">Clique aqui</a>
        </div>
    </div>
</div>

</body>

</html>

