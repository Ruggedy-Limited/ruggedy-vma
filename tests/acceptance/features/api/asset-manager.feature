Feature: As an administrator or user with the correct access control
  I want the ability to add new asset into workspaces through the API
  I want to add, edit, delete and suppress assets
  I want assets to have a relationship between vulnerabilities, scanners and exploit data
  I want to import assets from various sources, including nmap, Nexpose, Nessus, etc.
  I want to be able to add custom "tags" to assets for reporting and grouping.

  Assets:
  * An asset is one logical entry that represents a server/application/service, etc.
  * An asset will have one or two primary identifiers, for example IP Address or / and DNS name.
  * Assets are stored and managed under Workspaces
  * Assets should also be available in the "master assets" view - A view that contains a list of all assets under an account.

  ##
  # Create an Asset by importing scanner results
  ##
  Scenario: Add an asset to one of my Workspaces by importing an nmap scan result
    Given that I want to make a new "Asset"
    And that its "name" is "Web Server"
    And that its "nmap_file" is "test_files/nmap.txt"
    When I request "/api/asset/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "id" property
    And the type of the "id" property is integer
    And the response has a "name" property
    And the type of the "name" property is string
    And the "name" property equals "Web Server"
    And the response has a "ip_address" property
    And the type of the "ip_address" property is string
    And the "ip_address" property equals "66.29.210.204"
    And the type of the "open_ports" property is array
    And the "open_ports" array property has a "80" value
    And the "open_ports" array property has a "443" value
    # Add more properties here when the schema is more fleshed out

  Scenario: Add an asset to one of my Workspaces by importing an Nessus scan result
    Given that I want to make a new "Asset"
    And that its "name" is "Web Server"
    And that its "nmap_file" is "test_files/nesus.xml"
    When I request "/api/asset/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "id" property
    And the type of the "id" property is integer
    And the response has a "name" property
    And the type of the "name" property is string
    And the "name" property equals "Web Server"
    And the response has a "ip_address" property
    And the type of the "ip_address" property is string
    And the "ip_address" property equals "66.29.210.204"
    And the response has a "hostname" property
    And the type of the "hostname" property is string
    And the "hostname" property equals "www.ruggedy.io"
    # Add more properties here when the schema is more fleshed out

  Scenario: Add an asset to one of my Workspaces by importing a Burp scan result
    Given that I want to make a new "Asset"
    And that its "name" is "Web Server"
    And that its "nmap_file" is "test_files/burp.xml"
    When I request "/api/asset/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "id" property
    And the type of the "id" property is integer
    And the response has a "name" property
    And the type of the "name" property is string
    And the "name" property equals "Web Server"
    # Add more properties here when the schema is more fleshed out

  Scenario: Add an asset to one of my Workspaces by importing a ZAP Proxy scan result
    Given that I want to make a new "Asset"
    And that its "name" is "Web Server"
    And that its "nmap_file" is "test_files/burp.xml"
    When I request "/api/asset/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "id" property
    And the type of the "id" property is integer
    And the response has a "name" property
    And the type of the "name" property is string
    And the "name" property equals "Web Server"
    # Add more properties here when the schema is more fleshed out

  Scenario: Add an asset to one of my Workspaces by importing a Nexpose scan result
    Given that I want to make a new "Asset"
    And that its "name" is "Web Server"
    And that its "nmap_file" is "test_files/nexpose.xml"
    When I request "/api/asset/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "id" property
    And the type of the "id" property is integer
    And the response has a "name" property
    And the type of the "name" property is string
    And the "name" property equals "Web Server"
    # Add more properties here when the schema is more fleshed out

  Scenario: Add an asset to one of my Workspaces by importing a OpenVAS scan result
    Given that I want to make a new "Asset"
    And that its "name" is "Web Server"
    And that its "nmap_file" is "test_files/openvas.xml"
    When I request "/api/asset/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "id" property
    And the type of the "id" property is integer
    And the response has a "name" property
    And the type of the "name" property is string
    And the "name" property equals "Web Server"
    # Add more properties here when the schema is more fleshed out

  Scenario: Add an asset to one of my Workspaces by importing a W3AF scan result
    Given that I want to make a new "Asset"
    And that its "name" is "Web Server"
    And that its "nmap_file" is "test_files/w3af.xml"
    When I request "/api/asset/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "id" property
    And the type of the "id" property is integer
    And the response has a "name" property
    And the type of the "name" property is string
    And the "name" property equals "Web Server"
    # Add more properties here when the schema is more fleshed out

  Scenario: Add an asset to one of my Workspaces by importing a Arachne scan result
    Given that I want to make a new "Asset"
    And that its "name" is "Web Server"
    And that its "nmap_file" is "test_files/arachne.xml"
    When I request "/api/asset/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "id" property
    And the type of the "id" property is integer
    And the response has a "name" property
    And the type of the "name" property is string
    And the "name" property equals "Web Server"
    # Add more properties here when the schema is more fleshed out

  Scenario: Add an asset to one of my Workspaces by importing a NetSparker scan result
    Given that I want to make a new "Asset"
    And that its "name" is "Web Server"
    And that its "nmap_file" is "test_files/netsparker.xml"
    When I request "/api/asset/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "id" property
    And the type of the "id" property is integer
    And the response has a "name" property
    And the type of the "name" property is string
    And the "name" property equals "Web Server"
    # Add more properties here when the schema is more fleshed out

  Scenario: Add an asset to one of my Workspaces by importing a Nikto scan result
    Given that I want to make a new "Asset"
    And that its "name" is "Web Server"
    And that its "nmap_file" is "test_files/nikto.xml"
    When I request "/api/asset/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "id" property
    And the type of the "id" property is integer
    And the response has a "name" property
    And the type of the "name" property is string
    And the "name" property equals "Web Server"
    # Add more properties here when the schema is more fleshed out

  Scenario: Add an asset to one of my Workspaces by importing a Burp scan result, but provide a non-existant Workspace ID
    Given that I want to make a new "Asset"
    And that its "name" is "Web Server"
    And that its "nmap_file" is "test_files/burp.xml"
    When I request "/api/asset/10"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, we could not import your scan result. That Workspace does not exist."
    # Add more properties here when the schema is more fleshed out

  ##
  # Add scanner results to existing asset
  ##
  Scenario: Import an nmap scan result for an existing asset
    Given that I want to update my "Asset"
    And that its "nmap_file" is "test_files/nmap.txt"
    When I request "/api/asset/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "1"
    And the response has a "name" property
    And the type of the "name" property is string
    And the "name" property equals "Web Server"
    And the response has a "ip_address" property
    And the type of the "ip_address" property is string
    And the "ip_address" property equals "66.29.210.204"
    And the type of the "open_ports" property is array
    And the "open_ports" array property has a "80" value
    And the "open_ports" array property has a "443" value
    # Add more properties here when the schema is more fleshed out

  Scenario: Import a Nessus scan result for an existing Asset
    Given that I want to update my "Asset"
    And that its "nmap_file" is "test_files/nesus.xml"
    When I request "/api/asset/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "1"
    And the response has a "name" property
    And the type of the "name" property is string
    And the "name" property equals "Web Server"
    And the response has a "ip_address" property
    And the type of the "ip_address" property is string
    And the "ip_address" property equals "66.29.210.204"
    And the response has a "hostname" property
    And the type of the "hostname" property is string
    And the "hostname" property equals "www.ruggedy.io"
    # Add more properties here when the schema is more fleshed out

  Scenario: Import a Burp scan result for an existing Asset
    Given that I want to update my "Asset"
    And that its "nmap_file" is "test_files/burp.xml"
    When I request "/api/asset/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "1"
    And the response has a "name" property
    And the type of the "name" property is string
    And the "name" property equals "Web Server"
    # Add more properties here when the schema is more fleshed out

  Scenario: Import a ZAP Proxy scan result for an existing Asset
    Given that I want to update my "Asset"
    And that its "nmap_file" is "test_files/burp.xml"
    When I request "/api/asset/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "1"
    And the response has a "name" property
    And the type of the "name" property is string
    And the "name" property equals "Web Server"
    # Add more properties here when the schema is more fleshed out

  Scenario: Import a Nexpose scan result for an existing Asset
    Given that I want to update my "Asset"
    And that its "nmap_file" is "test_files/nexpose.xml"
    When I request "/api/asset/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "1"
    And the response has a "name" property
    And the type of the "name" property is string
    And the "name" property equals "Web Server"
    # Add more properties here when the schema is more fleshed out

  Scenario: Import a OpenVAS scan result for an existing Asset
    Given that I want to update my "Asset"
    And that its "nmap_file" is "test_files/openvas.xml"
    When I request "/api/asset/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "1"
    And the response has a "name" property
    And the type of the "name" property is string
    And the "name" property equals "Web Server"
    # Add more properties here when the schema is more fleshed out

  Scenario: Importing a W3AF scan result for an existing Asset
    Given that I want to update my "Asset"
    And that its "nmap_file" is "test_files/w3af.xml"
    When I request "/api/asset/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "1"
    And the response has a "name" property
    And the type of the "name" property is string
    And the "name" property equals "Web Server"
    # Add more properties here when the schema is more fleshed out

  Scenario: Importing a Arachne scan result for an existing Asset
    Given that I want to update my "Asset"
    And that its "nmap_file" is "test_files/arachne.xml"
    When I request "/api/asset/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "1"
    And the response has a "name" property
    And the type of the "name" property is string
    And the "name" property equals "Web Server"
    # Add more properties here when the schema is more fleshed out

  Scenario: Importing a NetSparker scan result for an existing Asset
    Given that I want to update my "Asset"
    And that its "nmap_file" is "test_files/netsparker.xml"
    When I request "/api/asset/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "1"
    And the response has a "name" property
    And the type of the "name" property is string
    And the "name" property equals "Web Server"
    # Add more properties here when the schema is more fleshed out

  Scenario: Importing a Nikto scan result for an existing Asset
    Given that I want to update my "Asset"
    And that its "nmap_file" is "test_files/nikto.xml"
    When I request "/api/asset/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "1"
    And the response has a "name" property
    And the type of the "name" property is string
    And the "name" property equals "Web Server"
    # Add more properties here when the schema is more fleshed out

  Scenario: Importing a Burp scan result, but provide a non-existant Asset ID
    Given that I want to update my "Asset"
    And that its "nmap_file" is "test_files/burp.xml"
    When I request "/api/asset/10"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, we could not import your scan result. That Asset does not exist."
    # Add more properties here when the schema is more fleshed out
  
  Scenario: Edit the name of one of my Assets
    Given that I want to update my "Asset"
    And that I want to change it's "name" to "Name Changed Asset"
    When I request "/api/asset/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "1"
    And the response has a "name" property
    And the type of the "name" property is string
    And the "name" property equals "Name Changed Asset"

  Scenario: I attempt to edit the name of one of my Assets, but provide an invalid Asset ID
    Given that I want to update my "Asset"
    And that I want to change it's "name" to "Name Changed Asset"
    When I request "/api/asset/10"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, we could not update that Asset. The Asset does not exist."

  ##
  # Add a single tag and multiple tags to an Asset
  ##

  ##
  # Delete Assets
  ##

  ##
  # Surpress Assets and specific events for a specific Asset
  ##

  ##
  # Get all the Assets from all of my Projects and Workspaces (Master Assets View)
  ##

  ##
  # Get all Assets from a specific Project
  ##

  ##
  # Get all Assets from a specific Workspace
  ##

  ##
  # Get a list of scanners used on a particular Asset
  ##

  ##
  # Get all the Vulnerabilities related to an Asset
  ##

  ##
  # Get all the open ports related to an Asset
  ##