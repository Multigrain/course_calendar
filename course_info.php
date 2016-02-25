<?php
  //DB read only credentials
  $db_host = 'mycampus.ctlmvn6rw3p8.us-east-1.rds.amazonaws.com:3306'
  $db_user = 'appserver'
  $db_pass = 'Publicuser'
  $db_schema = 'mycampus'

  //Determines if valid query (includes query_type field)
  if(isset($_GET['query_type'])) {
    $query_type = $_GET['query_type']

    if($query_type == 'semesters') {
      //Year selection - Finds all available terms
      $connection = mysqli_connect($db_host, $db_user, $db_pass, $db_schema) or die("Error " . mysqli_error($connection));

      $sql = "SELECT year, term FROM Semesters;";
      $result = $connection->query($sql);

      $semesters = array();
      if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
          array_push($semesters, array($row['year'], $row['semester']));
        }
      }

      echo json_encode($semesters);
      mysqli_close($connection);
    } elseif($query_type == 'course_code') {
      //Course codes - Finds all course codes for specified semester



    } elseif($query_type == 'course_info') {
      //Course selection - Finds section/session info for specified course

    }

  }


?>
