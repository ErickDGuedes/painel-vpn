# Painel VPN - Gerenciamento de Usuários e Certificados

Este projeto é uma atividade de criação de um painel para gerenciar usuários através de uma VPN. Com o objetivo de permitir que funcionários e administradores controlem o acesso via certificados individuais.
O sistema está configurado para rodar em uma Máquina Virtual Linux, com Apache2 instalado para servir os arquivos PHP. O banco de dados utilizado é MySQL/MariaDB, também configurado dentro da VM.    

Atualmente, o painel permite:

- Criar e gerenciar usuários;
- Definir administradores;
- Ativar, desativar e excluir usuários;
- Gerenciar certificados VPN (criar e revogar).

Embora essas funcionalidades básicas já estejam implementadas, o sistema ainda está em desenvolvimento e algumas funcionalidades, como o download automático dos arquivos de configuração VPN e a recuperação de senha via e-mail, ainda não foram finalizadas.

## Funcionalidades implementadas

- Login com limitações para tentativas incorretas.
- Cadastro e listagem de usuários com filtros e ordenação.
- Elevação e revogação de privilégios de administrador.
- Criação e remoção de certificados VPN.
- Interface diferentes para administradores e funcionários.

## Funcionalidades pendentes

- Envio de link único para redefinição de senha por e-mail.
- Download seguro dos arquivos de configuração VPN (.zip).
- Interface para primeiro acesso e recuperação de senha.

## Tecnologias usadas

- PHP 7.4+ para o backend e lógica do sistema.
- MySQL/MariaDB para banco de dados relacional.
- Apache2 como servidor web.
- HTML5, CSS3 e JavaScript para a interface do usuário.
- Postfix (ou outro servidor SMTP) para envio de e-mails (planejado).
- OpenVPN para gerenciar os certificados e conexões VPN.

## Configuração da Máquina Virtual (VM)

> Considerando que a VM já possui o serviço de OpenVPN instalado, precisa instalar os serviços do Apache2 e PHP, abaixo o passo a passo.

### 1. Atualize os pacotes da VM
```
sudo apt update -y
```
### 2. Instale o Apache2
```
sudo apt install apache2 -y
```
>Verifique se o serviço está ativo:
```
sudo systemctl status apache2.service
```
>Caso não esteja:
```
sudo systemctl enable apache2.service
```
### 3. Instale o PHP

sudo apt install php -y

>Reinicie o serviço do apache2 para integrar o PHP

sudo systemctl restart apache2.service

### 4. Instale o OpenSSL

sudo apt install openssl -y, p

### 5. Habilite os módulos do Apache

sudo a2enmod ssl
sudo a2enmod rewrite

### 6. Edite o arquivo de configuração do Apache

sudo nano /etc/apache2/apache2.conf

>Adicione no final:

<Directory /var/www/html>
    AllowOverride ALL
</Directory>

### 7. Gere o certificado SSL

sudo mkdir /etc/apache2/certs
cd /etc/apache2/certs
sudo openssl req -new -newkey rsa:4096 -x509 -sha256 -days 365 -nodes -out apache.cert -keyout apache.key

### 8. Configure o redirecionamento HTTPS

sudo nano /etc/apache2/sites-enabled/000-default.conf

> Adicione ou substitua o conteúdo por:

<VirtualHost *:80>
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule (.*) https://%{HTTP_HOST}%{REQUEST_URI}
</VirtualHost>

<VirtualHost *:443>
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/html
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
    SSLEngine on
    SSLCertificateFile /etc/apache2/certs/apache.cert
    SSLCertificateKeyFile /etc/apache2/certs/apache.key
</VirtualHost>

>Reinicie o Apache:

sudo systemctl restart apache2.service

### 9.  Adicione os arquivos do projeto
>Copie os arquivos do painel para:

/var/www/html/

>Agora, acesse o sistema usando o IP da sua máquina local via navegador.

