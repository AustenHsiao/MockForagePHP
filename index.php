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
    <div class="container whole">
      <div class="container coord">
        <form class="coordTitle" id='searchBar' method='post'>
            <label for="username">Search by User Name:</label>
            <input type='text' id='username_input' name='username_input' placeholder="Enter name"></input>
            <input type='submit' value='submit' name='username_button'>
        </form>

        <!--<h3 class="coordTitle" id="searchBar">Search by User Name</h3>
        <input id="userNameInput" type="text" name="username_input" >
        <input type='button' value="Search" name="search_btn">-->
            
        <div class='searchSplit'> 
            <h3 class="coordTitle">Foraging Locations</h3>
            <div class="searchResult">
                <?php
                    if(array_key_exists('username_button', $_POST)){
                        lookup_locations();
                    }

                    //Look up the database     
                    function lookup_locations(){
                        $db = parse_url(getenv("DATABASE_URL"));
                        $db["path"] = ltrim($db["path"], "/");
                        $connection_string = "host=" . $db["host"] . " dbname=" . $db["path"] . " user=" . $db["user"] . " password=" . $db["pass"];
                        $connect = pg_connect($connection_string);

                        if($connect){   
                            $fullname = preg_split("/ /", $_POST["username_input"]);
                            echo count($fullname);
                            echo $fullname;
                            echo $fullname[0];
                            echo strlen($fullname[0]);
                            if(strlen($fullname[0]) == 0){
                                echo "Enter a name to query the database eg.'firstname lastname'";
                                exit();
                            }else if(count($fullname) == 1){
                                // only 1 name (first) OR empty
                                $escape_first = pg_escape_string(trim($fullname[0]));
                                $result = pg_query_params($connect, 'SELECT * FROM users U JOIN Locations L ON U.id=L.user WHERE U.name_first=$1', array($escape_first));
                            }else{
                                // 2 names or more-- only the first two words count
                                $escape_first = pg_escape_string(trim($fullname[0]));
                                $escape_last = pg_escape_string(trim($fullname[1]));
                                $result = pg_query_params($connect, 'SELECT * FROM users U JOIN Locations L ON U.id=L.user WHERE U.name_first=$1 AND U.name_last=$2', array($escape_first, $escape_last));
                            }

                            while($row = pg_fetch_assoc($result)){
                                echo $row['name_first'] . "\n";
                                echo $row['name_last'] . "\n";
                                echo $row['title'] . "\n";
                                echo $row['comment'] . "\n";
                                echo $row['lat'] . "\n";
                                echo $row['lng'] . "\n";
                                echo "\n\n";
                            }

                            pg_close($connect);
                        }else{
                            echo "Not connected";
                        }
                    }
                ?>
            </div>
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
