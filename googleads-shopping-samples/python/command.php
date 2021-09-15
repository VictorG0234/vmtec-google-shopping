<?php

// $command = "python3 -m shopping.content.products.insert";
echo "<pre>";
echo exec("python3 -m shopping.content.products.insert");
echo "</pre>";

// $command = exec("python3 -m shopping.content.products.insert");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col">
            <h2>
                Estamos agregando su producto.
                Refresque su pantalla de Google Merchant en uno 10 segundos.
                Gracias!
            </h2>
        </div>
    </div>
</div>
</body>
</html>