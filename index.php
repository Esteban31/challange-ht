<?php
require 'vendor/autoload.php'; 
require 'db/conexion.php';

$db = conexion();

use PhpOffice\PhpSpreadsheet\IOFactory;


$spreadsheet = IOFactory::load('test-data-import.xlsx');
$sheet = $spreadsheet->getActiveSheet();

// DEFINE THE COLUMNS TO USE
$upcColumn = 'F';
$brandColumn = 'B';
$productNameColumn = 'G';

$lastRow = $sheet->getHighestRow();

for ($row = 1; $row <= $lastRow; $row++) {
      $upcRowValue = $sheet->getCell($upcColumn . $row)->getValue();
      $brandRowValue = $sheet->getCell($brandColumn . $row)->getValue();
      $productRowValue = $sheet->getCell($productNameColumn . $row)->getValue();

      // Check if product is already registed
      $findProduct = "SELECT id FROM products_test WHERE name = ?";
      $result= $db->prepare($findProduct);
      $result->execute([$productRowValue]); 
      $result = $result->fetch();

      if (@$result["id"]!='') {
            // echo "The product ".$productRowValue." has already been created";
      }else{
            //We save the product on database with the 3 fields
            $sql = "INSERT INTO products_test (upc, brand, name) VALUES (?,?,?)";
            $stmt= $db->prepare($sql);
            $stmt->execute([$upcRowValue, $brandRowValue, $productRowValue]);
      }

      
}



for ($row = 2; $row <= $lastRow; $row++) {
      $images = array(
            'AF' => $sheet->getCell('AF' . $row)->getValue(),
            'AG' => $sheet->getCell('AG' . $row)->getValue(),
            'AH' => $sheet->getCell('AH' . $row)->getValue(),
            'AI' => $sheet->getCell('AI' . $row)->getValue(),
            'AJ' => $sheet->getCell('AJ' . $row)->getValue(),
            'AK' => $sheet->getCell('AK' . $row)->getValue()
      );

      $productRowValue = $sheet->getCell('D' . $row)->getValue();

      foreach ($images as $column => $imageURL) {
            if (!empty($imageURL)) {
                  $imageContent = file_get_contents($imageURL);

                  if ($imageContent !== false) {
                        $directoryPath = "storage/" . $productRowValue . "/";
                        if (!file_exists($directoryPath)) {
                              mkdir($directoryPath, 0777, true);
                        }

                        $imageFileName = basename($imageURL);
                        $imagePath = $directoryPath . $imageFileName;

                        file_put_contents($imagePath, $imageContent);
                  }
            }
      }
}


?>



<!DOCTYPE html>
<html lang="en">
<head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Read Xlsx</title>
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>
      <div class="container-fluid">
            <h1>Tardó en cargar, cierto?, bueno fue por lo siguiente...</h1>
            <div class="row">
                  <div class="col-md-6">
                        <h2>Registro de Productos</h2>
                        <p>Este archivo al ser ejercutado (cuando carga), va a registrar los productos que están dentro 
                              del archivo test-data-import.xlsx usando 3 campos como ejemplo (ucp, brand, productName)
                              se adjunta una imagen ilustrativa del código
                        </p>
                        <img src="script-1.svg" alt="" style="width:100%">
                  </div>

                  <div class="col-md-6">
                        <h2>Descarcargar Imágenes de AF a la AK</h2>
                        <p>Iteramos dentro de nuestro archivo xslx y obtenemos las imágenes de nuestros productos de la AF A la columa AK
                              a su vez, creamos un directorio con el número de item de cada producto para poder administrarlos mejor
                        </p>
                        <img src="script-2.svg" alt="" style="width:100%">
                  </div>
            </div>
      </div>      
</body>
</html>