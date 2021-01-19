<?php
require_once "class/Log.php";
require_once "class/MysqlConfig.php";
require_once "class/MysqlDatabase.php";
?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">

    <title>MysqlDatabase workbench</title>
  </head>
  <body>
    <div class="container">
      <div class="row-sm">
        <div class="col">
        </div>
        <div class="col">


    <?php 
    /** init  */
    $database = "flurp";
    $table = "snarf";

    ?>
    <h1>MysqlDatabase workbench</h1>
    <h2>Create Log object</h2>
    <?php
        $log = new Log(time() . ".log");
        if (!empty($log))
        {
          echo "Log object created.";
        } else {
          echo "Log object NOT created.";
        }
    ?>
    <h2>Create Config object</h2>
    <?php 
        $config = new MysqlConfig();
        if (!empty($config))
        {
          echo "Config object created.";
        } else {
          echo "Config object NOT created.";
        }
    ?>

    <h2>Create MysqlDatabase instance</h2>
    <?php 
        $db = new MysqlDatabase($config, $log);
        if (!empty($db))
        {
          echo "Database object created.";
        } else {
          echo "Database object NOT created.";
        }

    ?>

    <h2>Check if database exists</h2>
    <?php 
        if ($db->databaseExists($database))
        {
          echo "Database {$database} exists";
        } else {
          echo "Database {$database} does not exist";
        }
    ?>

    <!-- create a database -->
    <h2>Create a new database</h2>
    <?php 
        if ($db->createDatabase($database))
        {
          echo "Database {$database} was created";
        } else {
          echo "Database {$database} was NOT created";

        }
    ?>

    <h2>Check if database exists</h2>
    <?php 
        if ($db->databaseExists($database))
        {
          echo "Database {$database} exists";
        } else {
          echo "Database {$database} does not exist";
        }
    ?>

    <!-- create a table -->
    <h2>Create a table</h2>
    <?php 
        $sql = "CREATE TABLE {$database}.{$table} (`id` int(11) NOT NULL, `descr` varchar(40) NOT NULL)";
        if ($db->createTable($database, $table, $sql))
        {
          echo "Table snarf was created.";
        } else {
          echo "Database snarf was not created.";
        }
    ?>

    <h2>Insert a record</h2>
    <?php 
        $sql = "INSERT INTO {$database}.{$table} (`id`, `descr`) VALUES (?, ?)";
        if ($db->prepare($sql))
        {
          $db->bind("i", "1");
          $db->bind("s", "one"); 

          if ($db->execute())
          {
            echo "First record was created.";
          } else {
            echo "First record was not created.";
          }
        } else {
          echo "Preparation failed.";
        }


    ?>

    <h2>Insert another record</h2>
    <?php 
        $sql = "INSERT INTO {$database}.{$table} (`id`, `descr`) VALUES (?, ?)";
        if ($db->prepare($sql))
        {
          $db->bind("i", "2");
          $db->bind("s", "two"); 

          if ($db->execute())
          {
            echo "Second record was created.";
          } else {
            echo "Second record was not created.";
          }
        } else {
          echo "Preparation failed.";
        }
      
    ?>

    <h2>Select the inserted records</h2>
    <?php
      getRecords($db, $database, $table);
    ?>

    <h2>Update records</h2>
    <?php
        $sql = "UPDATE {$database}.{$table} SET descr = ? WHERE id = ? ";
        if ($db->prepare($sql))
        {
          $db->bind("s", "three");
          $db->bind("i", "2");

          if ($db->execute())
          {
            echo "Update succeeded.";

          } else {
            echo "Update failed.";
          }
        } else {
          echo "Preparation failed.";
        }
    ?>

    <h2>Select the updated records</h2>
    <?php
      getRecords($db, $database, $table);
    ?>

    <h2>Delete a record</h2>
    <?php
        $sql = "DELETE FROM {$database}.{$table} WHERE descr = ?";
        if ($db->prepare($sql))
        {
          $db->bind("s", "three");

          if ($db->execute())
          {
            echo "Delete succeeded.";

          } else {
            echo "Delete failed.";
          }
        } else {
          echo "Preparation failed.";
        }
    ?>

    <h2>Select the remaining records</h2>
    <?php
      getRecords($db, $database, $table);
    ?>

    <h2>Truncate a table </h2>
    <?php
        $sql = "TRUNCATE TABLE {$database}.{$table}";
        if ($db->prepare($sql))
        {
          if ($db->execute())
          {
            echo "Truncate succeeded.";

          } else {
            echo "Truncate failed.";
          }
        } else {
          echo "Preparation failed.";
        }
    ?>

    <h2>Show the empty table</h2>
    <?php
      getRecords($db, $database, $table);
    ?>

    <!-- drop a table -->
    <h2>Drop a table</h2>
    <?php 

        if ($db->dropTable($database, $table))
        {
          echo "Table {$database}.{$table} was dropped.";
        } else {
          echo "Database {$database}.{$table} was not dropped.";
        }
    ?>
    <!-- create a database -->
    <h2>Drop a database</h2>
    <?php 
        if ($db->dropDatabase($database))
        {
          echo "Database {$database} was dropped.";
        } else {
          echo "Database {$database} was not dropped.";
          }
    ?>

    <h2>Check if database already exists</h2>
    <?php 
        if ($db->databaseExists($database))
        {
          echo "Database {$database} exists";
        } else {
          echo "Database {$database} does not exist";
        }
    ?>
    <h1>*** Done ***</h1>

        </div>
        <div class="col-sm">
        </div>
      </div>
    </div>

    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js" integrity="sha384-q2kxQ16AaE6UbzuKqyBE9/u/KzioAlnx2maXQHiDX9d4/zp8Ok3f+M7DPm+Ib6IU" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-pQQkAEnwaBkjpqZ8RU1fF1AKtTcHJwFl3pblpTlHXybJjHpMYo79HY3hIi4NKxyj" crossorigin="anonymous"></script>
    -->
  </body>
</html>

<?php
function getRecords(MysqlDatabase $db, string $database, string $table)
{
  $sql = "SELECT * FROM {$database}.{$table}";
  if ($db->prepare($sql))
  {
    if ($db->execute())
    {
      $rows = $db->getRows();
      echo "<table>\n";
      echo "<tr><th>id</th><th>descr</th></tr>\n";
      foreach ($rows as $row)
      {
        echo "<tr><td>{$row['id']}</td><td>{$row['descr']}</td></tr>\n";
      }
      echo "</table>";
      
    } else {
      echo "Select failed.";
    }
  } else {
    echo "Preparation failed.";
  }
}
?>