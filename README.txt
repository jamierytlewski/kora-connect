***********************************************************************************************
***********************************************************************************************
** Plugin Name: KORA Database Display                                                        **
** Plugin URI: TBD                                                                           **
** Description: Plugin for displaying information from a KORA database.                      **
** Author: MATRIX: The Center for Digital Humanities and Social Sciences (Anthony D'Onofrio) **
** Version: 1.0                                                                              **
** Author URI: TBD                                                                           **
***********************************************************************************************
***********************************************************************************************


Installation & Use
---------------------

1) Complete the options form in the admin settings section, labeled "KORA Database Display". 
   Graphs cannot display properly until all information is filled out.

2) Imbed KORA graph using html with a URL pointing to the kora_execute.php file. Iframes are 
   recommened as they have been tested and are easily modifiable.

3) You may imbed a graph in any area of wordpress that allows php processing using the function
   kordat_getrecords().

4) IMPORTANT! For security purposes, please change the passwords in the files 
   kora_database_admin.php (2 places) and kora_execute.php (1 place). Make sure all three 
   passwords match exactly and are as random as possible. 


Legal
---------------------

1) KORA Database Display and all other likenesses to KORA: The Digital Repository and 
   Publishing Platform are property of Matrix: Center for Digital Humanities & Social Sciences 
   and Michigan State University.

2) This plugin holds the same legal standings as KORA: The Digital Repository and Publishing 
   Platform. This includes, but not limited to, copyrights, licensing, and open-source usage.