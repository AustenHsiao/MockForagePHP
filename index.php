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
        <button onclick=show_search() class='displayButton'>Search Location(s)</button>
        <button onclick=show_add() class='displayButton'>Add Location</button>
        <form class="coordTitle" id='searchBar' method='post'>
            <label for="username_input">Search by User Name:</label>
            <input type='text' id='username_input' name='username_input' placeholder="Enter name"></input>
            <input type='submit' value='Search' name='username_button' class='form-button'>
        </form>
        <form class="coordTitle" id='addBar' method='post'>
            <label for="username_addition">Add Information:</label><br>
            <input type='text' class="addToDB" id='firstname_addition' name='firstname_addition' placeholder="Enter first name" maxlength="25" required></input>
            <input type='text' class="addToDB" id='lastname_addition' name='lastname_addition' placeholder="Enter last name" maxlength="25" required></input>
            <input type='text' class="addToDB" id='spot_addition' name='spot_addition' placeholder="Spot title" maxlength="50"></input>
            <input type='text' class="addToDB" id='detail_addition' name='detail_addition' placeholder="Spot details" maxlength="200"></input>
            <input type='number' class="addToDB" id='lat_addition' name='lat_addition' placeholder="Latitude" min="-90" max="90" step="0.000001" required></input>
            <input type='number' class="addToDB" id='lng_addition' name='lng_addition' placeholder="Longitude" min="-180" max="180" step="0.000001" required></input>
            <input type='submit' value='Add' class='form-button' name='add_button'>
        </form>

        <div class='searchSplit' id='searchSplit'> 
            <h3 class="coordTitle">Foraging Locations</h3>
            <div class="searchResult">
                <?php
                    if(array_key_exists('username_button', $_POST)){
                        lookup_locations();
                    }else if(array_key_exists('add_button', $_POST)){
                        add_location();
                    }

                    function connect_to_static_DB(){
                        $db = parse_url(getenv("DATABASE_URL"));
                        $db["path"] = ltrim($db["path"], "/");
                        $connection_string = "host=" . $db["host"] . " dbname=" . $db["path"] . " user=" . $db["user"] . " password=" . $db["pass"];
                        return pg_connect($connection_string);
                    }

                    //add to database
                    function add_location(){
                        $connect = connect_to_static_DB();
                        $first_name = $_POST["firstname_addition"];
                        $last_name = $_POST["lastname_addition"];
                        $spot_title = $_POST["spot_addition"];
                        $spot_details = $_POST["detail_addition"];
                        $latitude = $_POST["lat_addition"];
                        $longitude = $_POST["lng_addition"];
                        if($connect){
                            $id = (int)pg_fetch_result(pg_query($connect, 'SELECT MAX(id) AS max_id FROM users'), 0, 0) + 1;
                            pg_query_params($connect, 'INSERT INTO users VALUES ($1, $2, $3)', array($id, $first_name, $last_name));
                            pg_query_params($connect, 'INSERT INTO locations VALUES ($1, $2, $3, $4, $5)', array($spot_title, $spot_details, $id, $latitude, $longitude));
                        }else{
                            echo "Not connected";
                        }
                    }

                    //Look up the database     
                    function lookup_locations(){
                        $connect = connect_to_static_DB();

                        if($connect){   
                            $fullname = preg_split("/ /", $_POST["username_input"]);

                            if(strlen($fullname[0]) == 0){
                                // empty search
                                echo "Enter a name to query the database eg.'firstname lastname' or enter information to add to the database.";
                                return;
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
                                echo "Name: " . $row['name_first'] . " " . $row['name_last'] . "<br>";
                                echo "Title: " . $row['title'] . "<br>";
                                echo "Information: " . $row['comment'] . "<br>";
                                echo "Latitude: " . $row['lat'] . "<br>";
                                echo "Longitude: " . $row['lng'] . "<br>";
                                echo "<br><br>";
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
    <!--<div class="container weather" id="weatherBox"></div>-->

    <script src="forage.js"></script>
  </body>
</html>
