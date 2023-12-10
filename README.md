# Env
This class is used for loading and parsing environment variables from a file.

# Install
```bash
composer require krzysztofzylka/env
```

# Load env file
```php
$env = new \Krzysztofzylka\Env\Env('/path/to/env/file');
$env->load();
```

## Example ENV file content
```text
DB_HOST=localhost
DB_NAME=testDB
DB_USER=username
DB_PASS=password
```

# Exceptions
File not found: If the given file path does not exist a File not found exception will be thrown.