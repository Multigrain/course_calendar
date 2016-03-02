var CourseCalendar = {
  setup: function() {
    CourseCalendar.addCalendar();
    CourseCalendar.selectTermListener();
    CourseCalendar.changeCourseListener();
    CourseCalendar.selectCourseListener();
  },
  addCalendar: function() {
    $('#calendar').fullCalendar({
      //Calendar settings
      weekends: false,
      defaultView: 'agendaWeek',
      allDaySlot: false,
      minTime: '08:00:00',
      maxTime: '22:00:00',
      defaultDate: '2007-01-01',
      columnFormat: {week: 'dddd'},
      contentHeight: 'auto',
      slotLabelFormat: 'h(:mm) a',
      customButtons: {
        myCustomButton: {
            text: 'Switch Week',
            click: function() {
                CourseCalendar.switchWeek();
            }
        }
      },
      header: {
        left: 'myCustomButton',
        center: 'title',
        right: 'Test'
      },
      titleFormat: '[Week 1]'
    });

    //Makes calendar times span rows
    $('.fc-time:odd').remove();
    $('.fc-time').attr('rowspan', 2);

    //Sets classes for alternating row colours
    $('.fc-slats tr:nth-child(4n)').addClass("odd-row").prev().addClass("odd-row");

  },
  switchWeek: function() {
    //Switches week and updates labels
    if($('.fc-center').children().text() == 'Week 1') {
      $('#calendar').fullCalendar('next');
      $('.fc-center').children().text('Week 2');
    } else {
      $('#calendar').fullCalendar('prev');
      $('.fc-center').children().text('Week 1');
    }
    $('.fc-time:odd').remove();
    $('.fc-time').attr('rowspan', 2);

    //Sets classes for alternating row colours
    $('.fc-slats tr:nth-child(4n)').addClass("odd-row").prev().addClass("odd-row");
  },
  selectTermListener: function() {
    $('#select-term').on('click', function(selected_term) {
      //Sets term
      var term_key = {'01' : 'Winter', '05' : 'Spring/Summer', '09' : 'Fall'};
      var selected_term = $("#input-term").val();
      CourseInfo.year = selected_term.substring(0, 4);
      CourseInfo.term = selected_term.substring(4, 6);
      $('.fc-right').text(term_key[CourseInfo.term] + " " + CourseInfo.year);

      //Resets divs visibility
      document.getElementById("calendar-selector").style.visibility = "visible";
      document.getElementById("courses-container").style.visibility = "hidden";

      //Clears calendar

      //Clears courses div
      $('.selected-courses').remove();

      //Clears local data
      CourseInfo.courses = [];

    });
  },
  changeCourseListener: function() {
    $(function() {
      $("#input-coursecode").autocomplete({
        source: function(request, response) {
          $.ajax({
            url: "course_info.php",
            type: 'GET',
            dataType: "json",
            data: {
              'query_type' : 'course_code',
              'year' : CourseInfo.year,
              'term' : CourseInfo.term,
              'keyword' : request.term
            },
            success: function (data) {
              response(data)
            }
          });
        },
        minLength: 2
      });
    });
  },
  selectCourseListener: function() {
    $('#select-course').on('click', function() {
      //Checks if course has already been added or max number of courses reached
      if (CourseInfo.courses.length > 5) {
          $('#course-prompt').html("Only allowed to select up to 6 courses");
      } else {
        //Parses course code for request
        var course_code = $('#input-coursecode').val().match(/^([a-zA-Z]{4}) ?(.*)$/);
        if(course_code) {
          //Checks if course already selected
          for (var i = 0; i < CourseInfo.courses.length; i++) {
            if (CourseInfo.courses[i].subject === course_code[1] && CourseInfo.courses[i].code === course_code[2]) {
              $('#course-prompt').html("Course already selected");
              return;
            }
          }

          $.ajax({
            url: 'course_info.php',
            type: 'GET',
            data: {
              'query_type' : 'course_info',
              'year' :CourseInfo.year,
              'term' :CourseInfo.term,
              'subject' : course_code[1],
              'code' : course_code[2]
            },
            success: function (response) {
              //Checks if course was found
              if (response.title != null) {
                //Adds course info as variable to be accessed later
                CourseInfo.courses.push(response);

                //Generates random color for events
                var current_colour = CourseCalendar.generateColour();
                CourseInfo.courses[CourseInfo.courses.length - 1].colour = current_colour;

                //Splits sessions into appropriate drop downs
                var sections = {}
                for (var i = 0; i < response.sections.length; i++) {
                  var current_type = response.sections[i].type;
                  if(!(sections[current_type])) {
                    sections[current_type] = [];
                  }
                  sections[current_type].push(response.sections[i]);
                }

                //Creates new course div
                var first = true;
                var div_colour =  current_colour.slice(0, 3) + "a" + current_colour.slice(3, -1) + ", 0.5)";
                var page_div = `<div style = "background-color:${div_colour}" class = "selected-courses" subj = "${response.subject}" code = "${response.code}">
                  <header><h3>${response.title}</h3></header>`;

                for (var section in sections) {
                  if(!first) {
                    page_div = page_div + "<br>"
                  } else {
                    first = false;
                  }
                  page_div = page_div + `${section}: <select class="course_sections"
                    course-type = "${section}"><option selected disabled hidden value=''>Select Section</option>`;

                  for (var i = 0; i < sections[section].length; i++) {
                    page_div = page_div + `<option value=${sections[section][i].crn}>Section ${sections[section][i].section} - CRN ${sections[section][i].crn}</option>`;
                  }

                  page_div = page_div + "</select>";
                }

                //Adds div to page

                $('#calendar-courses').append(page_div);
                document.getElementById("courses-container").style.visibility = "visible";
                $('#course-prompt').html("");

              } else {
                $('#course-prompt').html('Course not found');
              }
            }
          });
        } else {
          $('#course-prompt').html('Invalid course code');
        }
      }
    })
  },
  generateColour: function() {
    //Generates random colour
    var event_r = 0;
    var event_g = 0;
    var event_b = 0;

    for (;;) {
      event_r = Math.round(Math.random() * 255);
      event_g = Math.round(Math.random() * 255);
      event_b = Math.round(Math.random() * 255);

      //Determines if visible on white font by converting color to grayscale
      if(0.21*event_r + 0.72*event_g + 0.07*event_b < 60) {
        break;
      }
    }

    //Formats rgb before storing
    var course_rgb = `rgb(${event_r},${event_g},${event_b})`;

    return course_rgb;
  }
}
