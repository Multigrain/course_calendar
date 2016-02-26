<?php
  function getCourseID($connection, $subject, $code, $year, $term) {
    //Finds course id that matches specified parameters
    $course_sql = $connection->prepare("SELECT Courses.id FROM Courses ".
      "LEFT JOIN Semesters ON semester_id = Semesters.id ".
      "WHERE subject = ? AND code = ? AND year = ? AND term = ?");
    $course_sql->bind_param('ssss', $subject, $code, $year, $term);
    $course_sql->execute();

    //Reads results
    $course_sql->bind_result($course_id);
    $course_sql->fetch();

    $course_sql->close();
    return $course_id;
  }

  function getCourse($connection, $course_id) {
    //Finds course info matching course id
    $course_sql = $connection->prepare("SELECT Courses.title, ".
      "GROUP_CONCAT(Levels.level SEPARATOR ', ') FROM Courses LEFT JOIN Course_Levels ".
      "INNER JOIN Levels ON Course_Levels.level_id = Levels.id ON Course_Levels.course_id = Courses.id ".
      "WHERE Courses.id = ? GROUP BY Courses.id");
    $course_sql->bind_param('s', $course_id);
    $course_sql->execute();

    //Reads results
    $course_info = array();
    $course_sql->bind_result($course_title, $course_levels);
    $course_sql->fetch();
    $course_info['title'] = $course_title;
    $course_info['levels'] = $course_levels;

    $course_sql->close();
    return $course_info;
  }

  function getSectionIDs($connection, $course_id) {
    //Finds section id's that match specified parameters
    $section_sql = $connection->prepare("SELECT id FROM Sections WHERE course_id = ?");
    $section_sql->bind_param('s', $course_id);
    $section_sql->execute();

    //Reads results
    $section_ids = array();
    $section_sql->bind_result($section_id);
    while ($section_sql->fetch()) {
      array_push($section_ids, $section_id);
    }

    $section_sql->close();
    return $section_ids;
  }

  function getSessionIDs($connection, $section_id) {
    //Finds session id's that match specified parameters
    $session_sql = $connection->prepare("SELECT id FROM Sessions WHERE section_id = ?");
    $session_sql->bind_param('s', $section_id);
    $session_sql->execute();

    //Reads results
    $session_ids = array();
    $session_sql->bind_result($session_id);
    while ($session_sql->fetch()) {
      array_push($session_ids, $session_id);
    }

    $session_sql->close();
    return $session_ids;
  }

  function getSection($connection, $section_id) {
    $section_info = array();

    //Finds section info from id
    $section_sql = $connection->prepare("SELECT type_id, crn, section FROM Sections WHERE id = ?");
    $section_sql->bind_param('s', $section_id);
    $section_sql->execute();
    $section_sql->bind_result($section_type_id, $section_crn, $section_section);
    $section_sql->fetch();
    $section_sql->close();

    //Collects section info in array
    $section_info['crn'] = $section_crn;
    $section_info['section'] = $section_section;
    $section_info['instructors'] = getInstructors($connection, $section_id);
    $section_info['type'] = getCourseType($connection, $section_type_id);
    $section_availability = getAvailability($connection, $section_id);
    $section_info['capacity'] = $section_availability[0];
    $section_info['actual'] = $section_availability[1];
    $section_info['remaining'] = $section_availability[2];

    return $section_info;
  }

  function getSession($connection, $session_id) {
    $session_info = array();

    //Finds session info from id
    $session_sql = $connection->prepare("SELECT week, day, location FROM Sessions WHERE id = ?");
    $session_sql->bind_param('s', $session_id);
    $session_sql->execute();
    $session_sql->bind_result($session_week, $session_day, $session_location);
    $session_sql->fetch();
    $session_sql->close();

    //Collects session info in array
    $session_info['week'] = $session_week;
    $session_info['day'] = $session_day;
    $session_info['location'] = $session_location;
    $session_period = getPeriod($connection, $session_id);
    $session_info['start_date'] = $session_period[0];
    $session_info['finish_date'] = $session_period[1];
    $session_info['start_time'] = $session_period[2];
    $session_info['finish_time'] = $session_period[3];

    return $session_info;
  }

  function getPeriod($connection, $session_id) {
    //Finds timeslot for session
    $period_sql = $connection->prepare("SELECT start_date, finish_date, start_time, ".
      "finish_time FROM Session_Period WHERE session_id = ?");
    $period_sql->bind_param('s', $session_id);
    $period_sql->execute();

    //Reads results
    $period_sql->bind_result($start_date, $finish_date, $start_time, $finish_time);
    $period_sql->fetch();

    $period_sql->close();
    return array($start_date, $finish_date, $start_time, $finish_time);
  }

  function getInstructors($connection, $section_id) {
    //Finds instructors for the section
    $instr_sql = $connection->prepare("SELECT name, primary_instructor FROM Instructors ".
      "LEFT JOIN Section_Instructors ON Section_Instructors.instructor_id = Instructors.id ".
      "WHERE section_id = ?");
    $instr_sql->bind_param('s', $section_id);
    $instr_sql->execute();

    //Stores instructors in array
    $section_instructors = array();
    $instr_sql->bind_result($section_instructor, $section_instructor_primary);
    while ($instr_sql->fetch()) {
      array_push($section_instructors, array($section_instructor, $section_instructor_primary));
    }

    $instr_sql->close();
    return $section_instructors;
  }

  function getCourseType($connection, $type_id) {
    //Finds type name from id
    $type_sql = $connection->prepare("SELECT type FROM Types WHERE id = ?");
    $type_sql->bind_param('s', $type_id);
    $type_sql->execute();

    //Reads results
    $type_sql->bind_result($section_type);
    $type_sql->fetch();

    $type_sql->close();
    return $section_type;
  }

  function getAvailability($connection, $section_id) {
    //Finds availability from sectionid
    $avail_sql = $connection->prepare("SELECT capacity, actual, remaining FROM Section_Availability WHERE section_id = ?");
    $avail_sql->bind_param('s', $section_id);
    $avail_sql->execute();

    //Reads results
    $avail_sql->bind_result($section_cap, $section_act, $section_rem);
    $avail_sql->fetch();

    $avail_sql->close();
    return array($section_cap, $section_act, $section_rem);
  }

  function getSemesters($connection) {
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

    return $semesters;
  }

  function getCourseCodes($connection, $year, $term, $key_word) {
    //Queries for course codes that match specified semester and query term
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

    $course_sql->close();
    return $course_codes;
  }

  function getCourseInfo($connection, $subject, $code, $year, $term) {
    //Checks if course exists
    $course_id = getCourseID($connection, $subject, $code, $year, $term);
    if(empty($course_id)) {
      return '';
    } else {
      //Finds course info
      $course_info = getCourse($connection, $course_id);
      $course_info['subject'] = $subject;
      $course_info['code'] = $code;
      $course_info['year'] = $year;
      $course_info['term'] = $term;

      //Finds all sections for specified course
      $section_ids = getSectionIDs($connection, $course_id);

      //Finds section info and sessions for sections
      $sections = array();
      foreach ($section_ids as $section_id) {
        $section = getSection($connection, $section_id);

        //Finds session info for section
        $sessions = getSessionInfo($connection, $section_id);
        $section['sessions'] = $sessions;

        array_push($sections, $section);
      }
      $course_info['sections'] = $sections;
    }
    return $course_info;
  }

  function getSessionInfo($connection, $section_id) {
    //Finds all sessions for section
    $session_ids = getSessionIDs($connection, $section_id);

    //Finds all session info for sessions
    $sessions = array();
    foreach ($session_ids as $session_id) {
      $session = getSession($connection, $session_id);
      array_push($sessions, $session);
    }

    return $sessions;
  }
?>
