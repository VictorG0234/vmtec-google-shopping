<?php

// Esto solo es para revisar que efectivamente los datos esten llegando
date_default_timezone_set('America/Mexico_City');     
$DateAndTime2 = date('m-d-Y h:i:s a', time());
$post_log = 'post_log.txt';

$message = $DateAndTime2 . "\n";
$message .= $_POST['id']. "\n";
$message .= $_POST['title']. "\n";
$message .= $_POST['description']. "\n";
$message .= $_POST['link']. "\n";
$message .= $_POST['imageLink']. "\n";
$message .= $_POST['stock']. "\n";
$message .= $_POST['sku']. "\n";
$message .= $_POST['price']. "\n";

if ( file_exists( $post_log ) ){
    $file = fopen( $post_log, 'a' );
    fwrite( $file, $message . "\n" );
}else{
    $file = fopen( $post_log, 'w');
    fwrite( $file, $message."\n" );
}
fclose( $file );

$servername = "localhost";
$username = "vmtec";
$password = "N4nodokipesedo#1";

$connTVYN = new mysqli($servername, $username, $password, "tienda");
$_SERVER['SERVER_NAME'] = "vmtec.com.mx";
$_SERVER['HTTP_HOST'] = "vmtec.com.mx";

// Will NOT affect $mysqli->real_escape_string();
$connTVYN->query("SET NAMES utf8mb4");

// Will NOT affect $mysqli->real_escape_string();
$connTVYN->query("SET CHARACTER SET utf8mb4");

// But, this will affect $mysqli->real_escape_string();
$connTVYN->set_charset('utf8mb4');

// But, this will NOT affect it (UTF-8 vs utf8mb4) -- don't use dashes here
$connTVYN->set_charset('UTF-8');

$sql = "SELECT p1.ID, product_id, p1.post_title AS name_product, p1.post_name, p1.post_excerpt AS product_description, 
p1.guid AS product_url, p1.post_date AS product_date, min_price AS price_product, onsale AS status , 
sku, stock_status AS product_stock, stock_quantity AS product_quantity, p2.guid as imageLink
FROM wp_posts p1
INNER JOIN wp_wc_product_meta_lookup
ON ID = product_id
inner join wp_posts p2 on p2.post_parent = p1.ID and p2.post_type = 'attachment'
WHERE p1.post_type = 'product'
ORDER BY p1.post_modified DESC
LIMIT 1"; 

$stmt = $connTVYN->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$rows = $result->fetch_all(MYSQLI_ASSOC);

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
    'offerId' => $data['product_id'],
    'title' => $data['name_product'],
    'description' => $data['product_description'],
    'link' => "https://vmtec.com.mx/tienda/index.php/producto/" . $data['post_name'],
    'imageLink' => $data['imageLink'],
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
  
  $j_encode = json_encode($json);
  
}

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
          <form action="command.php" method="post">
            <?php foreach ($rows as $data){?>
            <div class="mb-3">
              <label for="exampleInput1" class="form-label">Nombre del Producto</label>
              <input type="text" class="form-control" id="exampleInput1" value="<?php echo $data['name_product'];?>">
            </div>
            <div class="mb-3">
              <label for="exampleInput2" class="form-label">Descripcion del Producto</label>
              <input type="text" class="form-control" id="exampleInput2"
                value="<?php echo $data['product_description']; ?>">
            </div>
            <div class="mb-3">
              <label for="exampleInput3" class="form-label">Link del Producto</label>
              <input type="text" class="form-control" id="exampleInput3"
                value="<?php echo "https://vmtec.com.mx/tienda/index.php/producto/" . $data['post_name']; ?>">
            </div>
            <div class="mb-3">
              <label for="exampleInput4" class="form-label">Fecha de Registro</label>
              <input type="text" class="form-control" id="exampleInput4" value="<?php echo $data['product_date']; ?>">
            </div>
            <div class="mb-3">
              <label for="exampleInput5" class="form-label">Precio del Producto</label>
              <input type="text" class="form-control" id="exampleInput5" value="<?php echo $data['price_product']; ?>">
            </div>
            <div class="mb-3">
              <label for="exampleInput6" class="form-label">SKU</label>
              <input type="text" class="form-control" id="exampleInput6" value="<?php echo $data['sku']; ?>">
            </div>
			<div class="mb-3">
              <label for="exampleInput8" class="form-label">Imagen</label>
              <input type="text" class="form-control" id="exampleInput8" value="<?php echo $data['imageLink']; ?>">
            </div>
            <div class="mb-1">
              <label for="exampleInput7" class="form-label">Estatus</label>
              <input type="text" class="form-control" id="exampleInput7" value="<?php echo $data['product_stock']; ?>">
            </div>
            <hr>
            <?php }?>
            <div class="">
              <input type="submit" value="Enviar Producto" class="btn btn-primary">
            </div>
          </form>
        </div>


        <div>
          <hr>
        </div>
      </div>


    </div>
  </div>


</body>

</html>