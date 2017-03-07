<?


if(isset($_FILES["master"]) && strlen($_FILES["master"]["tmp_name"]) > 0): 

  $master = [];

  include 'simplexlsx.class.php';
  $xlsx = new SimpleXLSX($_FILES["master"]["tmp_name"]);

  foreach( $xlsx->rows() as $k => $r ):
    
    if(count($r) < 2) continue;
    if(strlen($r[0]) <= 3) continue;

    $sku = trim(strtoupper($r[0]));
    $cat = trim(strtolower($r[1]));

    if(array_key_exists($sku, $master)):
      if(!in_array($cat, $master[$sku])) :
        array_push($master[$sku], $cat);
      endif;
    else:
      $master[$sku] = array($cat);
    endif;

  endforeach;

  foreach( $master as $k => $v ):


    for($i=0;$i<count($v);$i++):
      for($j=$i;$j<count($v);$j++):
        if($v[$i] > $v[$j]) {
          $tmp = $v[$i];
          $v[$i] = $v[$j];
          $v[$j] = $tmp;
        }
      endfor;
    endfor;

    $master[$k] = $v;
    
  endforeach;

  include_once "xlsxwriter.class.php";

  $filename = "output.xlsx";
  header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
  header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
  header('Content-Transfer-Encoding: binary');
  header('Cache-Control: must-revalidate');
  header('Pragma: public');

  $header = array(
    'SKU'=>'string',
    'CAT'=>'string',
  );

  $data = [];
  foreach($master as $k => $v):
    array_push($data, array( $k, join(",", $v)));
  endforeach;

  $writer = new XLSXWriter();
  $writer->setAuthor('LZD');
  $writer->writeSheet($data,'OUTPUT',$header);
  $writer->writeToStdOut();
  exit(0);



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
    <label for="inpMaster">Target File</label><input id="inpMaster" name="master" type="file">
  </div>
  <input type="submit" value="Start">
</form>

<div id="dvSample">
  Sample file <a href="download/sample-cat.xlsx" download>sample-cat.xlsx</a>
</div>


  </body>
</html>