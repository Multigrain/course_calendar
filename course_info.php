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

      //Finds all available semesters
      $semesters = getSemesters($connection);

      //Changes semesters to plain english
      $term_key = array( '01' => 'Winter', '05' => 'Spring/Summer', '09' => 'Fall');
      $english_semesters = array();
      foreach ($semesters as $semester) {
        array_push($english_semesters, $term_key[$semester[1]].' '.$semester[0]);
      }

      echo json_encode($english_semesters, JSON_UNESCAPED_SLASHES);
      header('Content-Type: application/json');
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

        //Finds course codes similar to partial match
        $connection = mysqli_connect($host, $user, $password, $dbname) or die("Error " . mysqli_connect_error());
        $course_codes = getCourseCodes($connection, $year, $term, $key_word);

        echo json_encode($course_codes);
        header('Content-Type: application/json');
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

        //Finds course info for matching semester
        $connection = mysqli_connect($host, $user, $password, $dbname) or die("Error " . mysqli_connect_error());
        $course_info = getCourseInfo($connection, $subject, $code, $year, $term);
        mysqli_close($connection);

        //Checks if course info found
        if(empty($course_info)) {
          echo 'Course not found.';
        } else {
          echo json_encode($course_info);
          header('Content-Type: application/json');
        }
      }
    }
  }
?>
