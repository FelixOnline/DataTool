DataTool
========

Felix Surveys DataTool. **This has been discontinued**.

Installation:

1. Set up HTTP authentication
2. Import survey data as below
3. Create a history table (see the source code)
4. Create a config file as per db.sample.php

Usage:

Take an exported CSV from a survey and upload it via phpmyadmin without headings (leave phpmyadmin default COL_1 heading etc.)

Then, add a map table to map COL_1 etc. headings to the real question name.

See the source code for details.

Authentication:

Use HTTP authentication. The logged in user will be tracked with queries for the history page.
