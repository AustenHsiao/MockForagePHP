<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <meta name="authors" content="Austen Hsiao" />
    <title>Secret Spot</title>
    <link rel="stylesheet" href="/style/forage.css" />
  </head>
  <body>
    
    <nav class="container navBar">
        <h3>Secret Spot</h3>
        <a href="/" name="nav1" id="nav1" >Main</a>
        <a href="/" name="nav2" id ="nav2">Add Spot</a>
        <a href="/" name="nav3" id="nav3">About</a>
    </nav>
    <?php   
        //Look up the database     
        function lookup_locations(){
            $db = parse_url(getenv("DATABASE_URL"));
            $db["path"] = ltrim($db["path"], "/");
            $connection_string = "host=" . $db["host"] . " dbname=" . $db["path"] . " user=" . $db["user"] . " password=" . $db["pass"];
            $connect = pg_connect($connection_string);

            if($connect){   
                /*$fullname = preg_split(" ", $_POST["username_input"]);
                $escape_first = mysql_real_escape_string(trim($fullname[0]));
                $escape_last = mysql_real_escape_string(trim($fullname[1]));*/
                $statement = "SELECT * FROM locations L JOIN users U ON L.user=U.id"/* WHERE U.name_first=$escape_first AND U.name_last=$escape_last"*/;
                $result = pg_query($connect, $statement);
                while($row = pg_fetch_assoc($result)){
                    echo $row['name_first'] . "\n";
                    echo $row['name_last'] . "\n";
                    echo $row['title'] . "\n";
                    echo $row['comment'] . "\n";
                    echo $row['lat'] . "\n";
                    echo $row['lng'] . "\n";
                    echo "\n";
                }

                pg_close($connect);
            }else{
                echo "Not connected";
            }
        }
    ?>
    <div class="container whole">
      <div class="container coord">
        <h3 class="coordTitle" id="searchBar">Search by User Name</h3>
        <input id="userNameInput" type="text" name="username_input" placeholder="Enter name"> <!-- place php here-->
        <!--<input type='button' value="Search" name="search_btn">-->
        <p>
            <?php
                lookup_locations(/*$_POST['username_input']*/);
            ?>
        </p>
        <div class='searchSplit'> 
            <h3 class="coordTitle">Foraging Locations</h3>
            <div class="searchResult"></div>
        </div>
      </div>

      <div class="container map">
        <div id="mapBox">
            <img src="https://i.imgur.com/syTPyyp.png" alt='Google maps mock image'/>
        </div>
      </div>
    </div>
    <div class="container weather" id="weatherBox"></div>

        <script>
  </body>
</html>
