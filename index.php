    <?php 
    include_once($_SERVER['DOCUMENT_ROOT'].'/lib/constants.php');
    include_once($_SERVER['DOCUMENT_ROOT'].'/lib/CMain.php');
    include_once($_SERVER['DOCUMENT_ROOT'].'/lib/DB.php');
    include_once($_SERVER['DOCUMENT_ROOT'].'/lib/functions.php');
    include_once($_SERVER['DOCUMENT_ROOT'].'/work.php');
    ?>
    <!doctype html>
    <html lang="ru">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Вывод продуктов</title>
        <!-- Bootstrap -->
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link href="css/develop.css" rel="stylesheet">
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="//oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="//oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>

    <body>
      <div class="main">
        <div class="mbox">
        <br>
        <p>
          <h2>Таблица с продуктами</h2> 
        </p>
        <form action="" method="post" id="prosto" enctype="multipart/form-data">
            <input type="hidden" name="auth" value="" />
            <input type="file" id="testfile" name="file" value="">
            <input type="submit" id=" target" value="Отправить" />
        </form>
    
    <div class="over">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Product name</th>
            <th scope="col">Qty</th>
            <th scope="col">Warehouses</th>
          </tr>
        </thead>
        <tbody>
          <?if ($arResult):?>
            <?foreach ($arResult as $key => $arItem):?>
                <?if  (!in_array($arItem[0], $delete) ):?>
                  <tr>
                    <th scope="row"><?=$key+1;?></th>
                    <td><?=$arItem['NAME'];?></td>
                    <td><?=$arItem['QUANTITY'];?></td>
                    <td><?=$arItem['WHAREHOUSES'];?></td>
                  </tr>
                <?endif;?>
            <?endforeach;?>
          <?endif;?>
        </tbody>
      </table>
      </div>
    </div>
  </div>
<script src="https://code.jquery.com/jquery-latest.js"></script>
<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
</body>
</html>

