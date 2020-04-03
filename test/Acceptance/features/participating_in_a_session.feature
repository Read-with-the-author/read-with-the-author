Feature:

  Once a session was planned, and a member has been granted access to the book club, the member can register as a
  participant to upcoming sessions.

  Background:
    Given today is "2020-03-26"
    And a member who has been granted access

  Scenario: An new session shows up in the list of upcoming sessions
    When the administrator schedules a session for "2020-04-01 20:00" with description "Chapter 1"
    Then this session should show up in the list of upcoming sessions for the active member with the following details:
      | Date | Wednesday, April 1st |
      | Time | 20:00                |

  Scenario: The session's date is in the past
    Given today is "2020-03-26"
    And the administrator has scheduled a session for "2020-01-01 20:00"
    Then this session should not show up in the list of upcoming sessions

  Scenario: The member's time zone is different from the author's time zone
    Given the author's time zone is "Europe/Amsterdam"
    And the member's time zone is "America/New_York"
    When the administrator schedules a session for "2020-04-01 20:00" with description "Chapter 1"
    Then this session should show up in the list of upcoming sessions for the active member with the following details:
      | Date | Wednesday, April 1st |
      | Time | 14:00                |

  Scenario: A member registers themselves as a participant of an upcoming session
    Given an upcoming session
    When the member registers themselves as a participant of the session
    Then the list of upcoming sessions should indicate that they have been registered as a participant
    And they should receive an email confirming their registration

  Scenario: A member registers themselves as a participant of an upcoming session
    Given an upcoming session
    And the member has registered themselves as a participant of the session
    When they cancel their attendance
    Then the list of upcoming sessions should indicate that they have not been registered as a participant

  Scenario: An administrator has not provided the URL for the call yet
    Given an upcoming session
    And the member has registered themselves as a participant of the session
    When the member requests the call URL for this session
    Then it fails because it has not been determined yet

  Scenario: An administrator has provided the URL for the call, but the member is not registered as a participant
    Given an upcoming session
    And the administrator has set the call URL to "https://whereby.com/matthiasnoback"
    When the member requests the call URL for this session
    Then it fails because this member is not registered as an attendee

  Scenario: An administrator sets the call URL and the member has been registered as a participant
    Given an upcoming session
    And the member has registered themselves as a participant of the session
    And the administrator has set the call URL to "https://whereby.com/matthiasnoback"
    When the member requests the call URL for this session it will be "https://whereby.com/matthiasnoback"
