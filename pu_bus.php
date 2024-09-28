<?php

$host = "localhost";
$username = "root";
$password = "";
$db = "pubus";

// $con = @mysqli_conect($host, $username, $password, $db) or die(mysql_error);
$con = new mysqli($host, $username, $password, $db);

/* ============ This for getting the url with function======================*/
@$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
$action = preg_replace('/[^a-z0-9_]+/i', '', array_shift($request));


switch ($action) {
	case "getview":
		getView();
		break;
	case "adddriver":
		addDriver();
		break;
	case "deletedriver":
		deleteDriver();
		break;
	case "addbus":
		addBus();
		break;	
	case "deletebus":
		deleteBus();
		break;
	case "addroute":
		addRoute();
		break;
	case "deleteroute":
		deleteRoute();
		break;
	case "deleteAllRoute":
		deleteAllRoute();
		break;
	case "addshift":
		addShift();
		break;
	case "updateshift":
		updateShift();
		break;
	case "deleteshiftid":
		deleteShiftId();
		break;
	case "deleteshiftname":
		deleteShiftName();
		break;
	default:
		echo "no function found";
		break;
}


function getView()
{
	global $con;
// Check if the required parameters are set
if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare the SQL query
    $sql = "SELECT 
                s.student_name, 
                s.shift_name, 
                s.route_area_no, 
                s.route_name,
                sd.bus_no, 
                bd.driver_name, 
                bd.section, 
                bd.slot_no 
            FROM 
                student_list s
            JOIN 
                shift_data sd ON s.shift_name = sd.shift_name
                               AND s.route_area_no = sd.route_area_no
                               AND s.route_name = sd.route_name
            JOIN 
                bus_data bd ON sd.bus_no = bd.bus_no
            WHERE 
                s.username = ? 
                AND s.password = ?";

    // Prepare the statement to prevent SQL injection
    if ($stmt = $con->prepare($sql)) {
        // Bind parameters
        $stmt->bind_param("ss", $username, $password);

        // Execute the statement
        $stmt->execute();

        // Get the result
        $result = $stmt->get_result();

        // Check if any rows are returned
        if ($result->num_rows > 0) {
            // Fetch all rows as an associative array
            $data = $result->fetch_all(MYSQLI_ASSOC);

            // Return the data as JSON
            echo json_encode($data);
        } else {
            echo json_encode(["message" => "No records found"]);
        }

        // Close the statement
        $stmt->close();
    } else {
        echo json_encode(["message" => "Query preparation failed"]);
    }
} else {
    echo json_encode(["message" => "Invalid input"]);
}


}

function addShift(){

	global $con;
	// Check connection
if ($con->connect_error) {
    die(json_encode(["message" => "Connection failed: " . $con->connect_error]));
}

// Check if the required parameter is set
if (isset($_POST['shift_name'])) {
    $shift_name = $_POST['shift_name'];

    // Prepare the SQL query to insert the new shift
    $sql = "INSERT INTO shift (shift_name) VALUES (?)";

    // Prepare the statement to prevent SQL injection
    if ($stmt = $con->prepare($sql)) {
        // Bind the parameter
        $stmt->bind_param("s", $shift_name);

        // Execute the statement
        if ($stmt->execute()) {
            echo json_encode(["message" => "Shift added successfully"]);
        } else {
            echo json_encode(["message" => "Failed to add shift"]);
        }

        // Close the statement
        $stmt->close();
    } else {
        echo json_encode(["message" => "Query preparation failed"]);
    }
} else {
    echo json_encode(["message" => "Invalid input"]);
}
}

function updateShift(){
	global $con;
	// Check connection
if ($con->connect_error) {
    die(json_encode(["message" => "Connection failed: " . $con->connect_error]));
}

// Check if the required parameters are set
if (isset($_POST['shift_id']) && isset($_POST['shift_name'])) {
    $shift_id = $_POST['shift_id'];
    $shift_name = $_POST['shift_name'];

    // Prepare the SQL query to update the shift name
    $sql = "UPDATE shift SET shift_name = ? WHERE shift_id = ?";

    // Prepare the statement to prevent SQL injection
    if ($stmt = $con->prepare($sql)) {
        // Bind the parameters
        $stmt->bind_param("si", $shift_name, $shift_id);

        // Execute the statement
        if ($stmt->execute()) {
            // Check if any rows were affected
            if ($stmt->affected_rows > 0) {
                echo json_encode(["message" => "Shift updated successfully"]);
            } else {
                echo json_encode(["message" => "No shift found with the given ID"]);
            }
        } else {
            echo json_encode(["message" => "Failed to update shift"]);
        }

        // Close the statement
        $stmt->close();
    } else {
        echo json_encode(["message" => "Query preparation failed"]);
    }
} else {
    echo json_encode(["message" => "Invalid input"]);
}

}

function deleteShiftId(){
		global $con;	
		// Check if the required parameter is set
if (isset($_POST['shift_id'])) {
    $shift_id = $_POST['shift_id'];

    // Prepare the SQL query to delete the shift
    $sql = "DELETE FROM shift WHERE shift_id = ?";

    // Prepare the statement to prevent SQL injection
    if ($stmt = $con->prepare($sql)) {
        // Bind the parameter
        $stmt->bind_param("i", $shift_id);

        // Execute the statement
        if ($stmt->execute()) {
            // Check if any rows were affected
            if ($stmt->affected_rows > 0) {
                echo json_encode(["message" => "Shift deleted successfully"]);
            } else {
                echo json_encode(["message" => "No shift found with the given ID"]);
            }
        } else {
            echo json_encode(["message" => "Failed to delete shift"]);
        }

        // Close the statement
        $stmt->close();
    } else {
        echo json_encode(["message" => "Query preparation failed"]);
    }
} else {
    echo json_encode(["message" => "Invalid input"]);
}
}

function deleteShiftName(){
	global $con;
	// Check connection
if ($con->connect_error) {
    die(json_encode(["message" => "Connection failed: " . $con->connect_error]));
}

// Check if the required parameter is set
if (isset($_POST['shift_name'])) {
    $shift_name = $_POST['shift_name'];

    // Prepare the SQL query to delete the shift
    $sql = "DELETE FROM shift WHERE shift_name = ?";

    // Prepare the statement to prevent SQL injection
    if ($stmt = $con->prepare($sql)) {
        // Bind the parameter
        $stmt->bind_param("s", $shift_name);

        // Execute the statement
        if ($stmt->execute()) {
            // Check if any rows were affected
            if ($stmt->affected_rows > 0) {
                echo json_encode(["message" => "Shift deleted successfully"]);
            } else {
                echo json_encode(["message" => "No shift found with the given name"]);
            }
        } else {
            echo json_encode(["message" => "Failed to delete shift"]);
        }

        // Close the statement
        $stmt->close();
    } else {
        echo json_encode(["message" => "Query preparation failed"]);
    }
} else {
    echo json_encode(["message" => "Invalid input"]);
}

}


function deleteAllRoute(){
	global $con;	
	// Check if the required parameter is set
if (isset($_POST['route_area_no'])) {
    $route_area_no = $_POST['route_area_no'];

    // Prepare the SQL query to delete all routes for the specified route area number
    $sql = "DELETE FROM route_list WHERE route_area_no = ?";

    // Prepare the statement to prevent SQL injection
    if ($stmt = $con->prepare($sql)) {
        // Bind the parameter
        $stmt->bind_param("i", $route_area_no);

        // Execute the statement
        if ($stmt->execute()) {
            // Check if any rows were affected
            if ($stmt->affected_rows > 0) {
                echo json_encode(["message" => "Routes deleted successfully"]);
            } else {
                echo json_encode(["message" => "No routes found with the given area number"]);
            }
        } else {
            echo json_encode(["message" => "Failed to delete routes"]);
        }

        // Close the statement
        $stmt->close();
    } else {
        echo json_encode(["message" => "Query preparation failed"]);
    }
} else {
    echo json_encode(["message" => "Invalid input"]);
}
}

function deleteRoute(){
	global $con;	
	if ($con->connect_error) {

		die(json_encode(["message" => "Connection failed: " . $con->connect_error]));
	}
	
	// Check if the required parameters are set
	if (isset($_POST['route_area_no']) && isset($_POST['route_name'])) {
		$route_area_no = $_POST['route_area_no'];
		$route_name = $_POST['route_name'];
	
		// Prepare the SQL query to delete the route
		$sql = "DELETE FROM route_list WHERE route_area_no = ? AND route_name = ?";
	
		// Prepare the statement to prevent SQL injection
		if ($stmt = $con->prepare($sql)) {
			// Bind the parameters
			$stmt->bind_param("is", $route_area_no, $route_name);
	
			// Execute the statement
			if ($stmt->execute()) {
				// Check if any rows were affected
				if ($stmt->affected_rows > 0) {
					echo json_encode(["message" => "Route deleted successfully"]);
				} else {
					echo json_encode(["message" => "No route found with the given area number and name"]);
				}
			} else {
				echo json_encode(["message" => "Failed to delete route"]);
			}
	
			// Close the statement
			$stmt->close();
		} else {
			echo json_encode(["message" => "Query preparation failed"]);
		}
	} else {
		echo json_encode(["message" => "Invalid input"]);
	}
}
function addRoute(){
	global $con;
	// Check connection
if ($con->connect_error) {
    die(json_encode(["message" => "Connection failed: " . $con->connect_error]));
}

// Check if the required parameters are set
if (isset($_POST['route_name']) && isset($_POST['route_area_no'])) {
    $route_name = $_POST['route_name'];
    $route_area_no = $_POST['route_area_no'];

    // Prepare the SQL query to insert the new route
    $sql = "INSERT INTO route_list (route_name, route_area_no) VALUES (?, ?)";

    // Prepare the statement to prevent SQL injection
    if ($stmt = $con->prepare($sql)) {
        // Bind parameters
        $stmt->bind_param("si", $route_name, $route_area_no);

        // Execute the statement
        if ($stmt->execute()) {
            echo json_encode(["message" => "Route added successfully"]);
        } else {
            echo json_encode(["message" => "Failed to add route"]);
        }

        // Close the statement
        $stmt->close();
    } else {
        echo json_encode(["message" => "Query preparation failed"]);
    }
} else {
    echo json_encode(["message" => "Invalid input"]);
}

}

function deleteBus()	{
	global $con;	
	if ($con->connect_error) {
		die(json_encode(["message" => "Connection failed: " . $con->connect_error]));
	}
	
	// Check if the required parameter is set
	if (isset($_POST['bus_id'])) {
		$bus_id = $_POST['bus_id'];
	
		// Prepare the SQL query to delete the bus
		$sql = "DELETE FROM bus_list WHERE bus_id = ?";
	
		// Prepare the statement to prevent SQL injection
		if ($stmt = $con->prepare($sql)) {
			// Bind the bus ID parameter
			$stmt->bind_param("i", $bus_id);
	
			// Execute the statement
			if ($stmt->execute()) {
				// Check if any rows were affected
				if ($stmt->affected_rows > 0) {
					echo json_encode(["message" => "Bus deleted successfully"]);
				} else {
					echo json_encode(["message" => "No bus found with the given ID"]);
				}
			} else {
				echo json_encode(["message" => "Failed to delete bus"]);
			}
	
			// Close the statement
			$stmt->close();
		} else {
			echo json_encode(["message" => "Query preparation failed"]);
		}
	} else {
		echo json_encode(["message" => "Invalid input"]);
	}
	
}
function addBus(){
	global $con;
	if ($con->connect_error) {
		die(json_encode(["message" => "Connection failed: " . $con->connect_error]));
	}
	
	// Check if the required parameter is set
	if (isset($_POST['bus_no'])) {
		$bus_no = $_POST['bus_no'];
	
		// Prepare the SQL query to insert the new bus
		$sql = "INSERT INTO bus_list (bus_no) VALUES (?)";
	
		// Prepare the statement to prevent SQL injection
		if ($stmt = $con->prepare($sql)) {
			// Bind the bus number parameter
			$stmt->bind_param("s", $bus_no);
	
			// Execute the statement
			if ($stmt->execute()) {
				echo json_encode(["message" => "Bus added successfully"]);
			} else {
				echo json_encode(["message" => "Failed to add bus"]);
			}
	
			// Close the statement
			$stmt->close();
		} else {
			echo json_encode(["message" => "Query preparation failed"]);
		}
	} else {
		echo json_encode(["message" => "Invalid input"]);
	}
	
}

function deleteDriver(){

	global $con;
	if ($con->connect_error) {
		die(json_encode(["message" => "Connection failed: " . $con->connect_error]));
	}
	
	// Check if the required parameter is set
	if (isset($_POST['driver_id'])) {
		$driver_id = $_POST['driver_id'];
	
		// Prepare the SQL query to delete the driver
		$sql = "DELETE FROM driver_list WHERE driver_id = ?";
	
		// Prepare the statement to prevent SQL injection
		if ($stmt = $con->prepare($sql)) {
			// Bind the driver ID parameter
			$stmt->bind_param("i", $driver_id);
	
			// Execute the statement
			if ($stmt->execute()) {
				// Check if any rows were affected
				if ($stmt->affected_rows > 0) {
					echo json_encode(["message" => "Driver deleted successfully"]);
				} else {
					echo json_encode(["message" => "No driver found with the given ID"]);
				}
			} else {
				echo json_encode(["message" => "Failed to delete driver"]);
			}
	
			// Close the statement
			$stmt->close();
		} else {
			echo json_encode(["message" => "Query preparation failed"]);
		}
	} else {
		echo json_encode(["message" => "Invalid input"]);
	}
}

function addDriver(){

	global $con;
	if ($con->connect_error) {
		die(json_encode(["message" => "Connection failed: " . $con->connect_error]));
	}
	
	// Check if the required parameters are set
	if (isset($_POST['driver_name']) && isset($_POST['driver_mobile_no'])) {
		$driver_name = $_POST['driver_name'];
		$driver_mobile_no = $_POST['driver_mobile_no'];
	
		// Prepare the SQL query to insert the new driver
		$sql = "INSERT INTO driver_list (driver_name, driver_mobile_no) VALUES (?, ?)";
	
		// Prepare the statement to prevent SQL injection
		if ($stmt = $con->prepare($sql)) {
			// Bind parameters
			$stmt->bind_param("ss", $driver_name, $driver_mobile_no);
	
			// Execute the statement
			if ($stmt->execute()) {
				echo json_encode(["message" => "Driver added successfully"]);
			} else {
				echo json_encode(["message" => "Failed to add driver"]);
			}
	
			// Close the statement
			$stmt->close();
		} else {
			echo json_encode(["message" => "Query preparation failed"]);
		}
	} else {
		echo json_encode(["message" => "Invalid input"]);
	}
}

function register()
{

	global $con;
	$name = $_POST["key_name"];
	$email = $_POST["key_email"];
	$password = $_POST["key_pass"];
	$gender = $_POST["key_gender"];


	$my_query = "insert into register (name,email,password,gender) values ('$name','$email','$password','$gender')";

	$result = mysqli_query($con, $my_query);

	if ($result) {
		$my_result = array("errorcode" => "0000", "message" => "success");
		echo json_encode($my_result);

	} else {

		$my_result = array("errorcode" => "0000", "message" => "fail");
		echo json_encode($my_result);

	}
}

function login_user()
{
	global $con;
	$email = $_POST["key_email"];
	$password = $_POST["key_pass"];

	$my_query = "select name,email from register where email='$email' and password='$password'";

	$result = mysqli_query($con, $my_query);

	$response = array();
	if (mysqli_num_rows($result) > 0) {

		while ($row = mysqli_fetch_assoc($result)) {

			$response = $row;

		}
		echo json_encode($response);
	} else {

		$arr = array("errorcode" => "1111", "message" => "Email and password didn't match");
		echo json_encode($arr);
	}
}

function login_admin()
{
	global $con;
	$email = $_POST["key_email"];
	$password = $_POST["key_pass"];

	$my_query = "select name,email from admin where email='$email' and password='$password'";
	$result = mysqli_query($con, $my_query);

	$response = array();
	if (mysqli_num_rows($result) > 0) {

		while ($row = mysqli_fetch_assoc($result)) {

			$response = $row;

		}
		echo json_encode($response);
	} else {

		$arr = array("errorcode" => "1111", "message" => "Email and password didn't match");
		echo json_encode($arr);
	}
}
?>