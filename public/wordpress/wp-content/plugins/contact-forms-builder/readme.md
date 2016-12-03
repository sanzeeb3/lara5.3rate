HOW TO EXPORT PLUGIN TABLES FROM DATABASE
=============================================

If you're using phpMyAdmin when exporting SQL, choose Custom Export Method. 
Then among the checkbox options, select "Disable foreign key checks".
The exported SQL statement will have the disable and enable foreign key checks at
the beginning and end of the output file respectively.