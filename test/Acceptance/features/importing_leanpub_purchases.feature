Feature:

  In order to validate members, we collect the invoice ID of their Leanpub purchase. We first need to collect valid
  invoice IDs and instead of asking Leanpub synchronously, we make sure we regularly import purchases using their API.

  Scenario: Purchases are imported for the first time

    Given Leanpub returns to us the following list of individual purchases:
      | Invoice ID             | Purchase date            |
      | jP6LfQ3UkfOvZTLZLNfDfg | 2019-04-15T06:36:54.000Z |
      | 6gbXPEDMOEMKCNwOykPvpg | 2019-04-15T03:06:11.000Z |
    When the system imports all purchases
    Then the imported invoice IDs should be:
      | Invoice ID             |
      | jP6LfQ3UkfOvZTLZLNfDfg |
      | 6gbXPEDMOEMKCNwOykPvpg |

  Scenario: Purchases are imported for the second time

    Given Leanpub returns to us the following list of individual purchases:
      | Invoice ID             | Purchase date            |
      | jP6LfQ3UkfOvZTLZLNfDfg | 2019-04-15T06:36:54.000Z |
      | 6gbXPEDMOEMKCNwOykPvpg | 2019-04-15T03:06:11.000Z |
    And the system has imported all purchases
    When the system imports all purchases again
    Then no purchases should have been imported
