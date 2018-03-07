<?php 
global $arResult,$delete;
if (isset($_FILES['file']) && !empty($_FILES['file']))
{
    $name = $_FILES["file"]["name"];
    $temp = explode(".", $_FILES["file"]["name"]);
    if(end($temp) == "csv")
    {
        $uploads_dir = dirname(__FILE__) .'/files/'.$_FILES['file']['name'];
        move_uploaded_file($_FILES["file"]["tmp_name"], $uploads_dir);
        $data = array(); 
        $fp = fopen($uploads_dir, "r"); 
        while (!feof($fp)) { 
             $data[] = fgetcsv($fp, 1024, ","); 
        } 
        fclose($fp);
        unlink($uploads_dir);
    } 
    else
    {?>
      <div style="color:red" ><?echo "Error,not Documentum type...";?></div>
    <?}
}
$delete = array();
$CMain = new CMain(DB_HOST, DB_USER, DB_NAME, DB_PASS);
$query = 'SELECT * FROM `'.DB_TABLE.'` WHERE 1';
$SELECT = $CMain->Query( $query);
if ($SELECT)
{
  foreach ($SELECT as &$value)
  {	
    $value['NAME'] = $value[1];
    $value['QUANTITY'] = $value[2]+$value[3];
    $value['WHAREHOUSES'] = Warehouses($value[2],$value[3]);
    if ($data)
    { 

        foreach ($data as $k=>$item) 
        { 
            if ($value['NAME'] == $item[0])
            {    
                if ($item[2] === "WH1")
                { 
                  $value[2] = $value[2] + $item[1];
                  $arUpdate = $CMain->UpdateRecord(DB_TABLE,array("QUANTITY_WH1"=>$value[2] ),$value[0]);
                }
                elseif ($item[2] === "WH2")
                { 
                  $value[3] = $value[3] + $item[1];
                  $arUpdate = $CMain->UpdateRecord(DB_TABLE,array("QUANTITY_WH2"=>$value[3] ),$value[0]);
                }
                $value['QUANTITY'] = $value[2] + $value[3];
                $value['WHAREHOUSES'] = Warehouses($value[2],$value[3]);
            }
            else
            {   
                if ( ($item[2] === "WH1") && ($item[1]>0) )
                {
                  $arAdd = $CMain->AddNewRecord(DB_TABLE,array("PRODUCT_NAME"=>$item[0],"QUANTITY_WH1"=>$item[1]));
                }
                elseif ( ($item[2] === "WH2") && ($item[1]>0) )
                {
                  $arAdd = $CMain->AddNewRecord(DB_TABLE,array("PRODUCT_NAME"=>$item[0],"QUANTITY_WH2"=>$item[1]));
                } 
             
            }
        }
    }
    if ($value['QUANTITY']<=0)
    {
        $delete[] = $value[0];
    }                
  }
    if ($delete)
    { 
      $arDelete = $CMain->DeleteRecord(DB_TABLE,$delete);
    }
}
else
{
    if ($data)
    {   
        foreach ($data as $item)
        { 
          $productname[] =  $item[0];
        }
        $productname = array_unique($productname);
        foreach ($productname as $key => $name) {
          foreach ($data as $item) 
          { 
            if ($name == $item[0])
            { 
              $new_product[$key]['NAME'] = $item[0];
              if ( ($item[2] == "WH1") && ($item[1]>0) )
              {
                $new_product[$key]['WH1'] = $item[1];
              }
              elseif ( ($item[2] == "WH2") && ($item[1]>0) )
              {
                $new_product[$key]['WH2'] = $item[1];
              }
            }
          }
          $new_product[$key]['QUANTITY'] = $new_product[$key]['WH1'] + $new_product[$key]['WH2'];
          $new_product[$key]['WHAREHOUSES'] = Warehouses($new_product[$key]['WH1'],$new_product[$key]['WH2']);
          if ($new_product[$key]['QUANTITY']>0)
          {
              $arAdd = $CMain->AddNewRecord(
                DB_TABLE,
                array(
                "PRODUCT_NAME"=>$new_product[$key]['NAME'],
                "QUANTITY_WH1"=>$new_product[$key]['WH1'],
                "QUANTITY_WH2"=>$new_product[$key]['WH2'])
              );
          }
          else
          {
            unset($new_product[$key]);
          }
          
        }
    } 
}
if ( ($new_product) && ($SELECT) )
{
  $arResult = array_merge ($SELECT, $new_product);
}
elseif($SELECT)
{
  $arResult = $SELECT;
}
elseif($new_product)
{
  $arResult = $new_product;
}



