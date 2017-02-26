<?

// include your own settings file with database connection here!
include("../../settings.php");

$run = false;

/*

export geojson from https://overpass-turbo.eu/ after zooming in on Amsterdam and using 
this query:

[bbox:{{bbox}}];
((
// Query all buildings
  way[building=yes][name~"."];  
); >; );
out;

*/

// set path to geojson file mentioned above
$geojsonpath = "/Users/mennodenengelse/Documents/opdrachtgevers/hicsuntleones/gebouwnamen/ruwe-data/osm-overpass.geojson"; 

$json = file_get_contents($geojsonpath);

$buildings = json_decode($json,true);

foreach ($buildings['features'] as $k => $building) {


    // get both preferred name and alternative names
    $names = array();
    if($building['properties']['name']!=""){
        $names[] = $building['properties']['name'];
    }
    if($building['properties']['old_name']!=""){
        $names[] = $building['properties']['old_name'];
    }
    if($building['properties']['alt_name']!=""){
        $names[] = $building['properties']['alt_name'];
    }
    
    
    // insert each name ...
    foreach ($names as $key => $name) {

        if(isset($building['properties']['ref:bag'])){
            
            if(isset($building['properties']['amenity'])){
                $type = $building['properties']['amenity'];
            }else{
                $type = '';
            }

            $sql = "insert into amsterdam_building_names (name,bag_id,building_type_label, name_source) values(
                    '" . $mysqli->real_escape_string(trim($name)) . "',
                    '" . $building['properties']['ref:bag'] . "',
                    '" . $type . "',
                    'osm' )
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