Feature:

  People can join the book club if they have bought a copy of the e-book on Leanpub. When they sign up to join the club,
  they have to provide their Leanpub invoice ID (which they can find on their invoice).

  Scenario: The provided invoice ID matches a purchase
    Given someone has bought a copy of the book and the invoice ID was "jP6LfQ3UkfOvZTLZLNfDfg"
    When they request access to the club providing the same invoice ID
    Then they should be granted access to the club

  Scenario: The provided invoice ID does not match a purchase
    When someone requests access to the club providing an invoice ID that does not match an actual purchase
    Then they should not be granted access to the club

  Scenario: The provided invoice ID has been used before
    Given someone has been granted access to the club
    When someone else requests access providing the same invoice ID
    Then they should not be granted access to the club

  Scenario: The new member gets an access token by email
    When someone requests access to the club providing the correct invoice ID
    Then they should receive an email with an access token for their dashboard page

  Scenario: An existing member requests another access token
    Given someone has been granted access to the club
    When they request a new access token
    Then they should receive an email with an access token for their dashboard page
