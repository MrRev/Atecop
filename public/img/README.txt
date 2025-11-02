Carpeta pública para imágenes del proyecto ATECOP

Dónde colocar imágenes:
- Guarda aquí los archivos de imagen usados por la aplicación (logos, iconos, etc.).

Nombre del archivo requerido por la cabecera (logo):
- logo-atecop.png

Recomendaciones para `logo-atecop.png`:
- Formato: PNG (puedes usar PNG o adaptar el código si prefieres SVG/JPG).
- Tamaño recomendado: 240 x 80 px (proporción horizontal). Se adapta con CSS; 120 x 40 también funciona.
- Resolución: 72-150 dpi está bien para web.
- Fondo: si quieres que el logo se integre bien con la barra, usa fondo transparente.

Si quieres ver un placeholder rápido, puedes crear un PNG de 1x1 píxel transparente con el siguiente contenido base64 y guardarlo como `logo-atecop.png` (decodificar con cualquier herramienta o script):

iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8Xw8AAn8B9yqf6wAAAABJRU5ErkJggg==

Cómo agregar la imagen (ejemplo con PowerShell):
1) Abrir PowerShell en la carpeta del proyecto.
2) Ejecutar (ejemplo para decodificar el base64 y crear un PNG placeholder):

$base64 = "iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8Xw8AAn8B9yqf6wAAAABJRU5ErkJggg=="
[System.Convert]::FromBase64String($base64) | Set-Content -Encoding Byte -Path .\\public\\img\\logo-atecop.png

Después de añadir `logo-atecop.png`, recarga la aplicación y el logo aparecerá en la cabecera si la ruta `IMG_URL` apunta a `http://localhost/Atecop/public/img`.
