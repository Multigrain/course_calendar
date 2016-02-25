<html lang="en">
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
    <script>
      $(function() {
        $("#Courses").autocomplete({
          source: function(request, response) {
            $.ajax({
              url: "course_info.php",
              type: 'GET',
              dataType: "json",
              data: {
                'query_type' : 'course_code',
                'year' : 2016,
                'term' : '01',
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
    </script>
  </head>
  <body>
    <input id="Courses">
  </body>
</html>
