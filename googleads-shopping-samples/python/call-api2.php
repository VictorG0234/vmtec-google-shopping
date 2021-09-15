<?php

$servername = "localhost";
$username = "vmtec";
$password = "N4nodokipesedo#1";

$connTVYN = new mysqli($servername, $username, $password, "tienda");
$_SERVER['SERVER_NAME'] = "vmtec.com.mx";
$_SERVER['HTTP_HOST'] = "vmtec.com.mx";

$sql = "SELECT ID, product_id, post_title AS name_product, post_name, post_excerpt AS product_description, 
guid AS product_url, post_date AS product_date, min_price AS price_product, onsale AS status , 
sku, stock_status AS product_stock, stock_quantity AS product_quantity
FROM wp_posts
INNER JOIN wp_wc_product_meta_lookup
ON ID = product_id
WHERE post_type = 'product'"; 

$stmt = $connTVYN->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$rows = $result->fetch_all(MYSQLI_ASSOC);

$json_encode = [];

foreach ($rows as $data) {
  //Estructura de lo que trae la query

  // array (size=12)
  //   'ID' => int 11
  //   'product_id' => int 11
  //   'name_product' => string 'Barra tensora' (length=13)
  //   'post_name' => string 'barra-tensora' (length=13)
  //   'product_description' => string '' (length=0)
  //   'product_url' => string 'https://vmtec.com.mx/tienda/?post_type=product&#038;p=11' (length=56)
  //   'product_date' => string '2021-08-31 23:22:51' (length=19)
  //   'price_product' => string '1.0000' (length=6)
  //   'status' => int 0
  //   'sku' => string '01020344506' (length=11)
  //   'product_stock' => string 'instock' (length=7)
  //   'product_quantity' => null
  
  $json = array(
    'offerID' => $data['ID'],
    'title' => $data['name_product'],
    'description' => $data['product_description'],
    'link' => "https://vmtec.com.mx/tienda/index.php/producto/" . $data['post_name'],
    'imageLink' => "https://vmtec.com.mx/tienda/wp-content/uploads/2021/08/download-1.png",
    'contentLanguage' => 'es',
    'targetCountry' => 'MX',
    'channel' => 'online',
    'availability' => $data['product_stock'],
    'condition' => 'new',
    'googleProductCategory' => 'autos',
    'gtin' => $data['sku'],
    'price' => array(
      'value' => $data['price_product'],
      'currency' => 'MXN'),
    'shipping' => array(
      'country' => 'MX',
      'service' => 'Standard shipping',
      'price' => array(
          'value' => '250.00',
          'currency' => 'MXN'
        )
      ),
    'shippingWeight' => array(
      'value' => '20',
      'unit' => 'grams'
    )
  );
  
  $json_encode[] = $json;

}

$j_encode = base64_encode(json_encode($json_encode));

$archivo = fopen("shopping/content/products/sample.py", "w");

$sample = "from shopping.content import _constants" . "\n";
$sample .= "def create_product_sample(config, offer_id, **overwrites):". "\n";
$sample .= "\tproduct = ". $j_encode . "\n";
$sample .= "\tproduct.update(overwrites)". "\n";
$sample .= "\treturn product". "\n";
fwrite($archivo, $sample);
fclose($archivo);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css" crossorigin="anonymous">
  <link rel="stylesheet" href="../../bootstrap/css/bootstrap-theme.min.css" crossorigin="anonymous">
  <script src="../../bootstrap/js/bootstrap.min.js" crossorigin="anonymous"></script>
  <title>Registro productos API</title>
</head>

<body>
  <div class="container">
    <div class="row">

      <div class="col">
        <h2>Envio de producto mediante API a Google Merchant.</h2>
        
        <h2>Los datos de tu ultimo producto son los siquientes:</h2>

        <div>
          <?php foreach ($rows as $data){?>
            <div class="mb-3">
              <label for="exampleInput1" class="form-label">Nombre del Producto</label>
              <input type="text" class="form-control" id="exampleInput1" value="<?php echo $data['name_product'];?>">
            </div>
            <div class="mb-3">
              <label for="exampleInput2" class="form-label">Descripcion del Producto</label>
              <input type="text" class="form-control" id="exampleInput2" value="<?php echo $data['product_description']; ?>">
            </div>
            <div class="mb-3">
              <label for="exampleInput3" class="form-label">Link del Producto</label>
              <input type="text" class="form-control" id="exampleInput3" value="<?php echo "https://vmtec.com.mx/tienda/index.php/producto/" . $data['post_name']; ?>">
            </div>
            <div class="mb-3">
              <label for="exampleInput4" class="form-label">Fecha de Registro</label>
              <input type="text" class="form-control" id="exampleInput4" value="<?php echo $data['product_date']; ?>">
            </div>
            <div class="mb-3">
              <label for="exampleInput5" class="form-label">Precio del Producto</label>
              <input type="text" class="form-control" id="exampleInput5"  value="<?php echo $data['price_product']; ?>">
            </div>
            <div class="mb-3">
              <label for="exampleInput6" class="form-label">SKU</label>
              <input type="text" class="form-control" id="exampleInput6" value="<?php echo $data['sku']; ?>">
            </div>
            <div class="mb-1">
              <label for="exampleInput7" class="form-label">Estatus</label>
              <input type="text" class="form-control" id="exampleInput7" value="<?php echo $data['product_stock']; ?>">
            </div>
            <hr>
          <?php }?>
        </div>
      
        <div class="">
          <button type="button" class="btn btn-primary" onclick="funcion();" value="Enviar Producto">Enviar
            Producto</button>
        </div>
      </div>
      

    </div>
  </div>


</body>

</html>
<script>
  function funcion() {
    <?php echo shell_exec("python3 -m shopping.content.products.insert"); ?>
  }
</script>