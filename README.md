# PROCESO PARA OBTENER EL TOKEN, API KEY DE TRELLO Y LAS CREDENCIALES DE GOOGLE DRIVE.

Guia destinada a la extracción del **TOKEN** y **KEY** de **TRELLO** para crear carpetas en **GOOGLE DRIVE** y vincularlas a trello con sus respectivas tarjetas y checklist con **PHP 8** en **VISUAL STUDIO CODE**.

## VENDOR

Despues de haber clonado el proyecto, se debera primero que todo crear la carpeta **VENDOR** la cual es en donde se almacenan todas las librerias y dependencias que son necesarios instalar para el proyecto.

1. Digitamos el siguiente comando en nuestra consola: **Composer install**

![VENDOR](/trellomd/img/composer.png)

2. Despues de un rato ya tendremos la carpeta vendor en nuestro proyecto.

![VENDOR](/trellomd/img/capetavendor.png)

## TRELLO ATLASSIAN DEVELOPER

En **TRELLO ATLASSIAN DEVELOPER** Trello guarda su documentacion, guias y referencias para el uso de la plataforma mediante codigo y de esta manera facilitar la automatizacion en la organizacion de las actividades que se desean archivar.

Hacemos click en *[ATLASSIAN DEVELOPER](https://developer.atlassian.com/cloud/trello/)* la cual es su pagina oficial donde se encuentran guias completas.

1. Click en **Reference** 

![DESCARGAR CREDENCIALES](/trellomd/img/atlassiandeveloper.png)

2. Nos muestra todo las formas de uso para crear tableros y tarjetas en trello.

![DESCARGAR CREDENCIALES](/trellomd/img/reference.png)


### 1. DESCARGAR CREDENCIALES DE GOOGLE CLOUD.

Las credenciales permiten que Google sepa quién está haciendo la solicitud y confíe en que es tu aplicación. Son esenciales para la autorización de acceso a **APIs de Google.**

En la pagina de *[GOOGLE CLOUD](https://console.cloud.google.com/)*. 
1. Selecciona el buscador y escribe "**APIs Y SERVICIOS**" y en la seccion de **CREDENCIALES.** 

2. Click en **CREAR CREDENCIALES** y luego en **ID DE CLIENTE OAUTH**.

![DESCARGAR CREDENCIALES](/trellomd/img/credenciales.png)

3. Luego click en **CONFIGURAR PANTALLA DE CONSENTIMIENTO** primero.

4. Despues se debera completar la configuracion de la app, pasos simples.

![DESCARGAR CREDENCIALES](/trellomd/img/cred.png)

5. Luego seleccionas **APLICACION WEB** y escoger el nombre del cliente oauth 2.0 que solo se usa para identificar al cliente en consola, no se mostrará a los usuarios finales.

![DESCARGAR CREDENCIALES](/trellomd/img/oauthh.png)

6. Se debera agregar una URls de redireccionamiento 

- Estos URI previene ataques de suplantación y garantiza que el flujo de autenticación OAuth 2.0 se complete de manera segura y confiable, devolviendo a los usuarios a la página correcta dentro de tu aplicación después de iniciar sesión.

7. Click en **crear**

![DESCARGAR CREDENCIALES](/trellomd/img/oauthhhhh.png)

8. Luego descargamos el JSON que se muestra en el mensaje emerjente y ya tendriamos las credenciales descargada.

![DESCARGAR CREDENCIALES](/trellomd/img/credencial.png)

9. Cambiarle el nombre del archivo a **credentials.json** y guardarlo en la carpeta dataConfig.

10. Despues de ya realizado la descargar de las credenciales se necesita abrir la consola en donde este el codigo e instala las dependencias necesarias en php con composer:

```
composer require google/apiclient guzzlehttp/guzzle
```

> ⚠️ [!NOTA]
> Composer es un administrador de dependencias para PHP. Su función es descargar e instalar las bibliotecas de software necesarias para que la aplicación PHP pueda interactuar fácilmente con las APIs de Google.

### 2. HABILITAR LA API DE GOOGLE DRIVE.
En la pagina de *[GOOGLE CLOUD](https://console.cloud.google.com/)*. 
1. Selecciona el buscador y escribe "**APIS Y SERVICIOS**" 
2. Selecciona **BIBLIOTECA**, en el buscador escribe "**GOOGLE DRIVE API**"
3. Selecciona la primera opcion y oprime en **HABILITAR**.

### 2.1. PUBLICAR APLICACION EN GOOGLE CLOUD

Para poder tener permisos en la cuenta desde la app creada en google cloud.

1. En tu proyecto de *[GOOGLE CLOUD](https://console.cloud.google.com/)* al entrar tu **aplicacion web** dirigete en las opciones de la izquierda en el apartado de **PÚBLICO**.

2. Click en publicar la aplicación y añadir un usuario de prueba.

3. Y por ultimo agrega tu mismo correo de **Google Drive**.

Esto ayuda para tener acceso cuando el codigo de la terminal te pida la autorización.

### 3. OBTENER API DE TRELLO

En la pagina dueña de trello, **ATLASSIAN DEVELOPER** llena de documentacion al respecto se encuentra el apartado de **REST API CLIENT** (El link:  *[REST API](https://trello.com/power-ups/admin)*) Donde se facilita la obtencion de la api de trello.

1. Si no tienes una cuenta creada de Trello al entrar al link te pedira una cuenta y un espacio de trabajo y un tablero.

2. Luego en el apartado de power-ups e integracion hacer click en **NUEVO** y pedira llenar un formulario sencillo donde deberas agregar el espacio de trabajo en trello anteriormente creado de la cuenta registrada:

![FORMULARIO PARA LA API KEY](/trellomd/img/formulario1.png)

3. En la casilla de "URL del conector de iframe" solo es necesario cuando se vaya a desarrollar un Power-Up, pero en este caso vamos a realizar una integracion.

![FORMULARIO PARA LA API KEY](/trellomd/img/formulario2.png)

4. Al completar el formulario mostrara un boton, al darle click generara la nueva **API KEY**.

![FORMULARIO PARA LA API KEY](/trellomd/img/apii.png)

### 4. OBTENCION DEL TOKEN DE TRELLO

1. Ingresamos la **api key** o mejor dicho **trellokey** al codigo:

![AUTORIZACION DE TRELLO](/trellomd/img/trellokeyterr.png)

- Esto para permitir que el codigo lo rredireccione automatico a la url para sacar el token de trello por medio de la terminal

![AUTORIZACION DE TRELLO](/trellomd/img/terminaltoken.png)

2. Al bajar y darle permitir se generara el **TOKEN DE TRELLO**.

![AUTORIZACION DE TRELLO](/trellomd/img/autorizaciontrello.png)



![AUTORIZACION DE TRELLO](/trellomd/img/TOKEN2.png)

> ⚠️ [!NOTA]
> - Si estás trabajando con cuentas de Google diferentes, asegúrate de que el token generado corresponde al mismo usuario que tiene acceso de edición a la carpeta de destino.
> - No expongas tu API Key ni tu Token de Trello en archivos públicos ni repositorios.

### 4.1 CODIGO DE VERIFICACION

Con los pasos hechos de la **parte 2.1** anteriormente mencionados daria los permisos necesarios en la aplicacion de google cloud para lo siguiente:

1. Para acceder al siguiente link, colocamos el token de trello que sacamos en el paso anterior de la **parte 4**.

![AUTORIZACION DE TRELLO](/trellomd/img/digitartokentrelloter.png)

2. Que nos llevara a verificar nuestra cuenta google (Tener en cuenta que perfil de usuario estas usando actualmente en google)

![AUTORIZACION DE TRELLO](/trellomd/img/verificarcuentagoogle.png)

3. Continuamos con en el siguiente paso.

![AUTORIZACION DE TRELLO](/trellomd/img/continuearverificacion.png)

4. Confirmamos que confiamos en la plataforma **csmeducativo** y continuamos.

![AUTORIZACION DE TRELLO](/trellomd/img/confieconfie.png)

5. Luego nos redireccionara a la pagina oficial de **csmeducativo** y en la url estara el codigo que tenemos que confirmar en la terminal.

6. Deberemos de copiar despues de donde dice "**code=**" en la **URL**.

7. Y luego pergarlo en la terminal.

![AUTORIZACION DE TRELLO](/trellomd/img/csmeducativo.png)

8. Se realizaron los pasos con exito cuando se confirme que se generaron las carpetas en google drive y su respectico adjunto en trello.

![AUTORIZACION DE TRELLO](/trellomd/img/codigoterminal.png)

### 5. ACTUALIZAR TOKEN DE TRELLO Y EL TOKEN DE DRIVE

Al cambiar de cuenta se tiene que tener en cuenta que se deben actualizar los datos de la api de trello en el codigo para de esa manera se pueda generar el token correspondiente a su cuenta y todo se guarda en archivos desde la carpeta **dataConfig**.

![DESCARGAR CREDENCIALES](/trellomd/img/dataconfig.png)

Las credenciales se deben descargar y poner en la carpeta dataConfig como anteriormente se menciono en la **parte 1**.

### 6. VARIABLES DE LA API Y EL TOKEN DE TRELLO EN EL CODIGO

El codigo guardara la api y el token de trello de la siguiente manera:

![AUTORIZACION DE TRELLO](/trellomd/img/apiytoken.png)



- **$trelloToken**: Almacena el token de autenticación que devuelve el método **KeyToken()**
- **$trelloKey**: Almacena la clave pública de la API.

### 7. VERIFICA TUS DATOS.

Una vez obtenido tanto la **API KEY** y el **TOKEN**, verifica si estan correctamente copiados.
1. Se puede comprobar que funcionan con una llamada como en este link: *[VERIFICACION DE DATOS](https://api.trello.com/1/members/me?key=TU_API_KEY&token=TU_TOKEN)*
2. Debes reempleazar donde pone **TU_API_KEY** en la url con la Key.

![AUTORIZACION DE TRELLO](/trellomd/img/verifiapi.png)

3. Y de igual manera donde pone **TU_TOKEN** debes cambiar esa parte de la url con el token anterior mente obtenido de trelo.

![AUTORIZACION DE TRELLO](/trellomd/img/verifitoken.png)


4. Esto debería devolver tus datos de usuario en formato json.

### 8. OBTENER LA ID DEL TABLERO EN TRELLO

Necesario la id del tablero para permitir hacerle cambios al mismo desde el codigo, se podra configurar tarjetas y adjuntar enlaces de carpetas de google drive.

1. Por medio del terminal te mostrara un enlace a tu espacio de trabajo en trello y te pedira la id del trablero.

![DESCARGAR CREDENCIALES](/trellomd/img/boardboard.png)

2. Selecciona el tablero con el que deseas operar.

![DESCARGAR CREDENCIALES](/trellomd/img/tablerotrello.png)

3. Buscamos arriba en la url la **ID** del tablero

![DESCARGAR CREDENCIALES](/trellomd/img/idboard.png)

4. Copiamos y pegamos en la terminal.

![DESCARGAR CREDENCIALES](/trellomd/img/boardid.png)

### 9. SACAR LA **ID** DE LA CARPETA DE **GOOGLE DRIVE**

Se necesita la id de la carpeta principal de goolge drive donde se va a modificar su contenido agregando mas carpetas con distintos usos y para una correcta organizacion de las mimas atraves del codigo.

1. Al iniciar el codigo te pedira la id de tu carpeta en google drive.

2. En la terminal se mostrara el link de la pagina principal de tu **GOOGLE DRIVE** donde escogeras la carpeta que deseas.

![DESCARGAR CREDENCIALES](/trellomd/img/googleaidi.png)

3. Seleccionas la carpeta y extraes la **ID** de la carpeta para ponerla en la terminal.

![DESCARGAR CREDENCIALES](/trellomd/img/aidigoogle.png)

