var CourseCalendar = {
  setup: function() {
    CourseCalendar.addCalendar();
  },
  addCalendar: function() {
    $('#calendar').fullCalendar({
      // put your options and callbacks here
      weekends: false,
      defaultView: 'agendaWeek',
      header: false,
      allDaySlot: false,
      minTime: "08:00:00",
      maxTime: "22:00:00",
      defaultDate: '2007-01-01',
      columnFormat: {
            week: 'dddd'
      },
      aspectRatio: 1.435
    })
  }
}
