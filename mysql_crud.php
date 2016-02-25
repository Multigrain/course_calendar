<?php
  function getCourseID($connection, $subject, $code, $year, $term) {
    //Finds course id that matches specified parameters
    $course_sql = $connection->prepare("SELECT Courses.id FROM Courses ".
      "LEFT JOIN Semesters ON semester_id = Semesters.id ".
      "WHERE subject = ? AND code = ? AND year = ? AND term = ?");
    $course_sql->bind_param('ssss', $subject, $code, $year, $term);
    $course_sql->execute();

    //Reads results

    $course_sql->bind_result($id);
    $course_sql->fetch();
    $course_id = $id;
    $course_sql->close();

    return $course_id;
  }
?>
