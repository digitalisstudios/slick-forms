<?php

require_once __DIR__.'/../../../../vendor/autoload.php';

// Manually register the test namespace since composer doesn't auto-load autoload-dev from path repositories
spl_autoload_register(function ($class) {
    $prefix = 'DigitalisStudios\\SlickForms\\Tests\\';
    $base_dir = __DIR__.'/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir.str_replace('\\', '/', $relative_class).'.php';

    if (file_exists($file)) {
        require $file;
    }
});
