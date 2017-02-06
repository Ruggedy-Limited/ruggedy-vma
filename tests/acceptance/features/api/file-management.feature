Feature: As an account or team owner
  I want to be able to view assets related to a file

  As an administrator
  I want the ability to delete files and their associated data from my workspace.

  Background:
    Given the following existing Users:
      | id | name           | email                      | password                                                     | remember_token | photo_url    | uses_two_factor_auth | authy_id | country_code | phone       | two_factor_reset_code | current_team_id | stripe_id | current_billing_plan | card_brand | card_last_four | card_country | billing_address | billing_address_line_2 | billing_city | billing_state | billing_zip | billing_country | vat_id | extra_billing_information | trial_ends_at       | last_read_announcements_at | created_at          | updated_at          |
      | 1  | John Smith     | johnsmith@dispostable.com  | $2y$10$IPgIlPVo/NW6fQMx0gJUyesYjV1N4LwC1fH2rj94s0gq.xDjMisNm | NULL           | NULL         | 0                    | NULL     | ZAR          | 0716852996  | NULL                  | 1               | NULL      | NULL                 | NULL       | NULL           | NULL         | NULL            | NULL                   | NULL         | NULL          | NULL        | NULL            | NULL   | NULL                      | 2016-05-19 14:39:01 | 2016-05-09 14:39:01        | 2016-05-09 14:39:01 | 2016-05-09 14:39:02 |
      | 2  | Greg Symons    | gregsymons@dispostable.com | $2y$10$0WLCM1EUuJce.zSlS1N4h.XRn7u8uDbyxslTkFOI0ka0fxSIXmjhC | NULL           | /myphoto.jpg | 0                    | NULL     | NZ           | 06134582354 | NULL                  | 2               | NULL      | NULL                 | NULL       | NULL           | NULL         | NULL            | NULL                   | NULL         | NULL          | NULL        | NULL            | NULL   | NULL                      | 2016-05-20 11:51:29 | 2016-05-10 11:51:43        | 2016-05-10 11:51:29 | 2016-05-10 11:51:43 |
      | 3  | Another Person | another@dispostable.com    | $2y$10$0WLCM1EUuJce.zSlS1N4h.XRn7u8uDbyxslTkFOI0ka0fxSIXmjhC | NULL           | /aphoto.jpg  | 0                    | NULL     | AUS          | 08134582354 | NULL                  | 2               | NULL      | NULL                 | NULL       | NULL           | NULL         | NULL            | NULL                   | NULL         | NULL          | NULL        | NULL            | NULL   | NULL                      | 2016-05-20 11:51:29 | 2016-05-10 11:51:43        | 2016-05-10 11:51:29 | 2016-05-10 11:51:43 |
    And the following existing Teams:
      | id | owner_id | name        | photo_url | stripe_id | current_billing_plan | card_brand | card_last_four | card_country | billing_address | billing_address_line_2 | billing_city | billing_state | billing_zip | billing_country | vat_id | extra_billing_information | trial_ends_at       | created_at          | updated_at          |
      | 1  | 1        | Johns Team  | NULL      | NULL      | NULL                 | NULL       | NULL           | NULL         | NULL            | NULL                   | NULL         | NULL          | NULL        | NULL            | NULL   | NULL                      | 2016-05-09 14:39:01 | 2016-05-09 14:39:01 | 2016-05-09 14:39:01 |
      | 2  | 3        | Jack's Team | NULL      | NULL      | NULL                 | NULL       | NULL           | NULL         | NULL            | NULL                   | NULL         | NULL          | NULL        | NULL            | NULL   | NULL                      | 2016-05-11 11:39:01 | 2016-05-11 11:39:01 | 2016-05-09 14:39:01 |
    And the following Users in Team 1:
      | id | role   |
      | 1  | owner  |
      | 2  | member |
    And the following existing Workspaces:
      | id | name                | user_id  | created_at          | updated_at          |
      | 1  | John's Workspace    | 1        | 2016-05-13 11:06:00 | 2016-05-13 11:06:00 |
      | 2  | Someone's Workspace | 2        | 2016-05-13 10:06:00 | 2016-05-13 10:06:00 |
      | 3  | Another Workspace   | 3        | 2016-05-13 09:06:00 | 2016-05-13 09:06:00 |
    And the following existing Files:
      | id | path                                              | format | size    | user_id | workspace_id | scanner_app_id | processed | deleted | created_at          | updated_at          |
      | 1  | scans/xml/nmap/1/nmap-adv-multiple-node-dns.xml   | xml    | 18646   | 1       | 1            | 1              | 1         | 0       | 2016-10-10 06:51:18 | 2016-11-14 15:00:19 |
      | 2  | scans/xml/burp/1/burp-multiple-auth-dns+ip.xml    | xml    | 4660178 | 1       | 1            | 2              | 1         | 0       | 2016-10-10 06:51:35 | 2016-11-14 15:00:19 |
      | 3  | scans/xml/nexpose/1/full-multiple-dns.xml         | xml    | 3662061 | 1       | 1            | 3              | 1         | 0       | 2016-10-10 06:51:53 | 2016-11-14 15:00:19 |
      | 4  | scans/xml/netsparker/1/single-dns.xml             | xml    | 568818  | 1       | 1            | 4              | 1         | 0       | 2016-10-17 19:25:33 | 2016-11-14 15:00:19 |
      | 5  | scans/xml/nessus/1/full-multiple-dns.nessus       | xml    | 1841174 | 2       | 2            | 5              | 1         | 0       | 2016-10-24 07:26:59 | 2016-11-14 16:58:45 |
      | 6  | scans/xml/nessus/1/full-audit-multiple-dns.nessus | xml    | 2664096 | 1       | 1            | 5              | 1         | 0       | 2016-11-07 07:22:00 | 2016-11-14 15:00:19 |
      | 7  | scans/xml/nessus/1/full-audit.nessus              | xml    | 2664096 | 3       | 3            | 5              | 1         | 0       | 2016-11-07 07:22:00 | 2016-11-14 15:00:19 |
    And the following existing ScannerApps:
      | id | name       | description                      | created_at          | updated_at          |
      | 1  | nmap       | NMAP Port Scanner Utility        | 2016-07-28 23:17:04 | 2016-07-28 23:17:04 |
      | 2  | burp       | Burp Vulnerability Scanner       | 2016-07-28 23:17:04 | 2016-07-28 23:17:04 |
      | 3  | netsparker | Netsparker Vulnerability Scanner | 2016-07-28 23:17:04 | 2016-07-28 23:17:04 |
      | 4  | nexpose    | Nexpose Vulnerability Scanner    | 2016-07-28 23:17:04 | 2016-07-28 23:17:04 |
      | 5  | nessus     | Nessus Vulnerability Scanner     | 2016-07-28 23:17:04 | 2016-07-28 23:17:04 |
    And the following existing Assets:
      | id | name                      | cpe                                                                 | vendor    | ip_address_v4 | ip_address_v6                           | hostname                  | mac_address       | os_version | netbios | file_id | user_id | created_at          | updated_at          |
      | 1  | homenetwork.home.co.za    | cpe:/o:ubuntu:ubuntu_linux:9.10                                     | Ubuntu    | 192.168.0.10  | FE80:0000:0000:0000:0202:B3FF:FE1E:8329 | homenetwork.home.co.za    | D0:E1:40:8C:63:6A | 9.10       | NULL    | 1       | 1       | 2016-06-20 09:00:00 | 2016-06-20 09:00:00 |
      | 2  | Windows Server 2003       | cpe:2.3:o:microsoft:windows_2003_server:*:gold:enterprise:*:*:*:*:* | Microsoft | 192.168.0.12  | fd03:10d3:bb1c::/48                     | NULL                      | NULL              | 5.2.3790   | NULL    | 1       | 1       | 2016-06-20 09:02:23 | 2016-06-20 09:02:23 |
      | 3  | 192.168.0.24              | NULL                                                                | NULL      | 192.168.0.24  | NULL                                    | NULL                      | NULL              | NULL       | NULL    | 1       | 1       | 2016-06-20 09:05:31 | 2016-06-20 09:05:31 |
      | 4  | webapp.test               | cpe:2.3:a:nginx:nginx:1.9.8:*:*:*:*:*:*:*                           | NULL      | 192.168.0.38  | NULL                                    | webapp.test               | NULL              | NULL       | NULL    | 2       | 1       | 2016-06-20 09:05:38 | 2016-06-20 09:05:38 |
      | 5  | ubuntu2.homenetwork.co.za | cpe:/o:ubuntu:ubuntu_linux:12.10                                    | Ubuntu    | NULL          | NULL                                    | ubuntu2.homenetwork.co.za | NULL              | 12.10      | NULL    | 3       | 1       | 2016-06-20 09:06:00 | 2016-06-20 09:06:00 |
      | 6  | fde3:970e:b33d::/48       | cpe:2.3:o:microsoft:windows_server_2008:*:*:x64:*:*:*:*:*           | Microsoft | NULL          | fde3:970e:b33d::/48                     | NULL                      | NULL              | 6.0.6001   | NULL    | 4       | 1       | 2016-06-20 09:07:23 | 2016-06-20 09:07:23 |
      | 7  | 192.168.1.24              | NULL                                                                | NULL      | 192.168.1.24  | NULL                                    | NULL                      | NULL              | NULL       | NULL    | 5       | 2       | 2016-06-20 09:08:31 | 2016-06-20 09:08:31 |
      | 8  | local.mysite.com          | cpe:2.3:a:nginx:nginx:1.1.8:*:*:*:*:*:*:*                           | nginx     | 192.168.0.38  | NULL                                    | local.mysite.com          | NULL              | NULL       | NULL    | 6       | 3       | 2016-06-20 09:09:38 | 2016-06-20 09:09:38 |
    And the following existing Vulnerabilities:
      | id  | id_from_scanner         | name                                                                                                | severity | pci_severity | malware_available | malware_description | impact | cvss_score | created_at          | updated_at          |
      | 103 | ubuntu-cve-2015-8395    | Ubuntu: USN-2943-1 (CVE-2015-8395): PCRE vulnerabilities                                            | 8.00     | 5.00         | NULL              | NULL                | NULL   | 7.50       | 2016-11-14 14:59:55 | 2016-11-14 15:00:17 |
      | 107 | ubuntu-cve-2015-8710    | Ubuntu: USN-2875-1 (CVE-2015-8710): libxml2 vulnerabilities                                         | 8.00     | 5.00         | NULL              | NULL                | NULL   | 7.50       | 2016-11-14 14:59:55 | 2016-11-14 15:00:17 |
      | 566 | 87876                   | MS KB3109853: Update to Improve TLS Session Resumption Interoperability                             | 0.00     | NULL         | NULL              | NULL                | NULL   | NULL       | 2016-11-14 15:00:07 | 2016-11-14 15:00:17 |
      | 340 | windows-hotfix-ms16-037 | MS16-037: Cumulative Security Update for Internet Explorer (3148531)                                | 8.00     | 5.00         | NULL              | NULL                | NULL   | 7.60       | 2016-11-14 14:59:58 | 2016-11-14 15:00:17 |
      | 482 | 84056                   | MS15-060: Vulnerability in Microsoft Common Controls Could Allow Remote Code Execution (3059317)    | 3.00     | NULL         | NULL              | NULL                | NULL   | 9.30       | 2016-11-14 15:00:04 | 2016-11-14 15:00:17 |
      | 27  | gnu-bash-cve-2014-6278  | CVE-2014-6278 bash: code execution via specially crafted environment variables                      | 10.00    | 5.00         | NULL              | NULL                | NULL   | 10.00      | 2016-11-14 14:59:54 | 2016-11-14 15:00:17 |
      | 614 | 77897                   | Ubuntu 10.04 LTS / 12.04 LTS / 14.04 : bash vulnerability (USN-2363-1)                              | 4.00     | NULL         | NULL              | NULL                | NULL   | 10.00      | 2016-11-14 15:00:16 | 2016-11-14 15:00:17 |
      | 361 | OptionsMethodEnabled    | OptionsMethodEnabled                                                                                | 3.90     | NULL         | NULL              | NULL                | NULL   | NULL       | 2016-11-14 15:00:00 | 2016-11-14 15:00:17 |
      | 430 | 81263                   | MS15-010: Vulnerabilities in Windows Kernel-Mode Driver Could Allow Remote Code Execution (3036220) | 3.00     | NULL         | NULL              | NULL                | NULL   | 7.20       | 2016-11-14 15:00:04 | 2016-11-14 15:00:17 |
      | 147 | ubuntu-usn-2348-1       | USN-2348-1: APT vulnerabilities                                                                     | 8.00     | 5.00         | NULL              | NULL                | NULL   | 7.50       | 2016-11-14 14:59:55 | 2016-11-14 15:00:17 |
    And the following existing SoftwareInformation:
      | id  | name                                | version              | vendor             | created_at          | updated_at          |
      | 1   | accountsservice                     | 0.6.35-0ubuntu7      | Ubuntu             | 2016-11-14 14:59:53 | 2016-11-14 15:00:17 |
      | 2   | acpid                               | 1:2.0.21-1ubuntu2    | Ubuntu             | 2016-11-14 14:59:53 | 2016-11-14 15:00:17 |
      | 3   | adduser                             | 3.113+nmu3ubuntu3    | Ubuntu             | 2016-11-14 14:59:53 | 2016-11-14 15:00:17 |
      | 4   | apache2                             | 2.4.7-1ubuntu4.9     | Ubuntu             | 2016-11-14 14:59:53 | 2016-11-14 15:00:17 |
      | 5   | apache2-bin                         | 2.4.7-1ubuntu4.9     | Ubuntu             | 2016-11-14 14:59:53 | 2016-11-14 15:00:17 |
      | 6   | apache2-data                        | 2.4.7-1ubuntu4.9     | Ubuntu             | 2016-11-14 14:59:53 | 2016-11-14 15:00:17 |
      | 7   | apparmor                            | 2.8.95~2430-0ubuntu5 | Ubuntu             | 2016-11-14 14:59:53 | 2016-11-14 15:00:17 |
      | 8   | apport                              | 2.14.1-0ubuntu3      | Ubuntu             | 2016-11-14 14:59:53 | 2016-11-14 15:00:17 |
      | 495 | WinPcap 4.1.3                       | 4.1.0.2980           | CACE Technologies  | 2016-11-14 14:59:54 | 2016-11-14 15:00:17 |
      | 496 | .NET Framework 4.5.1                | 4.5.1                | Microsoft          | 2016-11-14 14:59:54 | 2016-11-14 15:00:17 |
      | 497 | .NET Framework 4.5.1 Client Profile | 4.5.1                | Microsoft          | 2016-11-14 14:59:54 | 2016-11-14 15:00:17 |
      | 498 | Internet Explorer                   | 11.0.9600.17031      | Microsoft          | 2016-11-14 14:59:54 | 2016-11-14 15:00:17 |
      | 499 | MSXML                               | 6.30.9600.16384      | Microsoft          | 2016-11-14 14:59:54 | 2016-11-14 15:00:17 |
      | 500 | MSXML                               | 8.110.9600.16483     | Microsoft          | 2016-11-14 14:59:54 | 2016-11-14 15:00:17 |
      | 501 | Oracle VM VirtualBox                | 4.2.36               | Oracle Corporation | 2016-11-14 14:59:54 | 2016-11-14 15:00:17 |
      | 502 | VMware Tools                        | 9.4.10.2092844       | VMware, Inc.       | 2016-11-14 14:59:54 | 2016-11-14 15:00:17 |
    And the following existing OpenPorts:
      | id | number | protocol | service_name  | service_product       | service_extra_info | service_finger_print | service_banner | service_message | asset_id | created_at          | updated_at          |
      | 1  | 135    | TCP      | MSRPC         | Microsoft Windows RPC | NULL               | NULL                 | NULL           | NULL            | 1        | 2016-11-14 14:59:49 | 2016-11-14 15:00:19 |
      | 2  | 139    | TCP      | NETBIOS-SSN   | NULL                  | NULL               | NULL                 | NULL           | NULL            | 1        | 2016-11-14 14:59:49 | 2016-11-14 15:00:19 |
      | 3  | 445    | TCP      | NETBIOS-SSN   | NULL                  | NULL               | NULL                 | NULL           | NULL            | 1        | 2016-11-14 14:59:49 | 2016-11-14 15:00:19 |
      | 4  | 1025   | TCP      | MSRPC         | Microsoft Windows RPC | NULL               | NULL                 | NULL           | NULL            | 1        | 2016-11-14 14:59:49 | 2016-11-14 15:00:19 |
      | 5  | 1026   | TCP      | MSRPC         | Microsoft Windows RPC | NULL               | NULL                 | NULL           | NULL            | 1        | 2016-11-14 14:59:49 | 2016-11-14 15:00:19 |
      | 6  | 1027   | TCP      | MSRPC         | Microsoft Windows RPC | NULL               | NULL                 | NULL           | NULL            | 1        | 2016-11-14 14:59:49 | 2016-11-14 15:00:19 |
      | 7  | 1028   | TCP      | MSRPC         | Microsoft Windows RPC | NULL               | NULL                 | NULL           | NULL            | 1        | 2016-11-14 14:59:49 | 2016-11-14 15:00:19 |
      | 8  | 1029   | TCP      | MSRPC         | Microsoft Windows RPC | NULL               | NULL                 | NULL           | NULL            | 1        | 2016-11-14 14:59:49 | 2016-11-14 15:00:19 |
      | 9  | 1030   | TCP      | MSRPC         | Microsoft Windows RPC | NULL               | NULL                 | NULL           | NULL            | 1        | 2016-11-14 14:59:49 | 2016-11-14 15:00:19 |
      | 10 | 1031   | TCP      | MSRPC         | Microsoft Windows RPC | NULL               | NULL                 | NULL           | NULL            | 1        | 2016-11-14 14:59:49 | 2016-11-14 15:00:19 |
      | 11 | 3389   | TCP      | MS-WBT-SERVER | NULL                  | NULL               | NULL                 | NULL           | NULL            | 1        | 2016-11-14 14:59:49 | 2016-11-14 15:00:19 |
      | 12 | 22     | TCP      | SSH           | NULL                  | protocol 2.0       | NULL                 | NULL           | NULL            | 7        | 2016-11-14 14:59:49 | 2016-11-14 15:00:19 |
      | 13 | 80     | TCP      | HTTP          | Apache httpd          | NULL               | NULL                 | NULL           | NULL            | 7        | 2016-11-14 14:59:49 | 2016-11-14 15:00:19 |
      | 14 | 3306   | TCP      | MYSQL         | MySQL                 | unauthorized       | NULL                 | NULL           | NULL            | 7        | 2016-11-14 14:59:49 | 2016-11-14 15:00:19 |
      | 15 | 22     | TCP      | SSH           | NULL                  | protocol 2.0       | NULL                 | NULL           | NULL            | 8        | 2016-11-14 14:59:49 | 2016-11-14 15:00:19 |
      | 16 | 80     | TCP      | HTTP          | Apache httpd          | NULL               | NULL                 | NULL           | NULL            | 8        | 2016-11-14 14:59:49 | 2016-11-14 15:00:19 |
      | 17 | 3306   | TCP      | MYSQL         | MySQL                 | unauthorized       | NULL                 | NULL           | NULL            | 8        | 2016-11-14 14:59:49 | 2016-11-14 15:00:19 |
    And the following existing Audits:
      | id  | audit_file                                  | compliance_check_name                                                                                  | compliance_check_id              | actual_value                                                                                                                                                  | policy_value                                                                                                                           | result | agent   | uname                                                                                                    | created_at          | updated_at          |
      | 128 | CIS_Ubuntu_14.04_LTS_Server_L2_v1.0.0.audit | 2.19 Disable Mounting of freevxfs Filesystems - loadeable                                              | a0a282aee09ccc654048629e572f1e98 | The command '/sbin/modprobe -n -v freevxfs' returned : ↵↵insmod /lib/modules/3.13.0-24-generic/kernel/fs/freevxfs/freevxfs.ko                                 | NULL                                                                                                                                   | FAILED | unix    | Linux app-8 3.13.0-24-generic #46-Ubuntu SMP Thu Apr 10 19:11:08 UTC 2014 x86_64 x86_64 x86_64 GNU/Linux | 2016-11-14 15:00:17 | 2016-11-14 15:00:17 |
      | 125 | CIS_Ubuntu_14.04_LTS_Server_L2_v1.0.0.audit | 2.20 Disable Mounting of jffs2 Filesystems - loaded                                                    | 20925b29e471b3e8ddf4c8bf4231702d | ↵The command '/sbin/lsmod                                                                                                                                     | /bin/egrep '^jffs2\s'\|/usr/bin/awk '{ print } END { if (NR==0) print "not loaded" }'' returned : ↵↵not loaded\|↵expect: ^not loaded$↵ | PASSED | unix    | Linux app-8 3.13.0-24-generic #46-Ubuntu SMP Thu Apr 10 19:11:08 UTC 2014 x86_64 x86_64 x86_64 GNU/Linux | 2016-11-14 15:00:17 | 2016-11-14 15:00:17 |
      | 28  | CIS_MS_SERVER_2012_R2_Level_2_v2.1.0.audit  | 18.9.19.1.6 Set 'Turn off printing over HTTP' to 'Enabled'                                             | 190c8af55c2d3bc4adffb5fa0d204d91 | NULL                                                                                                                                                          | NULL                                                                                                                                   | ERROR  | windows | NULL                                                                                                     | 2016-11-14 15:00:06 | 2016-11-14 15:00:17 |
      | 70  | CIS_Ubuntu_14.04_LTS_Server_L2_v1.0.0.audit | 8.1.18 Make the Audit Configuration Immutable                                                          | 67352c5a435797058a033cb2831d4e3f | The command '/usr/bin/strings /etc/audit/audit.rules 2&gt;&amp;1\|/bin/egrep -v '(^$\|^#)'\|/usr/bin/tail -1' returned : ↵↵sh: 1: /usr/bin/strings: not found | NULL                                                                                                                                   | FAILED | unix    | Linux app-8 3.13.0-24-generic #46-Ubuntu SMP Thu Apr 10 19:11:08 UTC 2014 x86_64 x86_64 x86_64 GNU/Linux | 2016-11-14 15:00:17 | 2016-11-14 15:00:17 |
      | 93  | CIS_Ubuntu_14.04_LTS_Server_L2_v1.0.0.audit | 8.1.6 Record Events That Modify the System's Network Environment - /etc/hosts                          | 2938a6a146388e032a894c42c0a35676 | The file "/etc/audit/audit.rules" could not be found                                                                                                          | NULL                                                                                                                                   | FAILED | unix    | Linux app-8 3.13.0-24-generic #46-Ubuntu SMP Thu Apr 10 19:11:08 UTC 2014 x86_64 x86_64 x86_64 GNU/Linux | 2016-11-14 15:00:17 | 2016-11-14 15:00:17 |
      | 17  | CIS_MS_SERVER_2012_R2_Level_2_v2.1.0.audit  | 18.9.31.2 Set 'Restrict Unauthenticated RPC clients' to 'Enabled: Authenticated'                       | 8f9cea1893aac092cdde7d9c94a43018 | NULL                                                                                                                                                          | NULL                                                                                                                                   | ERROR  | windows | NULL                                                                                                     | 2016-11-14 15:00:06 | 2016-11-14 15:00:17 |
      | 3   | CIS_MS_SERVER_2012_R2_Level_2_v2.1.0.audit  | 18.10.65.2 Set 'Prevent Internet Explorer security prompt for Windows Installer scripts' to 'Disabled' | 2bd59f1d03bbd851f9a7a5c4623a7571 | NULL                                                                                                                                                          | NULL                                                                                                                                   | ERROR  | windows | NULL                                                                                                     | 2016-11-14 15:00:05 | 2016-11-14 15:00:17 |
      | 85  | CIS_Ubuntu_14.04_LTS_Server_L2_v1.0.0.audit | 8.1.9 Collect Session Initiation Information - /var/log/btmp                                           | 398ce03b689740c2b46f320f35cb6897 | The file "/etc/audit/audit.rules" could not be found                                                                                                          | NULL                                                                                                                                   | FAILED | unix    | Linux app-8 3.13.0-24-generic #46-Ubuntu SMP Thu Apr 10 19:11:08 UTC 2014 x86_64 x86_64 x86_64 GNU/Linux | 2016-11-14 15:00:17 | 2016-11-14 15:00:17 |
      | 111 | CIS_Ubuntu_14.04_LTS_Server_L2_v1.0.0.audit | 8.1.1.2 Disable System on Audit Log Full - action_mail_acct                                            | 82e7bbe257caad125e0967e066b05314 | The file "/etc/audit/auditd.conf" could not be found                                                                                                          | NULL                                                                                                                                   | FAILED | unix    | Linux app-8 3.13.0-24-generic #46-Ubuntu SMP Thu Apr 10 19:11:08 UTC 2014 x86_64 x86_64 x86_64 GNU/Linux | 2016-11-14 15:00:17 | 2016-11-14 15:00:17 |
      | 72  | CIS_Ubuntu_14.04_LTS_Server_L2_v1.0.0.audit | 8.1.17 Collect Kernel Module Loading and Unloading - /sbin/rmmod                                       | 4df1c4a3e69cbfb4dbcfd339a1d6671e | The file "/etc/audit/audit.rules" could not be found                                                                                                          | NULL                                                                                                                                   | FAILED | unix    | Linux app-8 3.13.0-24-generic #46-Ubuntu SMP Thu Apr 10 19:11:08 UTC 2014 x86_64 x86_64 x86_64 GNU/Linux | 2016-11-14 15:00:17 | 2016-11-14 15:00:17 |
    And the following existing Exploits:
      | id  | title                                                                                                       | url_reference                                                              | skill_level  | created_at          | updated_at          |
      | 14  | Dhclient Bash Environment Variable Injection (Shellshock)                                                   | http://www.metasploit.com/modules/exploit/unix/dhcp/bash_environment       | Novice       | 2016-11-14 14:59:54 | 2016-11-14 15:00:17 |
      | 15  | DHCP Client Bash Environment Variable Code Injection (Shellshock)                                           | http://www.metasploit.com/modules/auxiliary/server/dhclient_bash_env       | Intermediate | 2016-11-14 14:59:54 | 2016-11-14 15:00:17 |
      | 30  | Linux Kernel 3.14-rc1 <= 3.15-rc4 - Raw Mode PTY Local Echo Race Condition Local Privilege Escalation (x64) | http://www.exploit-db.com/exploits/33516                                   | Expert       | 2016-11-14 14:59:55 | 2016-11-14 15:00:17 |
      | 47  | Microsoft Internet Explorer 9/10 - CFormElement Use-After-Free and Memory Corruption PoC (MS14-035)         | http://www.exploit-db.com/exploits/34010                                   | Expert       | 2016-11-14 14:59:56 | 2016-11-14 15:00:17 |
      | 82  | MS15-004 Microsoft Remote Desktop Services Web Proxy IE Sandbox Escape                                      | http://www.metasploit.com/modules/exploit/windows/local/ms15_004_tswbproxy | Intermediate | 2016-11-14 14:59:56 | 2016-11-14 15:00:17 |
      | 99  | Microsoft Windows - Local Privilege Escalation (MS15-051)                                                   | http://www.exploit-db.com/exploits/37049                                   | Expert       | 2016-11-14 14:59:57 | 2016-11-14 15:00:17 |
      | 101 | Windows Kernel - win32k!vSolidFillRect Buffer Overflow (MS15-061)                                           | http://www.exploit-db.com/exploits/38270                                   | Expert       | 2016-11-14 14:59:57 | 2016-11-14 15:00:17 |
      | 132 | Windows 10 Build 10130 - User Mode Font Driver Thread Permissions Privilege Escalation                      | http://www.exploit-db.com/exploits/38198                                   | Expert       | 2016-11-14 14:59:57 | 2016-11-14 15:00:17 |
      | 152 | Windows Kernel win32k!OffsetChildren - Null Pointer Dereference                                             | http://www.exploit-db.com/exploits/39025                                   | Expert       | 2016-11-14 14:59:57 | 2016-11-14 15:00:17 |
      | 161 | Microsoft Windows 7-10 & Server 2008-2012 - Local Privilege Escalation (x32/x64) (MS16-032) (Powershell)    | http://www.exploit-db.com/exploits/39719                                   | Expert       | 2016-11-14 14:59:58 | 2016-11-14 15:00:17 |
    And the following Vulnerabilities in Asset 1:
      | id  | created_at          |
      | 103 | 2016-11-14 14:59:55 |
      | 107 | 2016-11-14 14:59:55 |
      | 566 | 2016-11-14 14:59:55 |
    And the following Vulnerabilities in Asset 2:
      | id  | created_at          |
      | 340 | 2016-11-14 14:59:55 |
      | 482 | 2016-11-14 14:59:55 |
      | 27  | 2016-11-14 14:59:55 |
    And the following Vulnerabilities in Asset 3:
      | id  | created_at          |
      | 614 | 2016-11-14 14:59:55 |
    And the following Vulnerabilities in Asset 7:
      | id  | created_at          |
      | 361 | 2016-11-14 14:59:55 |
      | 430 | 2016-11-14 14:59:55 |
    And the following Vulnerabilities in Asset 8:
      | id  | created_at          |
      | 147 | 2016-11-14 14:59:55 |
    And the following Vulnerabilities in File 1:
      | id  | created_at          |
      | 103 | 2016-11-14 14:59:55 |
      | 107 | 2016-11-14 14:59:55 |
      | 566 | 2016-11-14 14:59:55 |
      | 340 | 2016-11-14 14:59:55 |
      | 482 | 2016-11-14 14:59:55 |
      | 27  | 2016-11-14 14:59:55 |
      | 614 | 2016-11-14 14:59:55 |
    And the following Vulnerabilities in File 5:
      | id  | created_at          |
      | 361 | 2016-11-14 14:59:55 |
      | 430 | 2016-11-14 14:59:55 |
    And the following Vulnerabilities in File 6:
      | id  | created_at          |
      | 147 | 2016-11-14 14:59:55 |
    And the following SoftwareInformation in Asset 1:
      | id  | created_at          |
      | 1   | 2016-11-14 14:59:55 |
      | 2   | 2016-11-14 14:59:55 |
      | 3   | 2016-11-14 14:59:55 |
      | 4   | 2016-11-14 14:59:55 |
      | 5   | 2016-11-14 14:59:55 |
      | 6   | 2016-11-14 14:59:55 |
      | 7   | 2016-11-14 14:59:55 |
      | 8   | 2016-11-14 14:59:55 |
    And the following SoftwareInformation in Asset 7:
      | id  | created_at          |
      | 495 | 2016-11-14 14:59:55 |
      | 496 | 2016-11-14 14:59:55 |
      | 497 | 2016-11-14 14:59:55 |
      | 498 | 2016-11-14 14:59:55 |
      | 499 | 2016-11-14 14:59:55 |
      | 500 | 2016-11-14 14:59:55 |
      | 501 | 2016-11-14 14:59:55 |
      | 502 | 2016-11-14 14:59:55 |
    And the following SoftwareInformation in File 1:
      | id  | created_at          |
      | 1   | 2016-11-14 14:59:55 |
      | 2   | 2016-11-14 14:59:55 |
      | 3   | 2016-11-14 14:59:55 |
      | 4   | 2016-11-14 14:59:55 |
      | 5   | 2016-11-14 14:59:55 |
      | 6   | 2016-11-14 14:59:55 |
      | 7   | 2016-11-14 14:59:55 |
      | 8   | 2016-11-14 14:59:55 |
    And the following SoftwareInformation in File 5:
      | id  | created_at          |
      | 495 | 2016-11-14 14:59:55 |
      | 496 | 2016-11-14 14:59:55 |
      | 497 | 2016-11-14 14:59:55 |
      | 498 | 2016-11-14 14:59:55 |
      | 499 | 2016-11-14 14:59:55 |
      | 500 | 2016-11-14 14:59:55 |
      | 501 | 2016-11-14 14:59:55 |
      | 502 | 2016-11-14 14:59:55 |
    And the following OpenPorts in File 1:
      | id  | created_at          |
      | 1   | 2016-11-14 14:59:55 |
      | 2   | 2016-11-14 14:59:55 |
      | 3   | 2016-11-14 14:59:55 |
      | 4   | 2016-11-14 14:59:55 |
      | 5   | 2016-11-14 14:59:55 |
      | 6   | 2016-11-14 14:59:55 |
      | 7   | 2016-11-14 14:59:55 |
      | 8   | 2016-11-14 14:59:55 |
      | 9   | 2016-11-14 14:59:55 |
      | 10  | 2016-11-14 14:59:55 |
      | 11  | 2016-11-14 14:59:55 |
    And the following OpenPorts in File 5:
      | id | created_at          |
      | 12 | 2016-11-14 14:59:55 |
      | 13 | 2016-11-14 14:59:55 |
      | 14 | 2016-11-14 14:59:55 |
    And the following Audits in Asset 1:
      | id  | created_at          |
      | 128 | 2016-11-14 14:59:55 |
      | 125 | 2016-11-14 14:59:55 |
      | 28  | 2016-11-14 14:59:55 |
      | 70  | 2016-11-14 14:59:55 |
      | 93  | 2016-11-14 14:59:55 |
    And the following Audits in Asset 7:
      | id  | created_at          |
      | 17  | 2016-11-14 14:59:55 |
      | 3   | 2016-11-14 14:59:55 |
      | 85  | 2016-11-14 14:59:55 |
      | 111 | 2016-11-14 14:59:55 |
      | 72  | 2016-11-14 14:59:55 |
    And the following Audits in File 1:
      | id  | created_at          |
      | 28  | 2016-11-14 14:59:55 |
      | 70  | 2016-11-14 14:59:55 |
      | 93  | 2016-11-14 14:59:55 |
      | 125 | 2016-11-14 14:59:55 |
      | 128 | 2016-11-14 14:59:55 |
    And the following Audits in File 5:
      | id  | created_at          |
      | 17  | 2016-11-14 14:59:55 |
      | 3   | 2016-11-14 14:59:55 |
      | 85  | 2016-11-14 14:59:55 |
      | 111 | 2016-11-14 14:59:55 |
      | 72  | 2016-11-14 14:59:55 |
    And the following Exploits in Vulnerability 103:
      | id  | created_at          |
      | 14  | 2016-11-14 14:59:55 |
      | 15  | 2016-11-14 14:59:55 |
      | 30  | 2016-11-14 14:59:55 |
    And the following Exploits in Vulnerability 107:
      | id  | created_at          |
      | 47  | 2016-11-14 14:59:55 |
      | 82  | 2016-11-14 14:59:55 |
      | 99  | 2016-11-14 14:59:55 |
    And the following Exploits in Vulnerability 361:
      | id  | created_at          |
      | 101 | 2016-11-14 14:59:55 |
      | 132 | 2016-11-14 14:59:55 |
      | 152 | 2016-11-14 14:59:55 |
    And the following Exploits in Vulnerability 147:
      | id  | created_at          |
      | 161 | 2016-11-14 14:59:55 |
    And the following existing Components:
      | id | name            | class_name | created_at          | updated_at          |
      | 1  | User Account    | User       | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
      | 2  | Team            | Team       | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
      | 3  | Workspace       | Workspace  | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
      | 4  | Asset           | Asset      | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
      | 5  | Scanner App     | ScannerApp | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
      | 6  | Event           | Event      | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
      | 7  | Rules           | Rule       | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
    And the following existing ComponentPermissions:
      | id | component_id | instance_id | permission | user_id | team_id | granted_by | created_at          | updated_at          |
      | 1  | 1            | 2           | rw         | 1       | NULL    | 2          | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
    And a valid API key "OaLLlZl4XB9wgmSGg7uai1nvtTiDsLpSBCfFoLKv18GCDdiIxxPLslKZmcPN"

  ##
  # Deleting Files
  ##


  ##
  # List Assets detected in a file
  ##
  Scenario: Get a list of Assets found in a particular file in one of my Workspaces
    Given that I want to get information about "Assets"
    When I use a URL parameter "include" with value "assets"
    And I request "/api/file/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response does not have a "error" property
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "1"
    And the response has a "filename" property
    And the type of the "filename" property is string
    And the "filename" property equals "nmap-adv-multiple-node-dns.xml"
    And the response has a "format" property
    And the type of the "format" property is string
    And the "format" property equals "xml"
    And the response has a "size" property
    And the type of the "size" property is integer
    And the "size" property equals "18646"
    And the response has a "scannerId" property
    And the type of the "scannerId" property is integer
    And the "scannerId" property equals "1"
    And the response has a "scannerName" property
    And the type of the "scannerName" property is string
    And the "scannerName" property equals "nmap"
    And the response has a "workspaceId" property
    And the type of the "workspaceId" property is integer
    And the "workspaceId" property equals "1"
    And the response has a "ownerId" property
    And the type of the "ownerId" property is integer
    And the "ownerId" property equals "1"
    And the response has a "isProcessed" property
    And the type of the "isProcessed" property is boolean
    And the "isProcessed" property equals "true"
    And the response has a "isDeleted" property
    And the type of the "isDeleted" property is boolean
    And the "isDeleted" property equals "false"
    And the response has a "assets" property
    And the type of the "assets" property is array
    And the "assets" array property has the following items:
      | id | name                      | cpe                                                                 | ipAddress     | ipAddressV6                             | hostname                  | macAddress        | os        | osVersion | createdDate         | modifiedDate        |
      | 1  | homenetwork.home.co.za    | cpe:/o:ubuntu:ubuntu_linux:9.10                                     | 192.168.0.10  | FE80:0000:0000:0000:0202:B3FF:FE1E:8329 | homenetwork.home.co.za    | D0:E1:40:8C:63:6A | Ubuntu    | 9.10      | 2016-06-20 09:00:00 | 2016-06-20 09:00:00 |
      | 2  | Windows Server 2003       | cpe:2.3:o:microsoft:windows_2003_server:*:gold:enterprise:*:*:*:*:* | 192.168.0.12  | fd03:10d3:bb1c::/48                     | NULL                      | NULL              | Microsoft | 5.2.3790  | 2016-06-20 09:02:23 | 2016-06-20 09:02:23 |
      | 3  | 192.168.0.24              | NULL                                                                | 192.168.0.24  | NULL                                    | NULL                      | NULL              | NULL      | NULL      | 2016-06-20 09:05:31 | 2016-06-20 09:05:31 |

  Scenario: Get a list of Assets found in a particular file in someone else's Workspaces where I have at least read access
    Given that I want to get information about "Assets"
    When I use a URL parameter "include" with value "assets"
    And I request "/api/file/5"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response does not have a "error" property
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "5"
    And the response has a "filename" property
    And the type of the "filename" property is string
    And the "filename" property equals "full-multiple-dns.nessus"
    And the response has a "format" property
    And the type of the "format" property is string
    And the "format" property equals "xml"
    And the response has a "size" property
    And the type of the "size" property is integer
    And the "size" property equals "1841174"
    And the response has a "scannerId" property
    And the type of the "scannerId" property is integer
    And the "scannerId" property equals "5"
    And the response has a "scannerName" property
    And the type of the "scannerName" property is string
    And the "scannerName" property equals "nessus"
    And the response has a "workspaceId" property
    And the type of the "workspaceId" property is integer
    And the "workspaceId" property equals "2"
    And the response has a "ownerId" property
    And the type of the "ownerId" property is integer
    And the "ownerId" property equals "2"
    And the response has a "isProcessed" property
    And the type of the "isProcessed" property is boolean
    And the "isProcessed" property equals "true"
    And the response has a "isDeleted" property
    And the type of the "isDeleted" property is boolean
    And the "isDeleted" property equals "false"
    And the response has a "assets" property
    And the type of the "assets" property is array
    And the "assets" array property has the following items:
      | id | name                      | cpe                                                                 | ipAddress     | ipAddressV6                             | hostname                  | macAddress        | os        | osVersion | createdDate         | modifiedDate        |
      | 7  | 192.168.1.24              | NULL                                                                | 192.168.1.24  | NULL                                    | NULL                      | NULL              | NULL      | NULL      | 2016-06-20 09:08:31 | 2016-06-20 09:08:31 |

  Scenario: Get a list of Assets found in a particular file in someone else's Workspaces where I don't have at least read access
    Given that I want to get information about "Assets"
    When I use a URL parameter "include" with value "assets"
    And I request "/api/file/7"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, you don't have permission to view that file."

  Scenario: Get a list of Assets found on a non-existent file
    Given that I want to get information about "Assets"
    When I use a URL parameter "include" with value "assets"
    And I request "/api/file/17"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, that file does not exist."

  ##
  # Listing all the Vulnerabilities that have been found in a File
  ##
  Scenario: Get a list of Vulnerabilities found in a particular file in one of my Workspaces
    Given that I want to get information about "Vulnerabilities"
    When I use a URL parameter "include" with value "vulnerabilities"
    And I request "/api/file/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response does not have a "error" property
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "1"
    And the response has a "filename" property
    And the type of the "filename" property is string
    And the "filename" property equals "nmap-adv-multiple-node-dns.xml"
    And the response has a "format" property
    And the type of the "format" property is string
    And the "format" property equals "xml"
    And the response has a "size" property
    And the type of the "size" property is integer
    And the "size" property equals "18646"
    And the response has a "scannerId" property
    And the type of the "scannerId" property is integer
    And the "scannerId" property equals "1"
    And the response has a "scannerName" property
    And the type of the "scannerName" property is string
    And the "scannerName" property equals "nmap"
    And the response has a "workspaceId" property
    And the type of the "workspaceId" property is integer
    And the "workspaceId" property equals "1"
    And the response has a "ownerId" property
    And the type of the "ownerId" property is integer
    And the "ownerId" property equals "1"
    And the response has a "isProcessed" property
    And the type of the "isProcessed" property is boolean
    And the "isProcessed" property equals "true"
    And the response has a "isDeleted" property
    And the type of the "isDeleted" property is boolean
    And the "isDeleted" property equals "false"
    And the response has a "vulnerabilities" property
    And the type of the "vulnerabilities" property is array
    And the "vulnerabilities" array property has the following items:
      | id  | sourceId                | name                                                                                             | severity | pciSeverity  | isMalwareAvailable | malwareDescription | impact | cvssScore | createdDate         | modifiedDate        |
      | 27  | gnu-bash-cve-2014-6278  | CVE-2014-6278 bash: code execution via specially crafted environment variables                   | 10.00    | 5.00         | NULL               | NULL               | NULL   | 10.00     | 2016-11-14 14:59:54 | 2016-11-14 15:00:17 |
      | 103 | ubuntu-cve-2015-8395    | Ubuntu: USN-2943-1 (CVE-2015-8395): PCRE vulnerabilities                                         | 8.00     | 5.00         | NULL               | NULL               | NULL   | 7.50      | 2016-11-14 14:59:55 | 2016-11-14 15:00:17 |
      | 107 | ubuntu-cve-2015-8710    | Ubuntu: USN-2875-1 (CVE-2015-8710): libxml2 vulnerabilities                                      | 8.00     | 5.00         | NULL               | NULL               | NULL   | 7.50      | 2016-11-14 14:59:55 | 2016-11-14 15:00:17 |
      | 340 | windows-hotfix-ms16-037 | MS16-037: Cumulative Security Update for Internet Explorer (3148531)                             | 8.00     | 5.00         | NULL               | NULL               | NULL   | 7.60      | 2016-11-14 14:59:58 | 2016-11-14 15:00:17 |
      | 482 | 84056                   | MS15-060: Vulnerability in Microsoft Common Controls Could Allow Remote Code Execution (3059317) | 3.00     | NULL         | NULL               | NULL               | NULL   | 9.30      | 2016-11-14 15:00:04 | 2016-11-14 15:00:17 |
      | 566 | 87876                   | MS KB3109853: Update to Improve TLS Session Resumption Interoperability                          | 0.00     | NULL         | NULL               | NULL               | NULL   | NULL      | 2016-11-14 15:00:07 | 2016-11-14 15:00:17 |
      | 614 | 77897                   | Ubuntu 10.04 LTS / 12.04 LTS / 14.04 : bash vulnerability (USN-2363-1)                           | 4.00     | NULL         | NULL               | NULL               | NULL   | 10.00     | 2016-11-14 15:00:16 | 2016-11-14 15:00:17 |

  Scenario: Get a list of Vulnerabilities found in a particular file in someone else's Workspaces where I have at least read access
    Given that I want to get information about "Vulnerabilities"
    When I use a URL parameter "include" with value "vulnerabilities"
    And I request "/api/file/5"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response does not have a "error" property
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "5"
    And the response has a "filename" property
    And the type of the "filename" property is string
    And the "filename" property equals "full-multiple-dns.nessus"
    And the response has a "format" property
    And the type of the "format" property is string
    And the "format" property equals "xml"
    And the response has a "size" property
    And the type of the "size" property is integer
    And the "size" property equals "1841174"
    And the response has a "scannerId" property
    And the type of the "scannerId" property is integer
    And the "scannerId" property equals "5"
    And the response has a "scannerName" property
    And the type of the "scannerName" property is string
    And the "scannerName" property equals "nessus"
    And the response has a "workspaceId" property
    And the type of the "workspaceId" property is integer
    And the "workspaceId" property equals "2"
    And the response has a "ownerId" property
    And the type of the "ownerId" property is integer
    And the "ownerId" property equals "2"
    And the response has a "isProcessed" property
    And the type of the "isProcessed" property is boolean
    And the "isProcessed" property equals "true"
    And the response has a "isDeleted" property
    And the type of the "isDeleted" property is boolean
    And the "isDeleted" property equals "false"
    And the response has a "vulnerabilities" property
    And the type of the "vulnerabilities" property is array
    And the "vulnerabilities" array property has the following items:
      | id  | sourceId             | name                                                                                                | severity | pciSeverity  | isMalwareAvailable | malwareDescription | impact | cvssScore | createdDate         | modifiedDate        |
      | 361 | OptionsMethodEnabled | OptionsMethodEnabled                                                                                | 3.90     | NULL         | NULL               | NULL               | NULL   | NULL      | 2016-11-14 15:00:00 | 2016-11-14 15:00:17 |
      | 430 | 81263                | MS15-010: Vulnerabilities in Windows Kernel-Mode Driver Could Allow Remote Code Execution (3036220) | 3.00     | NULL         | NULL               | NULL               | NULL   | 7.20      | 2016-11-14 15:00:04 | 2016-11-14 15:00:17 |

  Scenario: Get a list of Vulnerabilities found in a particular file in someone else's Workspaces where I don't have at least read access
    Given that I want to get information about "Vulnerabilities"
    When I use a URL parameter "include" with value "vulnerabilities"
    And I request "/api/file/7"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, you don't have permission to view that file."

  Scenario: Get a list of Vulnerabilities found on a non-existent file
    Given that I want to get information about "Vulnerabilities"
    When I use a URL parameter "include" with value "vulnerabilities"
    And I request "/api/file/17"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, that file does not exist."

  ##
  # Listing all the Software Information that has been found in a File
  ##
  Scenario: Get a list of Software Information found in a particular file in one of my Workspaces
    Given that I want to get information about "SoftwareInformation"
    When I use a URL parameter "include" with value "softwareInformation"
    And I request "/api/file/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response does not have a "error" property
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "1"
    And the response has a "filename" property
    And the type of the "filename" property is string
    And the "filename" property equals "nmap-adv-multiple-node-dns.xml"
    And the response has a "format" property
    And the type of the "format" property is string
    And the "format" property equals "xml"
    And the response has a "size" property
    And the type of the "size" property is integer
    And the "size" property equals "18646"
    And the response has a "scannerId" property
    And the type of the "scannerId" property is integer
    And the "scannerId" property equals "1"
    And the response has a "scannerName" property
    And the type of the "scannerName" property is string
    And the "scannerName" property equals "nmap"
    And the response has a "workspaceId" property
    And the type of the "workspaceId" property is integer
    And the "workspaceId" property equals "1"
    And the response has a "ownerId" property
    And the type of the "ownerId" property is integer
    And the "ownerId" property equals "1"
    And the response has a "isProcessed" property
    And the type of the "isProcessed" property is boolean
    And the "isProcessed" property equals "true"
    And the response has a "isDeleted" property
    And the type of the "isDeleted" property is boolean
    And the "isDeleted" property equals "false"
    And the response has a "softwareInformation" property
    And the type of the "softwareInformation" property is array
    And the "softwareInformation" array property has the following items:
      | id  | name                                | version              | vendor             | createdDate         | modifiedDate        |
      | 1   | accountsservice                     | 0.6.35-0ubuntu7      | Ubuntu             | 2016-11-14 14:59:53 | 2016-11-14 15:00:17 |
      | 2   | acpid                               | 1:2.0.21-1ubuntu2    | Ubuntu             | 2016-11-14 14:59:53 | 2016-11-14 15:00:17 |
      | 3   | adduser                             | 3.113+nmu3ubuntu3    | Ubuntu             | 2016-11-14 14:59:53 | 2016-11-14 15:00:17 |
      | 4   | apache2                             | 2.4.7-1ubuntu4.9     | Ubuntu             | 2016-11-14 14:59:53 | 2016-11-14 15:00:17 |
      | 5   | apache2-bin                         | 2.4.7-1ubuntu4.9     | Ubuntu             | 2016-11-14 14:59:53 | 2016-11-14 15:00:17 |
      | 6   | apache2-data                        | 2.4.7-1ubuntu4.9     | Ubuntu             | 2016-11-14 14:59:53 | 2016-11-14 15:00:17 |
      | 7   | apparmor                            | 2.8.95~2430-0ubuntu5 | Ubuntu             | 2016-11-14 14:59:53 | 2016-11-14 15:00:17 |
      | 8   | apport                              | 2.14.1-0ubuntu3      | Ubuntu             | 2016-11-14 14:59:53 | 2016-11-14 15:00:17 |

  Scenario: Get a list of Software Information found in a particular file in someone else's Workspaces where I have at least read access
    Given that I want to get information about "SoftwareInformation"
    When I use a URL parameter "include" with value "softwareInformation"
    And I request "/api/file/5"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response does not have a "error" property
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "5"
    And the response has a "filename" property
    And the type of the "filename" property is string
    And the "filename" property equals "full-multiple-dns.nessus"
    And the response has a "format" property
    And the type of the "format" property is string
    And the "format" property equals "xml"
    And the response has a "size" property
    And the type of the "size" property is integer
    And the "size" property equals "1841174"
    And the response has a "scannerId" property
    And the type of the "scannerId" property is integer
    And the "scannerId" property equals "5"
    And the response has a "scannerName" property
    And the type of the "scannerName" property is string
    And the "scannerName" property equals "nessus"
    And the response has a "workspaceId" property
    And the type of the "workspaceId" property is integer
    And the "workspaceId" property equals "2"
    And the response has a "ownerId" property
    And the type of the "ownerId" property is integer
    And the "ownerId" property equals "2"
    And the response has a "isProcessed" property
    And the type of the "isProcessed" property is boolean
    And the "isProcessed" property equals "true"
    And the response has a "isDeleted" property
    And the type of the "isDeleted" property is boolean
    And the "isDeleted" property equals "false"
    And the response has a "softwareInformation" property
    And the type of the "softwareInformation" property is array
    And the "softwareInformation" array property has the following items:
      | id  | name                                | version          | vendor             | createdDate         | modifiedDate        |
      | 495 | WinPcap 4.1.3                       | 4.1.0.2980       | CACE Technologies  | 2016-11-14 14:59:54 | 2016-11-14 15:00:17 |
      | 496 | .NET Framework 4.5.1                | 4.5.1            | Microsoft          | 2016-11-14 14:59:54 | 2016-11-14 15:00:17 |
      | 497 | .NET Framework 4.5.1 Client Profile | 4.5.1            | Microsoft          | 2016-11-14 14:59:54 | 2016-11-14 15:00:17 |
      | 498 | Internet Explorer                   | 11.0.9600.17031  | Microsoft          | 2016-11-14 14:59:54 | 2016-11-14 15:00:17 |
      | 499 | MSXML                               | 6.30.9600.16384  | Microsoft          | 2016-11-14 14:59:54 | 2016-11-14 15:00:17 |
      | 500 | MSXML                               | 8.110.9600.16483 | Microsoft          | 2016-11-14 14:59:54 | 2016-11-14 15:00:17 |
      | 501 | Oracle VM VirtualBox                | 4.2.36           | Oracle Corporation | 2016-11-14 14:59:54 | 2016-11-14 15:00:17 |
      | 502 | VMware Tools                        | 9.4.10.2092844   | VMware, Inc.       | 2016-11-14 14:59:54 | 2016-11-14 15:00:17 |

  Scenario: Get a list of Software Information found in a particular file in someone else's Workspaces where I don't have at least read access
    Given that I want to get information about "SoftwareInformation"
    When I use a URL parameter "include" with value "softwareInformation"
    And I request "/api/file/7"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, you don't have permission to view that file."

  Scenario: Get a list of Software Information found on a non-existent file
    Given that I want to get information about "SoftwareInformation"
    When I use a URL parameter "include" with value "softwareInformation"
    And I request "/api/file/17"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, that file does not exist."

  ##
  # Listing all the Open Ports that have been found in a File
  ##
  Scenario: Get a list of Open Ports found in a particular file in one of my Workspaces
    Given that I want to get information about "SoftwareInformation"
    When I use a URL parameter "include" with value "openPorts"
    And I request "/api/file/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response does not have a "error" property
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "1"
    And the response has a "filename" property
    And the type of the "filename" property is string
    And the "filename" property equals "nmap-adv-multiple-node-dns.xml"
    And the response has a "format" property
    And the type of the "format" property is string
    And the "format" property equals "xml"
    And the response has a "size" property
    And the type of the "size" property is integer
    And the "size" property equals "18646"
    And the response has a "scannerId" property
    And the type of the "scannerId" property is integer
    And the "scannerId" property equals "1"
    And the response has a "scannerName" property
    And the type of the "scannerName" property is string
    And the "scannerName" property equals "nmap"
    And the response has a "workspaceId" property
    And the type of the "workspaceId" property is integer
    And the "workspaceId" property equals "1"
    And the response has a "ownerId" property
    And the type of the "ownerId" property is integer
    And the "ownerId" property equals "1"
    And the response has a "isProcessed" property
    And the type of the "isProcessed" property is boolean
    And the "isProcessed" property equals "true"
    And the response has a "isDeleted" property
    And the type of the "isDeleted" property is boolean
    And the "isDeleted" property equals "false"
    And the response has a "openPorts" property
    And the type of the "openPorts" property is array
    And the "openPorts" array property has the following items:
      | id | portNumber | protocol | serviceName   | serviceProduct        | serviceExtraInformation   | serviceFingerprint   | serviceBanner  | serviceMessage  | createdDate         | modifiedDate        |
      | 1  | 135        | TCP      | MSRPC         | Microsoft Windows RPC | NULL                      | NULL                 | NULL           | NULL            | 2016-11-14 14:59:49 | 2016-11-14 15:00:19 |
      | 2  | 139        | TCP      | NETBIOS-SSN   | NULL                  | NULL                      | NULL                 | NULL           | NULL            | 2016-11-14 14:59:49 | 2016-11-14 15:00:19 |
      | 3  | 445        | TCP      | NETBIOS-SSN   | NULL                  | NULL                      | NULL                 | NULL           | NULL            | 2016-11-14 14:59:49 | 2016-11-14 15:00:19 |
      | 4  | 1025       | TCP      | MSRPC         | Microsoft Windows RPC | NULL                      | NULL                 | NULL           | NULL            | 2016-11-14 14:59:49 | 2016-11-14 15:00:19 |
      | 5  | 1026       | TCP      | MSRPC         | Microsoft Windows RPC | NULL                      | NULL                 | NULL           | NULL            | 2016-11-14 14:59:49 | 2016-11-14 15:00:19 |
      | 6  | 1027       | TCP      | MSRPC         | Microsoft Windows RPC | NULL                      | NULL                 | NULL           | NULL            | 2016-11-14 14:59:49 | 2016-11-14 15:00:19 |
      | 7  | 1028       | TCP      | MSRPC         | Microsoft Windows RPC | NULL                      | NULL                 | NULL           | NULL            | 2016-11-14 14:59:49 | 2016-11-14 15:00:19 |
      | 8  | 1029       | TCP      | MSRPC         | Microsoft Windows RPC | NULL                      | NULL                 | NULL           | NULL            | 2016-11-14 14:59:49 | 2016-11-14 15:00:19 |
      | 9  | 1030       | TCP      | MSRPC         | Microsoft Windows RPC | NULL                      | NULL                 | NULL           | NULL            | 2016-11-14 14:59:49 | 2016-11-14 15:00:19 |
      | 10 | 1031       | TCP      | MSRPC         | Microsoft Windows RPC | NULL                      | NULL                 | NULL           | NULL            | 2016-11-14 14:59:49 | 2016-11-14 15:00:19 |
      | 11 | 3389       | TCP      | MS-WBT-SERVER | NULL                  | NULL                      | NULL                 | NULL           | NULL            | 2016-11-14 14:59:49 | 2016-11-14 15:00:19 |

  Scenario: Get a list of Open Ports found in a particular file in someone else's Workspaces where I have at least read access
    Given that I want to get information about "SoftwareInformation"
    When I use a URL parameter "include" with value "openPorts"
    And I request "/api/file/5"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response does not have a "error" property
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "5"
    And the response has a "filename" property
    And the type of the "filename" property is string
    And the "filename" property equals "full-multiple-dns.nessus"
    And the response has a "format" property
    And the type of the "format" property is string
    And the "format" property equals "xml"
    And the response has a "size" property
    And the type of the "size" property is integer
    And the "size" property equals "1841174"
    And the response has a "scannerId" property
    And the type of the "scannerId" property is integer
    And the "scannerId" property equals "5"
    And the response has a "scannerName" property
    And the type of the "scannerName" property is string
    And the "scannerName" property equals "nessus"
    And the response has a "workspaceId" property
    And the type of the "workspaceId" property is integer
    And the "workspaceId" property equals "2"
    And the response has a "ownerId" property
    And the type of the "ownerId" property is integer
    And the "ownerId" property equals "2"
    And the response has a "isProcessed" property
    And the type of the "isProcessed" property is boolean
    And the "isProcessed" property equals "true"
    And the response has a "isDeleted" property
    And the type of the "isDeleted" property is boolean
    And the "isDeleted" property equals "false"
    And the response has a "openPorts" property
    And the type of the "openPorts" property is array
    And the "openPorts" array property has the following items:
      | id | portNumber | protocol | serviceName   | serviceProduct        | serviceExtraInformation   | serviceFingerprint   | serviceBanner  | serviceMessage  | createdDate         | modifiedDate        |
      | 12 | 22         | TCP      | SSH           | NULL                  | protocol 2.0              | NULL                 | NULL           | NULL            | 2016-11-14 14:59:49 | 2016-11-14 15:00:19 |
      | 13 | 80         | TCP      | HTTP          | Apache httpd          | NULL                      | NULL                 | NULL           | NULL            | 2016-11-14 14:59:49 | 2016-11-14 15:00:19 |
      | 14 | 3306       | TCP      | MYSQL         | MySQL                 | unauthorized              | NULL                 | NULL           | NULL            | 2016-11-14 14:59:49 | 2016-11-14 15:00:19 |

  Scenario: Get a list of Open Ports found in a particular file in someone else's Workspaces where I don't have at least read access
    Given that I want to get information about "SoftwareInformation"
    When I use a URL parameter "include" with value "openPorts"
    And I request "/api/file/7"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, you don't have permission to view that file."

  Scenario: Get a list of Open Ports found on a non-existent file
    Given that I want to get information about "SoftwareInformation"
    When I use a URL parameter "include" with value "openPorts"
    And I request "/api/file/17"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, that file does not exist."

  ##
  # Listing all the Audits that have been found in a File
  ##
  Scenario: Get a list of Audits found in a particular file in one of my Workspaces
    Given that I want to get information about "SoftwareInformation"
    When I use a URL parameter "include" with value "audits"
    And I request "/api/file/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response does not have a "error" property
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "1"
    And the response has a "filename" property
    And the type of the "filename" property is string
    And the "filename" property equals "nmap-adv-multiple-node-dns.xml"
    And the response has a "format" property
    And the type of the "format" property is string
    And the "format" property equals "xml"
    And the response has a "size" property
    And the type of the "size" property is integer
    And the "size" property equals "18646"
    And the response has a "scannerId" property
    And the type of the "scannerId" property is integer
    And the "scannerId" property equals "1"
    And the response has a "scannerName" property
    And the type of the "scannerName" property is string
    And the "scannerName" property equals "nmap"
    And the response has a "workspaceId" property
    And the type of the "workspaceId" property is integer
    And the "workspaceId" property equals "1"
    And the response has a "ownerId" property
    And the type of the "ownerId" property is integer
    And the "ownerId" property equals "1"
    And the response has a "isProcessed" property
    And the type of the "isProcessed" property is boolean
    And the "isProcessed" property equals "true"
    And the response has a "isDeleted" property
    And the type of the "isDeleted" property is boolean
    And the "isDeleted" property equals "false"
    And the response has a "audits" property
    And the type of the "audits" property is array
    And the "audits" array property has the following items:
      | id  | auditFile                                   | complianceCheckName                                                                                    | complianceCheckId                | actualValue                                                                                                                                                   | policyValue                                                                                                                            | result | agent   | uname                                                                                                    | createdDate         | modifiedDate        |
      | 28  | CIS_MS_SERVER_2012_R2_Level_2_v2.1.0.audit  | 18.9.19.1.6 Set 'Turn off printing over HTTP' to 'Enabled'                                             | 190c8af55c2d3bc4adffb5fa0d204d91 | NULL                                                                                                                                                          | NULL                                                                                                                                   | ERROR  | windows | NULL                                                                                                     | 2016-11-14 15:00:06 | 2016-11-14 15:00:17 |
      | 70  | CIS_Ubuntu_14.04_LTS_Server_L2_v1.0.0.audit | 8.1.18 Make the Audit Configuration Immutable                                                          | 67352c5a435797058a033cb2831d4e3f | The command '/usr/bin/strings /etc/audit/audit.rules 2&gt;&amp;1\|/bin/egrep -v '(^$\|^#)'\|/usr/bin/tail -1' returned : ↵↵sh: 1: /usr/bin/strings: not found | NULL                                                                                                                                   | FAILED | unix    | Linux app-8 3.13.0-24-generic #46-Ubuntu SMP Thu Apr 10 19:11:08 UTC 2014 x86_64 x86_64 x86_64 GNU/Linux | 2016-11-14 15:00:17 | 2016-11-14 15:00:17 |
      | 93  | CIS_Ubuntu_14.04_LTS_Server_L2_v1.0.0.audit | 8.1.6 Record Events That Modify the System's Network Environment - /etc/hosts                          | 2938a6a146388e032a894c42c0a35676 | The file "/etc/audit/audit.rules" could not be found                                                                                                          | NULL                                                                                                                                   | FAILED | unix    | Linux app-8 3.13.0-24-generic #46-Ubuntu SMP Thu Apr 10 19:11:08 UTC 2014 x86_64 x86_64 x86_64 GNU/Linux | 2016-11-14 15:00:17 | 2016-11-14 15:00:17 |
      | 125 | CIS_Ubuntu_14.04_LTS_Server_L2_v1.0.0.audit | 2.20 Disable Mounting of jffs2 Filesystems - loaded                                                    | 20925b29e471b3e8ddf4c8bf4231702d | ↵The command '/sbin/lsmod                                                                                                                                     | *                                                                                                                                      | PASSED | unix    | Linux app-8 3.13.0-24-generic #46-Ubuntu SMP Thu Apr 10 19:11:08 UTC 2014 x86_64 x86_64 x86_64 GNU/Linux | 2016-11-14 15:00:17 | 2016-11-14 15:00:17 |
      | 128 | CIS_Ubuntu_14.04_LTS_Server_L2_v1.0.0.audit | 2.19 Disable Mounting of freevxfs Filesystems - loadeable                                              | a0a282aee09ccc654048629e572f1e98 | The command '/sbin/modprobe -n -v freevxfs' returned : ↵↵insmod /lib/modules/3.13.0-24-generic/kernel/fs/freevxfs/freevxfs.ko                                 | NULL                                                                                                                                   | FAILED | unix    | Linux app-8 3.13.0-24-generic #46-Ubuntu SMP Thu Apr 10 19:11:08 UTC 2014 x86_64 x86_64 x86_64 GNU/Linux | 2016-11-14 15:00:17 | 2016-11-14 15:00:17 |

  Scenario: Get a list of Audits found in a particular file in someone else's Workspaces where I have at least read access
    Given that I want to get information about "SoftwareInformation"
    When I use a URL parameter "include" with value "audits"
    And I request "/api/file/5"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response does not have a "error" property
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "5"
    And the response has a "filename" property
    And the type of the "filename" property is string
    And the "filename" property equals "full-multiple-dns.nessus"
    And the response has a "format" property
    And the type of the "format" property is string
    And the "format" property equals "xml"
    And the response has a "size" property
    And the type of the "size" property is integer
    And the "size" property equals "1841174"
    And the response has a "scannerId" property
    And the type of the "scannerId" property is integer
    And the "scannerId" property equals "5"
    And the response has a "scannerName" property
    And the type of the "scannerName" property is string
    And the "scannerName" property equals "nessus"
    And the response has a "workspaceId" property
    And the type of the "workspaceId" property is integer
    And the "workspaceId" property equals "2"
    And the response has a "ownerId" property
    And the type of the "ownerId" property is integer
    And the "ownerId" property equals "2"
    And the response has a "isProcessed" property
    And the type of the "isProcessed" property is boolean
    And the "isProcessed" property equals "true"
    And the response has a "isDeleted" property
    And the type of the "isDeleted" property is boolean
    And the "isDeleted" property equals "false"
    And the response has a "audits" property
    And the type of the "audits" property is array
    And the "audits" array property has the following items:
      | id  | auditFile                                   | complianceCheckName                                                                                    | complianceCheckId                | actualValue                                          | policyValue  | result | agent   | uname                                                                                                    | createdDate         | modifiedDate        |
      | 3   | CIS_MS_SERVER_2012_R2_Level_2_v2.1.0.audit  | 18.10.65.2 Set 'Prevent Internet Explorer security prompt for Windows Installer scripts' to 'Disabled' | 2bd59f1d03bbd851f9a7a5c4623a7571 | NULL                                                 | NULL         | ERROR  | windows | NULL                                                                                                     | 2016-11-14 15:00:05 | 2016-11-14 15:00:17 |
      | 17  | CIS_MS_SERVER_2012_R2_Level_2_v2.1.0.audit  | 18.9.31.2 Set 'Restrict Unauthenticated RPC clients' to 'Enabled: Authenticated'                       | 8f9cea1893aac092cdde7d9c94a43018 | NULL                                                 | NULL         | ERROR  | windows | NULL                                                                                                     | 2016-11-14 15:00:06 | 2016-11-14 15:00:17 |
      | 72  | CIS_Ubuntu_14.04_LTS_Server_L2_v1.0.0.audit | 8.1.17 Collect Kernel Module Loading and Unloading - /sbin/rmmod                                       | 4df1c4a3e69cbfb4dbcfd339a1d6671e | The file "/etc/audit/audit.rules" could not be found | NULL         | FAILED | unix    | Linux app-8 3.13.0-24-generic #46-Ubuntu SMP Thu Apr 10 19:11:08 UTC 2014 x86_64 x86_64 x86_64 GNU/Linux | 2016-11-14 15:00:17 | 2016-11-14 15:00:17 |
      | 85  | CIS_Ubuntu_14.04_LTS_Server_L2_v1.0.0.audit | 8.1.9 Collect Session Initiation Information - /var/log/btmp                                           | 398ce03b689740c2b46f320f35cb6897 | The file "/etc/audit/audit.rules" could not be found | NULL         | FAILED | unix    | Linux app-8 3.13.0-24-generic #46-Ubuntu SMP Thu Apr 10 19:11:08 UTC 2014 x86_64 x86_64 x86_64 GNU/Linux | 2016-11-14 15:00:17 | 2016-11-14 15:00:17 |
      | 111 | CIS_Ubuntu_14.04_LTS_Server_L2_v1.0.0.audit | 8.1.1.2 Disable System on Audit Log Full - action_mail_acct                                            | 82e7bbe257caad125e0967e066b05314 | The file "/etc/audit/auditd.conf" could not be found | NULL         | FAILED | unix    | Linux app-8 3.13.0-24-generic #46-Ubuntu SMP Thu Apr 10 19:11:08 UTC 2014 x86_64 x86_64 x86_64 GNU/Linux | 2016-11-14 15:00:17 | 2016-11-14 15:00:17 |

  Scenario: Get a list of Audits found in a particular file in someone else's Workspaces where I don't have at least read access
    Given that I want to get information about "SoftwareInformation"
    When I use a URL parameter "include" with value "audits"
    And I request "/api/file/7"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, you don't have permission to view that file."

  Scenario: Get a list of Audits found on a non-existent file
    Given that I want to get information about "SoftwareInformation"
    When I use a URL parameter "include" with value "audits"
    And I request "/api/file/17"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, that file does not exist."

  ##
  # Listing all the Exploits that have been found in a File
  ##
  Scenario: Get a list of Exploits found in a particular file in one of my Workspaces
    Given that I want to get information about "SoftwareInformation"
    When I use a URL parameter "include" with value "exploits"
    And I request "/api/file/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response does not have a "error" property
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "1"
    And the response has a "filename" property
    And the type of the "filename" property is string
    And the "filename" property equals "nmap-adv-multiple-node-dns.xml"
    And the response has a "format" property
    And the type of the "format" property is string
    And the "format" property equals "xml"
    And the response has a "size" property
    And the type of the "size" property is integer
    And the "size" property equals "18646"
    And the response has a "scannerId" property
    And the type of the "scannerId" property is integer
    And the "scannerId" property equals "1"
    And the response has a "scannerName" property
    And the type of the "scannerName" property is string
    And the "scannerName" property equals "nmap"
    And the response has a "workspaceId" property
    And the type of the "workspaceId" property is integer
    And the "workspaceId" property equals "1"
    And the response has a "ownerId" property
    And the type of the "ownerId" property is integer
    And the "ownerId" property equals "1"
    And the response has a "isProcessed" property
    And the type of the "isProcessed" property is boolean
    And the "isProcessed" property equals "true"
    And the response has a "isDeleted" property
    And the type of the "isDeleted" property is boolean
    And the "isDeleted" property equals "false"
    And the response has a "exploits" property
    And the type of the "exploits" property is array
    And the "exploits" array property has the following items:
      | id  | title                                                                                                       | url                                                                        | skillLevel   | createdDate         | modifiedDate        |
      | 14  | Dhclient Bash Environment Variable Injection (Shellshock)                                                   | http://www.metasploit.com/modules/exploit/unix/dhcp/bash_environment       | Novice       | 2016-11-14 14:59:54 | 2016-11-14 15:00:17 |
      | 15  | DHCP Client Bash Environment Variable Code Injection (Shellshock)                                           | http://www.metasploit.com/modules/auxiliary/server/dhclient_bash_env       | Intermediate | 2016-11-14 14:59:54 | 2016-11-14 15:00:17 |
      | 30  | Linux Kernel 3.14-rc1 <= 3.15-rc4 - Raw Mode PTY Local Echo Race Condition Local Privilege Escalation (x64) | http://www.exploit-db.com/exploits/33516                                   | Expert       | 2016-11-14 14:59:55 | 2016-11-14 15:00:17 |
      | 47  | Microsoft Internet Explorer 9/10 - CFormElement Use-After-Free and Memory Corruption PoC (MS14-035)         | http://www.exploit-db.com/exploits/34010                                   | Expert       | 2016-11-14 14:59:56 | 2016-11-14 15:00:17 |
      | 82  | MS15-004 Microsoft Remote Desktop Services Web Proxy IE Sandbox Escape                                      | http://www.metasploit.com/modules/exploit/windows/local/ms15_004_tswbproxy | Intermediate | 2016-11-14 14:59:56 | 2016-11-14 15:00:17 |
      | 99  | Microsoft Windows - Local Privilege Escalation (MS15-051)                                                   | http://www.exploit-db.com/exploits/37049                                   | Expert       | 2016-11-14 14:59:57 | 2016-11-14 15:00:17 |

  Scenario: Get a list of Exploits found in a particular file in someone else's Workspaces where I have at least read access
    Given that I want to get information about "SoftwareInformation"
    When I use a URL parameter "include" with value "exploits"
    And I request "/api/file/5"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response does not have a "error" property
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "5"
    And the response has a "filename" property
    And the type of the "filename" property is string
    And the "filename" property equals "full-multiple-dns.nessus"
    And the response has a "format" property
    And the type of the "format" property is string
    And the "format" property equals "xml"
    And the response has a "size" property
    And the type of the "size" property is integer
    And the "size" property equals "1841174"
    And the response has a "scannerId" property
    And the type of the "scannerId" property is integer
    And the "scannerId" property equals "5"
    And the response has a "scannerName" property
    And the type of the "scannerName" property is string
    And the "scannerName" property equals "nessus"
    And the response has a "workspaceId" property
    And the type of the "workspaceId" property is integer
    And the "workspaceId" property equals "2"
    And the response has a "ownerId" property
    And the type of the "ownerId" property is integer
    And the "ownerId" property equals "2"
    And the response has a "isProcessed" property
    And the type of the "isProcessed" property is boolean
    And the "isProcessed" property equals "true"
    And the response has a "isDeleted" property
    And the type of the "isDeleted" property is boolean
    And the "isDeleted" property equals "false"
    And the response has a "exploits" property
    And the type of the "exploits" property is array
    And the "exploits" array property has the following items:
      | id  | title                                                                                                       | url                                                                        | skillLevel   | createdDate         | modifiedDate        |
      | 101 | Windows Kernel - win32k!vSolidFillRect Buffer Overflow (MS15-061)                                           | http://www.exploit-db.com/exploits/38270                                   | Expert       | 2016-11-14 14:59:57 | 2016-11-14 15:00:17 |
      | 132 | Windows 10 Build 10130 - User Mode Font Driver Thread Permissions Privilege Escalation                      | http://www.exploit-db.com/exploits/38198                                   | Expert       | 2016-11-14 14:59:57 | 2016-11-14 15:00:17 |
      | 152 | Windows Kernel win32k!OffsetChildren - Null Pointer Dereference                                             | http://www.exploit-db.com/exploits/39025                                   | Expert       | 2016-11-14 14:59:57 | 2016-11-14 15:00:17 |

  Scenario: Get a list of Exploits found in a particular file in someone else's Workspaces where I don't have at least read access
    Given that I want to get information about "SoftwareInformation"
    When I use a URL parameter "include" with value "exploits"
    And I request "/api/file/7"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, you don't have permission to view that file."

  Scenario: Get a list of Exploits found on a non-existent file
    Given that I want to get information about "SoftwareInformation"
    When I use a URL parameter "include" with value "exploits"
    And I request "/api/file/17"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, that file does not exist."