Feature:

  Background:
    Given today is "2020-03-26"

  Scenario: An new session shows up in the list of upcoming sessions
    When the administrator schedules a session for "2020-04-01 20:00" with description "Chapter 1"
    Then this session should show up in the list of upcoming sessions for administrators:
      | Date | Wednesday, April 1st |
      | Time | 20:00                |
