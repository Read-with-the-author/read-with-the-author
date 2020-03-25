Feature:

  People can join the book club if they have bought a copy of the e-book on Leanpub. When they sign up to join the club,
  they have to provide their Leanpub invoice ID (which they can find on their invoice).

  Scenario: The provided invoice ID matches a purchase
    Given someone has bought a copy of the book
    When they request access to the club providing the correct invoice ID
    Then they should be granted access to the club
