<?php
  include 'mysql_crud.php';
  //DB read only credentials
  $host = 'mycampus.ctlmvn6rw3p8.us-east-1.rds.amazonaws.com:3306';
  $user = 'appserver';
  $password = 'Publicuser';
  $dbname = 'mycampus';
  $connection = mysqli_connect($host, $user, $password, $dbname) or die("Error " . mysqli_connect_error());
  $semesters = getSemesters($connection);
  mysqli_close($connection);
  $term_key = array( '01' => 'Winter', '05' => 'Spring/Summer', '09' => 'Fall');
?>

<html lang="en">
  <head>
    <meta charset="utf-8">
    <link rel='stylesheet' href='stylesheets/course_calendar.css'>
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
    <link rel='stylesheet' href='stylesheets/fullcalendar.css'>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
    <script src='scripts/moment.min.js'></script>
    <script src='scripts/fullcalendar.js'></script>
    <script src='scripts/course_calendar.js'></script>
  </head>
  <body>
    <div id="header"><h1>Course Calendar</h1></div>
    <div id="main-container">
      <div id="calendar-controller">
        <div id="calendar-term">
          <h2>Select Term</h2>
          <select id="input-term">
            <option selected disabled hidden value=''>Select Term</option>
            <?php
              foreach($semesters as $semester) {
            ?>
                <option value="<?= $semester[0].$semester[1] ?>"><?= $term_key[$semester[1]].' '.$semester[0] ?></option>
            <?php
              }
            ?>
          </select>
          <button id="select-term" type="button">Select Term</button>
        </div>
        <div id="calendar-selector">
          <h2>Select Courses</h2>
          <div id="course-prompt" class="error-message"></div>
          Course Code:
          <input id="input-coursecode" maxlength="10">
          <button id="select-course" type="button">Select Course</button>
        </div>
        <div id="courses-container">
          <h2>Selected Courses</h2>
          <div id="calendar-courses">

          </div>
        </div>
      </div>
      <div id="calendar-container">
        <div id='calendar'></div>
      </div>
    </div>
    <div id="footer"></div>
  </body>

  <script>
    var CourseInfo = {};
    CourseInfo.courses = [];
    $(document).ready(CourseCalendar.setup());
  </script>
</html>
