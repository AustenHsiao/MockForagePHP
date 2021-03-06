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
            <input type='text' id='firstname_input' name='firstname_input' placeholder="Enter first name" maxlength="25" required></input>
            <input type='text' id='lastname_input' name='lastname_input' placeholder="Enter last name" maxlength="25"></input>
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
                            // check to see the name already exists. Case sensitive. We're basically using the firstname/lastname as a key, which isn't good, but
                            // this is just a mock up to use php
                            $exist = pg_query_params($connect, 'SELECT U.id FROM users U WHERE U.name_first=$1 AND U.name_last=$2', array($first_name, $last_name));
                            if(pg_num_rows($exist) != 0){
                                $id = pg_fetch_result($exist, 0, 0);
                            }else{
                                $id = (int)pg_fetch_result(pg_query($connect, 'SELECT MAX(id) AS max_id FROM users'), 0, 0) + 1;
                                pg_query_params($connect, 'INSERT INTO users VALUES ($1, $2, $3)', array($id, $first_name, $last_name));
                            }
                            pg_query_params($connect, 'INSERT INTO locations VALUES ($1, $2, $3, $4, $5)', array($spot_title, $spot_details, $id, $latitude, $longitude));
                        
                            echo "Name: " . $first_name . " " . $last_name . "<br>";
                            echo "Title: " . $spot_title . "<br>";
                            echo "Information: " . $spot_details . "<br>";
                            echo "Latitude: " . $latitude . "<br>";
                            echo "Longitude: " . $longitude . "<br>";
                            echo "ADDED TO DATABASE";

                            pg_close($connect);
                        }else{
                            echo "Not connected";
                        }
                    }

                    //Look up the database     
                    function lookup_locations(){
                        $connect = connect_to_static_DB();

                        if($connect){   
                            $firstname = $_POST["firstname_input"];
                            $lastname = $_POST["lastname_input"];
                            if(strlen($lastname) == 0){
                                // only firstname
                                $result = pg_query_params($connect, 'SELECT * FROM users U JOIN Locations L ON U.id=L.user WHERE U.name_first=$1', array($firstname));
                            }else{
                                $result = pg_query_params($connect, 'SELECT * FROM users U JOIN Locations L ON U.id=L.user WHERE U.name_first=$1 AND U.name_last=$2', array($firstname, $lastname));
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
