<?php
  include 'mysql_crud.php';
  //DB read only credentials
  $host = 'mycampus.ctlmvn6rw3p8.us-east-1.rds.amazonaws.com:3306';
  $user = 'appserver';
  $password = 'Publicuser';
  $dbname = 'mycampus';

  //Determines if valid query (includes query_type field)
  if(isset($_GET['query_type'])) {
    $query_type = $_GET['query_type'];

    if($query_type == 'semesters') {
      //Year selection - Finds all available terms
      $connection = mysqli_connect($host, $user, $password, $dbname) or die("Error " . mysqli_connect_error());

      //Queries all semesters in db
      $sql = "SELECT year, term FROM Semesters ORDER BY year DESC, term DESC";
      $result = $connection->query($sql);

      //Prepares results to be converted to JSON
      $semesters = array();
      if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
          array_push($semesters, array($row['year'], $row['term']));
        }
      }

      echo json_encode($semesters);
      mysqli_close($connection);
    } elseif($query_type == 'course_code') {
      //Course codes - Finds all course codes for specified semester
      //Ensures appropriate parameters specified
      if(!isset($_GET['year']) || !isset($_GET['term']) || !isset($_GET['keyword'])) {
        echo 'Incorrect Parameters Specified';
      } else {
        $year = $_GET['year'];
        $term = $_GET['term'];
        $key_word = $_GET['keyword'].'%';

        //Queries for course codes that match specified semester and query term
        $connection = mysqli_connect($host, $user, $password, $dbname) or die("Error " . mysqli_connect_error());
        $course_sql = $connection->prepare("SELECT subject, code FROM Courses ".
          "LEFT JOIN Semesters ON semester_id = Semesters.id WHERE year = ? AND ".
          "term = ? AND concat_ws(' ', subject, code) like ? ORDER BY subject ASC, code ASC LIMIT 5");
        $course_sql->bind_param('sss', $year, $term, $key_word);
        $course_sql->execute();

        //Prepares results to be converted to JSON
        $course_codes = array();
        $course_sql->bind_result($course_subj, $course_code);
        while ($course_sql->fetch()) {
          array_push($course_codes, $course_subj.' '.$course_code);
        }

        echo json_encode($course_codes);
        $course_sql->close();
        mysqli_close($connection);
      }
    } elseif($query_type == 'course_info') {
      //Course selection - Finds section/session info for specified course
      //Ensures appropriate parameters specified
      if(!isset($_GET['year']) || !isset($_GET['term']) || !isset($_GET['subject']) || !isset($_GET['code'])) {
        echo 'Incorrect Parameters Specified';
      } else  {
        $year = $_GET['year'];
        $term = $_GET['term'];
        $subject = $_GET['subject'];
        $code = $_GET['code'];

        $connection = mysqli_connect($host, $user, $password, $dbname) or die("Error " . mysqli_connect_error());
        //getCourseID($connection, $subject, $code, $year, $term);
        $course_sql->close();
      }
    }
  }
?>
