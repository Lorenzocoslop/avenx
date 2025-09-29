# flexbase

Base de projetos da Lev Sistemas

Após criar o projeto, lembre de rodar o "composer install"

Pra criar uma chave nova na conta do cliente (não coloque frase secreta):
ssh-keygen -t rsa -b 4096 -C "eliemarjunior@gmail.com"

Confira a chave gerada:
cat ~/.ssh/id_rsa.pub

Cadastre no Github, depois teste a conexão:
ssh -T git@example.com

Adicionando comando para autodeploy
git remote set-url --add --push origin ssh://flexpoin@flexpoint.com.br:19000/home/flexpoin/public_html/dev/baseprojects

## Renomeie o .env.example em _core para apenas .env e defina as configurações que preferir. 

## Se for utilizar o envio de email via google execute siga os seguintes passos
```
composer require google/apiclient
composer require league/oauth2-google
```

Acesse o endereço <a href="https://flexpoint.com.br/goauth_token.php">https://flexpoint.com.br/goauth_token.php</a> para pegar o RefreshToken

Em posse do RefreshToken, altere o .env para
```
MAIL_DRIVER=XOAUTH2
MAIL_PORT=465
MAIL_ENCRYPTION=SSL
MAIL_PASSWORD=seurefreshtokenaqui
```
