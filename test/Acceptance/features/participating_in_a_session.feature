Feature:

  Once a session was planned, and a member has been granted access to the book club, the member can register as a
  participant to upcoming sessions.

  Background:
    Given today is "2020-03-26"
    And a member who has been granted access

  Scenario: An new session shows up in the list of upcoming sessions
    When the administrator schedules a session for "2020-04-01 20:00" with description "Chapter 1"
    Then this session should show up in the list of upcoming sessions for the active member

  Scenario: A member registers themselves as a participant of an upcoming session
    Given an upcoming session
    When the member registers themselves as a participant of the session
    Then the list of upcoming sessions should indicate that they have been registered as a participant
