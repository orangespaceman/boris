# Enabling Boris with virtual hosts
 
Sometimes it is useful/necessary to set up Apache with multiple 'virtual hosts', so instead of having a root of *http://localhost/ * you can access multiple directories by using *http://dirname/ * and *http://another-dir/ *

Boris can be made to work with virtual hosts, if you have access to the *httpd.conf* file.

If you add the following lines, then you should be able to access boris by typing in any of your virtual host paths, followed by */boris*

    <IfModule alias_module>
    <IfModule mime_module>
       Alias /boris "D:/path/to/boris"
       <Directory "D:/path/to/boris">
           AllowOverride AuthConfig
           Order allow,deny
           Allow from all
       </Directory>
    </IfModule>
    </IfModule>
