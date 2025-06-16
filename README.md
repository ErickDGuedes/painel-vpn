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
