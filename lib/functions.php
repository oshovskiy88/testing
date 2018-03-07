 <?php 
function Warehouses($arg_1, $arg_2)
{
    $Warehouses = "";
    if ($arg_1>0)
    {
      $Warehouses = $Warehouses."WH1,";
    }
    if($arg_2>0)
    {
      $Warehouses = $Warehouses."WH2";
    }
    if (substr($Warehouses, -1) !== ",")
    {
       $retval = $Warehouses;
    }
    else
    {
      $retval = substr($Warehouses, 0, -1);
    } 
    return $retval;
}