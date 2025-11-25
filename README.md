Essa é uma api para ser usada com o 'dog site', um projeto que está em https://github.com/ErickAmaralFreitasDev

Ela é feita em php com o wordpress, os plug-ins usados são JWT Authentication for WP-API e All-in-One WP Migration and Backup e com o software 'Local' do wordpress, ela funciona apenas off-line, e deve ser colocada na pasta 'themes' em wp-content.

////////////////////////////////////////////////////////

This is an API to be used with the 'dog site', a project located at https://github.com/ErickAmaralFreitasDev

It's written in PHP using WordPress, and uses the plugins JWT Authentication for WP-API and All-in-One WP Migration and Backup. It also uses WordPress's 'Local' software, works only offline, and should be placed in the 'themes' folder in wp-content.

////////////////////////////////////////////////////////

Endpoints:

POST - Create User

link - http://dogsite.local/wp-json/api/user?Content-Type=application/json

body - {
        "username": "",
        "email": "",
        "password": ""
        }

////////////////////////////////////////////////////////

Post - Token

link - http://dogsite.local/wp-json/jwt-auth/v1/token

Headers - Content-Type:application/json

Body - {
        "username": "",
        "password": ""
        }

////////////////////////////////////////////////////////

POST - Post Photo

link - http://dogsite.local/wp-json/api/photo

Headers - Content-Type:multipart/form-data
          Authorization: Bearer token

Body (form-data) - nome:text:''
                   peso:text:''
                   idade:text:''
                   img:file:file

////////////////////////////////////////////////////////

DELETE - Del Photo

link - http://dogsite.local/wp-json/api/photo/117

Headers - Authorization: Bearer token

////////////////////////////////////////////////////////

GET - Get Photo

link - http://dogsite.local/wp-json/api/photo/{id}

////////////////////////////////////////////////////////

GET - Get Photos

link - http://dogsite.local/wp-json/api/photo

////////////////////////////////////////////////////////

GET - Get Comments

link - http://dogsite.local/wp-json/api/comment/{id}

////////////////////////////////////////////////////////

POST - Post Comment

link - http://dogsite.local/wp-json/api/comment/{id}

Headers - Content-Type:application/json
          Authorization: Bearer token

Body - {
            "comment": ""
        }

////////////////////////////////////////////////////////

GET - Get Stats

link - http://dogsite.local/wp-json/api/stats

Headers - Authorization: Bearer token

