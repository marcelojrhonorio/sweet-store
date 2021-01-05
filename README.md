# Sweet Bonus Store

Portal de acúmulo de pontos e trocas por produtos e serviços.

## Instalar em servidor local

Siga os seguintes passos para rodar o projeto em seu ambiente local de desenvolvimento.

#### 1. Clone o projeto para seu repositório local

#### 2. Faça o setup do arquivo .env

Copie o arquivo *.env.example*, renomeando para *.env*. Além das padrões, as propriedades abaixo deverão estar presentes: 

`APP_SWEET_API=` Receberá o endereço da API do projeto.

`API_CALOGA=`API para disparo de leads.

Para disparo de e-mail transacional, as seguintes propriedades devem ser informadas: 
`INBOXCENTER_DOMAIN=`, `INBOXCENTER_EMAIL=`, `INBOXCENTER_API=` e `INBOXCENTER_TOKEN=`.


#### 3. Atualize seu projeto
Digite o comando `composer update` dentro da pasta.

#### 4. Adicione o projeto ao Homestead
Para que o projeto rode no Homestead, é necessário incluir as informações no arquivo *Homestead.yaml*.  

Exemplo de configuração para folders:

    folders: 
       - map: C:/www/sweetbonus
         to: /home/vagrant/Sites/sweetbonus.test

Exemplo de configuração para sites:

    sites: 
       - map: sweetbonus.test
         to: /home/vagrant/Sites/sweetmedia.test/public   

Lembre-se também de configurar o arquivo de *hosts* do seu sistema operacional. Veja o exemplo abaixo: 

    192.168.10.10	sweetbonus.test

