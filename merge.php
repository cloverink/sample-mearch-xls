<?


if(isset($_FILES["master"]) && strlen($_FILES["master"]["tmp_name"]) > 0): 

  $master = [];
  $append = [];

  include 'simplexlsx.class.php';
  $xlsx = new SimpleXLSX($_FILES["master"]["tmp_name"]);

  foreach( $xlsx->rows() as $k => $r ):
    
    if(count($r) < 2) continue;
    if(strlen($r[0]) < 5 || strlen($r[1]) < 5) continue;

    $sku = explode("-",trim(strtoupper($r[0])))[0];
    $url = trim(strtolower($r[1]));

    if(array_key_exists($url, $master)):
      if(!in_array($sku, $master[$url])) :
        array_push($master[$url], $sku);
      endif;
    else:
      $master[$url] = array($sku);
    endif;

  endforeach;

  if(isset($_FILES["append"]) && strlen($_FILES["append"]["tmp_name"]) > 0): 

    $xlsx2 = new SimpleXLSX($_FILES["append"]["tmp_name"]);

    foreach( $xlsx2->rows() as $k => $r ):
      
      if(count($r) < 2) continue;
      if(strlen($r[0]) < 5 || strlen($r[1]) < 5) continue;

      $sku = explode("-",trim(strtoupper($r[0])))[0];
      $url = trim(strtolower($r[1]));

      if(array_key_exists($url, $master)):
        if(!in_array($sku, $master[$url])) :
          array_push($master[$url], $sku);
        endif;
      else:
        $master[$url] = array($sku);
      endif;

    endforeach;

    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename="output.csv";');
    $file = fopen('php://output', 'w');

    foreach($master as $key => $row):
      foreach($row as $sku):
        fputcsv($file,array($sku, $key));
      endforeach;
    endforeach;

    exit();


  else:
    echo "Need Append file";
  endif;

endif;



?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/style.css" rel="stylesheet">
  </head>
  <body>
  

<form method="post" enctype="multipart/form-data">
  <div>
    <label for="inpMaster">Master File</label><input id="inpMaster" name="master" type="file">
  </div>
  <div>
    <label for="inpAppend">Append File</label><input id="inpAppend" name="append" type="file">
  </div>
  <input type="submit" value="Merge">
</form>

<div id="dvSample">
  Sample file <a href="download/Master.xlsx" download>Master.xlsx</a> | <a href="download/Append.xlsx" download>Append.xlsx</a>
</div>


  </body>
</html>