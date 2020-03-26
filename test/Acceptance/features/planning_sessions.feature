Feature:

  Members of the book club can attend sessions where we discuss a part of the book.

  Scenario:
    Given today is "2020-03-26"
    When the administrator schedules a session for "2020-04-01 20:00" with description "Chapter 1"
    Then this session should show up in the list of upcoming sessions
