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
    <div id="header">Header</div>
    <div id="main-container">
      <div id="calendar-controller">
        <input id="Courses">
        Left Nav
      </div>
      <div id="calendar-container">
        <div id='calendar'></div>
      </div>
    </div>
    <div id="footer">Footer</div>
  </body>

  <script>
    var CourseInfo = {};
    CourseInfo.courses = [];
    $(document).ready(CourseCalendar.setup());
  </script>
</html>
