<?

// include your own settings file with database connection here!
include("../../settings.php");

$run = true;

$apiurl = "http://verdwenengebouwen.nl/api/search/?place=Amsterdam&limit=1000"; 

$json = file_get_contents($apiurl);

$buildings = json_decode($json,true);

foreach ($buildings['results'] as $k => $building) {

    // get both preferred name and alternative names
    $names = array();
    if($building['alt_names']!=""){
        $names = explode(",", $building['alt_names']);
    }
    $names[] = $building['name'];
    
    // insert each name ...
    foreach ($names as $key => $name) {

        if(!preg_match('/[0-9]$/',$name)){ // ... unless it has the appearance of an address
            $sql = "insert into amsterdam_building_names (name,vg_id,building_type) values(
                        '" . $mysqli->real_escape_string(trim($name)) . "',
                        'http://verdwenengebouwen.nl/gebouw/" . $building['id'] . "',
                        '" . $building['type'] . "' )
                        ";

            if($run){
                $ins = $mysqli->query($sql);
                echo " .";
            }else{
                echo $sql . "\n\n";
            }
        }
    }
    
}




?>