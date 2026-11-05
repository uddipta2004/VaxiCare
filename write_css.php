<?php
$css = file_get_contents('assets/css/style_new.css');
file_put_contents('assets/css/style.css', $css);
echo "Done";
