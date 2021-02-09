# PHP-File-Tracker
Track what all PHP Files are included on a site.

To use this file, you need to make sure it (the `__auto_prepend.php`) is called with every PHP call on the site (or server).

The optimal method is to modify the server or site PHP settings to just auto prepend this script to all PHP scripts called. Alternatively, you just need to make sure you put an `include_once( '/path/to/file/__auto_prepend.php' );` at the top of every web entry point for the site. 

Also, for any files that get called via command line, or via cron jobs, you still need to do the manual `include_once()` for them.

Place the file somewhere on the server, and then also place an empty file that has permissions set that all potential users of PHP files can write to it (ie, the web server user and/or site user, the user a cron runs as, and any user that may log in and run a script via CLI. You will need to define the absolute path to this file on line 6 for `define( '__FILE_LIST_JSON', '/home/project/__file_list.json' );`

For line 5, the `define( '__MY_IP_ADDRESS', 'x.x.x.x' );`, set this to your IP address so that it only runs for when you hit pages. Alternatively set it to false to let it work for all web calls. Any calls from Command Line or CRONs will auto call since there is no `$_SERVER['REMOTE_ADDR']` set. 

For line 7, the `define( '__PER_ENTRY_POINT', true );` set this to true to log all included files per "entry point" file (as well as list how it was called, CLI, Web or both).

When the script runs, It will populate the JSON file with an array of data. (For raw viewing, see the `__view_files.php` file that needs to go in web directory. If you are using this script, I'm assuming you can read in JSON data and process it for your needs.

If Line 7 is set to true, data is stored as:

- `entry`
  - `from` (and array listing `cli` and/or `web`)
  - `files` (sorted list of all files used for it)
- `files` (sorted list of all files used for all calls)

Otherwise data is stored as:

- `entry` (unsorted list of entry points)
- `files` (sorted list of all files used for all calls)

For the detailed list, note that if an entry point is called a second time that ends up including more files, they will get added to the list at that time.

This is all "use at your own risk", If you have suggestions for changes or ways to make it better, let me know!
