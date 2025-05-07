# PROCESO PARA OBTENER EL TOKEN, API KEY DE TRELLO Y LA ID EN CARPETAS DE GOOGLE DRIVE.

Guia destinada a la extracción del **TOKEN** y **KEY** de **TRELLO** para crear carpetas en **GOOGLE DRIVE** y vincularlas a trello con sus respectivas tarjetas y checklist con **PHP 8** en **VISUAL STUDIO CODE**.

## 1. OBTENER API DE TRELLO

En la pagina dueña de trello, **ATLASSIAN DEVELOPER** llena de documentacion al respecto se encuentra el apartado de **REST API CLIENT** (El link:  *[REST API](https://trello.com/power-ups/admin)*.)
Donde se facilita la obtencion de la misma por medio de trello desde un **POWER-UP** asi llamado la aplicacion para adquirir esta integracion.


1. Si no tienes una cuenta creada de Trello al entrar al link te pedira una cuenta y un espacio de trabajo y un tablero.

2. Luego en el apartado de power-ups e integracion hacer click en **NUEVO** y pedira llenar un formulario sencillo donde deberas agregar el espacio de trabajo en trello anteriormente creado de la cuenta registrada:

![FORMULARIO PARA LA API KEY](/Trellomd/img/formulario1.png)

3. En la casilla de "URL del conector de iframe es opcional".

![FORMULARIO PARA LA API KEY](/Trellomd/img/formulario2.png)

4. Al completar el formulario mostrara un boton, al darle click generara la nueva **API KEY**.

![FORMULARIO PARA LA API KEY](/Trellomd/img/apiapi.png)

## 2. OBTENCION DEL TOKEN DE TRELLO

1. Despues en el siguiente link se debera reemplazar la parte que muestra **TU_API_KEY** con la api anterirormente obtenida para autorizar el uso del token: *[TOKEN DE TRELLO](https://trello.com/1/authorize?key=TU_API_KEY&scope=read,write&name=MiApp&expiration=never&response_type=token)*

![AUTORIZACION DE TRELLO](/Trellomd/img/autorizaciontrello.png)

2. Al bajar y darle permitir se generara el **TOKEN DE TRELLO**.

![AUTORIZACION DE TRELLO](/Trellomd/img/trellotoken.png)

> ⚠️ [!NOTA]
> - Si estás trabajando con cuentas de Google diferentes, asegúrate de que el token generado corresponde al mismo usuario que tiene acceso de edición a la carpeta de destino.
> - No expongas tu API Key ni tu Token de Trello en archivos públicos ni repositorios.

## 3. VERIFICA TUS DATOS.

Una vez obtenido tanto la **API KEY** y el **TOKEN**.
1. se puede comprobar que funcionan con una llamada como en este link: *[VERIFICACION DE DATOS](https://api.trello.com/1/members/me?key=TU_API_KEY&token=TU_TOKEN)*
2. Debes reempleazar donde pone **TU_API_KEY** y **TU_TOKEN** en la url.
3. Esto debería devolver tus datos de usuario en formato json.

## 4. HABILITAR LA API DE GOOGLE DRIVE.
En la pagina de *[GOOGLE CLOUD](https://console.cloud.google.com/)*. 
1. Selecciona el buscador y escribe "**APIS Y SERVICIOS**" 
2. Selecciona **BIBLIOTECA**, en el buscador escribe "**GOOGLE DRIVE API**"
3. Selecciona la primera opcion y oprime en **HABILITAR**.

## 5. DESCARGAR CREDENCIALES DE GOOGLE CLOUD.

las credenciales permiten que Google sepa quién está haciendo la solicitud y confíe en que es tu aplicación. Son esenciales para la utorización de acceso a **APIs de Google.**

En la pagina de *[GOOGLE CLOUD](https://console.cloud.google.com/)*. 
1. Selecciona el buscador y escribe "**APIs Y SERVICIOS**" y en la seccion de **CREDENCIALES.** 

2. click en **CREAR CREDENCIALES** y luego en **ID DE CLIENTE OAUTH**.

![DESCARGAR CREDENCIALES](/Trellomd/img/credenciales.png)

3. Luego click en **CONFIGURAR PANTALLA DE CONSENTIMIENTO** primero.

![DESCARGAR CREDENCIALES](/Trellomd/img/cred.png)

4. y se debera completar la configuracion de la app, pasos simples.

![DESCARGAR CREDENCIALES](/Trellomd/img/oauthh.png)

5. Seleccionas **APLICACION WEB** y escoger el nombre del cliente oauth 2.0 que solo se usa para identificar al cliente en consola, no se mostrará a los usuarios finales.

![DESCARGAR CREDENCIALES](/Trellomd/img/oauthhhhh.png)

6. se debera agregar una URls de redireccionamiento 

- Estos URI previene ataques de suplantación y garantiza que el flujo de autenticación OAuth 2.0 se complete de manera segura y confiable, devolviendo a los usuarios a la página correcta dentro de tu aplicación después de iniciar sesión.

7. Click en creara

![DESCARGAR CREDENCIALES](/Trellomd/img/credencial.png)

8. luego descargamos el JSON que se muestra en el mensaje emerjente y ya tendriamos las credenciales descargada.

9. Cambiarle el nombre del archivo a **credentials.json**

10. Despues de ya realizado la descargar de la credencial se necesita abrir la consola en donde este el codigo e instala las dependencias necesarias en php con composer:
```
composer require google/apiclient guzzlehttp/guzzle
```
- Composeres un administrador de dependencias para PHP. Su función es descargar e instalar las bibliotecas de software necesarias para que la aplicación PHP pueda interactuar fácilmente con las APIs de Google.

> ⚠️ [!NOTE]
> A segurarse de tener el archivo **credentials.json** (descargado desde google cloud) este en la misma carpeta donde se ejecuta el script.

## 6. SACAR LA **ID** DE LA CARPETA DE **GOOGLE DRIVE**

Se necesita la id de la carpeta principal de goolge drive donde se va a modificar su contenido agregando mas carpetas con distintos usos y para una correcta organizacion de las mimas atraves del codigo.

- Abrir la carpeta que deseas modificar su contenido y copia el **ID** de la **URL** Ejemplo:

https://drive.google.com/drive/folders/1UDlVUi379hNN-nd0AoQK2WL4kiHdntm_.

El **ID** es: 1UDlVUi379hNN-nd0AoQK2WL4kiHdntm_.