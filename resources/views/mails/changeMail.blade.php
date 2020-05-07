<!DOCTYPE html>
<html>
<head>
    <title>Smart Publishers</title>
</head>

<body>
<h2>Como você está? {{ $user['name'] }}.</h2>
<br/>
    Seu endereço de e-mail foi alterado para {{ $user['email'] }}.<br><br>
    Clique no link abaixo para verificar sua conta de e-mail.<br><br>
<br/>
<a href="/account/verifyEmail/{{ $user->verifyUser->token }}">Verifique o sei E-mail</a>
</body>

</html>